<?php

namespace Tests\Feature;

use App\Models\Inverter;
use App\Models\Panel;
use App\Models\SolarProject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SolarCalculationTest extends TestCase
{
    use RefreshDatabase;

    private function fakePvgis(): void
    {
        Http::fake([
            're.jrc.ec.europa.eu/*' => Http::response([
                'outputs' => [
                    'monthly' => [
                        'fixed' => collect(range(1, 12))->map(fn ($m) => [
                            'month' => $m,
                            'E_d' => 12.5,
                            'E_m' => 380.0,
                            'H(i)_d' => 4.2,
                        ])->all(),
                    ],
                ],
            ], 200),
        ]);
    }

    private function seedEquipment(): void
    {
        Panel::factory()->create([
            'manufacturer' => 'TestPanel', 'model' => 'TP-450', 'power_w' => 450,
            'efficiency_percent' => 21, 'length_mm' => 1900, 'width_mm' => 1100,
            'price_bam' => 300, 'warranty_years' => 12, 'is_active' => true,
        ]);

        Inverter::factory()->create([
            'manufacturer' => 'TestInv', 'model' => 'TI-5K', 'type' => 'string',
            'rated_power_kw' => 5, 'max_pv_power_kw' => 7.5, 'phases' => 1,
            'price_bam' => 1800, 'warranty_years' => 10, 'is_active' => true,
        ]);
    }

    public function test_public_calculator_returns_a_full_design_and_financial_projection(): void
    {
        $this->fakePvgis();
        $this->seedEquipment();

        $response = $this->postJson(route('calculator.calculate'), [
            'latitude' => 43.8563,
            'longitude' => 18.4131,
            'surface_type' => 'kosi_krov',
            'available_area_sqm' => 40,
            'avg_monthly_consumption_kwh' => 350,
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'system' => ['panel', 'panel_quantity', 'inverter', 'inverter_quantity', 'system_power_kwp', 'total_investment_bam'],
            'production' => ['annual_kwh', 'monthly'],
            'financials' => ['annual_savings_year1_bam', 'simple_payback_years', 'npv_25y_bam', 'cashflow'],
        ]);

        $this->assertGreaterThan(0, $response->json('system.system_power_kwp'));
        $this->assertCount(25, $response->json('financials.cashflow'));
    }

    public function test_calculator_rejects_a_surface_too_small_for_a_single_panel(): void
    {
        $this->fakePvgis();
        $this->seedEquipment();

        $response = $this->postJson(route('calculator.calculate'), [
            'latitude' => 43.8563,
            'longitude' => 18.4131,
            'surface_type' => 'kosi_krov',
            'available_area_sqm' => 2, // ispod min. iz validacije bi trebalo 422, testira granicu
            'avg_monthly_consumption_kwh' => 350,
        ]);

        $response->assertStatus(422);
    }

    public function test_customer_can_save_a_calculation_and_submit_an_order(): void
    {
        $this->fakePvgis();
        $this->seedEquipment();

        $customer = User::factory()->create(['role' => 'customer']);

        $payload = [
            'latitude' => 43.8563,
            'longitude' => 18.4131,
            'surface_type' => 'kosi_krov',
            'available_area_sqm' => 40,
            'avg_monthly_consumption_kwh' => 350,
            'contact_name' => $customer->name,
            'contact_email' => $customer->email,
        ];

        $response = $this->actingAs($customer)->post(route('projects.store'), $payload);

        $project = SolarProject::first();
        $response->assertRedirect(route('projects.show', $project));
        $this->assertSame(SolarProject::STATUS_CALCULATED, $project->status);
        $this->assertSame($customer->id, $project->user_id);
        $this->assertNotNull($project->panel_id);
        $this->assertNotNull($project->total_investment_bam);

        $submitResponse = $this->actingAs($customer)->post(route('projects.submit', $project));
        $submitResponse->assertRedirect(route('projects.show', $project));
        $this->assertSame(SolarProject::STATUS_SUBMITTED, $project->fresh()->status);
    }

    public function test_designer_can_claim_approve_and_schedule_a_submitted_project(): void
    {
        $this->fakePvgis();
        $this->seedEquipment();

        $customer = User::factory()->create(['role' => 'customer']);
        $designer = User::factory()->create(['role' => 'designer']);

        $this->actingAs($customer)->post(route('projects.store'), [
            'latitude' => 43.8563,
            'longitude' => 18.4131,
            'surface_type' => 'kosi_krov',
            'available_area_sqm' => 40,
            'avg_monthly_consumption_kwh' => 350,
            'contact_name' => $customer->name,
            'contact_email' => $customer->email,
        ]);
        $project = SolarProject::first();
        $this->actingAs($customer)->post(route('projects.submit', $project));

        $this->actingAs($designer)->post(route('review.claim', $project));
        $project->refresh();
        $this->assertSame(SolarProject::STATUS_UNDER_REVIEW, $project->status);
        $this->assertSame($designer->id, $project->designer_id);

        $this->actingAs($designer)->post(route('review.approve', $project));
        $this->assertSame(SolarProject::STATUS_APPROVED, $project->fresh()->status);

        $this->actingAs($designer)->post(route('review.schedule', $project), [
            'installation_scheduled_at' => now()->addDays(10)->toDateString(),
        ]);
        $project->refresh();
        $this->assertSame(SolarProject::STATUS_SCHEDULED, $project->status);
        $this->assertNotNull($project->installation_scheduled_at);

        $this->actingAs($designer)->post(route('review.complete', $project));
        $this->assertSame(SolarProject::STATUS_COMPLETED, $project->fresh()->status);
    }

    public function test_customer_cannot_review_someone_elses_project(): void
    {
        $this->fakePvgis();
        $this->seedEquipment();

        $owner = User::factory()->create(['role' => 'customer']);
        $stranger = User::factory()->create(['role' => 'customer']);

        $this->actingAs($owner)->post(route('projects.store'), [
            'latitude' => 43.8563,
            'longitude' => 18.4131,
            'surface_type' => 'kosi_krov',
            'available_area_sqm' => 40,
            'avg_monthly_consumption_kwh' => 350,
            'contact_name' => $owner->name,
            'contact_email' => $owner->email,
        ]);
        $project = SolarProject::first();

        $this->actingAs($stranger)->get(route('projects.show', $project))->assertForbidden();
    }
}
