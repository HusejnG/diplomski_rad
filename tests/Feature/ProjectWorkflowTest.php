<?php

namespace Tests\Feature;

use App\Models\Inverter;
use App\Models\Panel;
use App\Models\SolarProject;
use App\Models\User;
use App\Notifications\ProjectStatusChanged;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

/**
 * Radni tok narudžbe i ovlaštenja: statusi idu samo dozvoljenim redom, a
 * svaka uloga smije samo svoje korake.
 */
class ProjectWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;

    protected function setUp(): void
    {
        parent::setUp();

        Http::fake([
            're.jrc.ec.europa.eu/*' => Http::response([
                'outputs' => ['monthly' => ['fixed' => collect(range(1, 12))->map(fn ($m) => [
                    'month' => $m, 'E_d' => 12.5, 'E_m' => 380.0, 'H(i)_d' => 4.2,
                ])->all()]],
            ], 200),
        ]);

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

        $this->customer = User::factory()->create(['role' => 'customer']);
    }

    private function calculatedProject(): SolarProject
    {
        $this->actingAs($this->customer)->post(route('projects.store'), [
            'latitude' => 43.8563,
            'longitude' => 18.4131,
            'surface_type' => 'kosi_krov',
            'available_area_sqm' => 40,
            'avg_monthly_consumption_kwh' => 350,
            'contact_name' => $this->customer->name,
            'contact_email' => $this->customer->email,
        ]);

        return SolarProject::latest('id')->firstOrFail();
    }

    private function submittedProject(): SolarProject
    {
        $project = $this->calculatedProject();
        $this->actingAs($this->customer)->post(route('projects.submit', $project));

        return $project->fresh();
    }

    private function claimedProject(User $designer): SolarProject
    {
        $project = $this->submittedProject();
        $this->actingAs($designer)->post(route('review.claim', $project));

        return $project->fresh();
    }

    public function test_a_designer_cannot_approve_a_project_nobody_has_claimed(): void
    {
        $designer = User::factory()->create(['role' => 'designer']);
        $project = $this->submittedProject();

        $this->actingAs($designer)->post(route('review.approve', $project))->assertForbidden();

        $this->assertSame(SolarProject::STATUS_SUBMITTED, $project->fresh()->status);
    }

    public function test_a_designer_can_view_an_unclaimed_order_before_claiming_it(): void
    {
        $designer = User::factory()->create(['role' => 'designer']);
        $project = $this->submittedProject();

        $this->actingAs($designer)->get(route('review.show', $project))->assertOk();
        $this->actingAs($designer)->get(route('review.index'))->assertOk()->assertSee($this->customer->name);
    }

    public function test_a_designer_cannot_touch_a_project_claimed_by_another_designer(): void
    {
        $first = User::factory()->create(['role' => 'designer']);
        $second = User::factory()->create(['role' => 'designer']);
        $project = $this->claimedProject($first);

        $this->actingAs($second)->get(route('review.show', $project))->assertForbidden();
        $this->actingAs($second)->post(route('review.approve', $project))->assertForbidden();
        $this->actingAs($second)->post(route('review.claim', $project));

        $project->refresh();
        $this->assertSame($first->id, $project->designer_id);
        $this->assertSame(SolarProject::STATUS_UNDER_REVIEW, $project->status);
    }

    public function test_steps_cannot_be_skipped(): void
    {
        $designer = User::factory()->create(['role' => 'designer']);
        $project = $this->claimedProject($designer);

        // zakazivanje i završavanje prije odobravanja
        $this->actingAs($designer)->post(route('review.schedule', $project), [
            'installation_scheduled_at' => now()->addDays(10)->toDateString(),
        ])->assertSessionHas('error');
        $this->actingAs($designer)->post(route('review.complete', $project))->assertSessionHas('error');

        $project->refresh();
        $this->assertSame(SolarProject::STATUS_UNDER_REVIEW, $project->status);
        $this->assertNull($project->installation_scheduled_at);
        $this->assertNull($project->completed_at);
    }

    public function test_a_rejected_project_stays_rejected(): void
    {
        $designer = User::factory()->create(['role' => 'designer']);
        $project = $this->claimedProject($designer);

        $this->actingAs($designer)->post(route('review.reject', $project), ['reason' => 'Krov nije pogodan.']);
        $this->assertSame(SolarProject::STATUS_REJECTED, $project->fresh()->status);

        $this->actingAs($designer)->post(route('review.approve', $project))->assertSessionHas('error');
        $this->assertSame(SolarProject::STATUS_REJECTED, $project->fresh()->status);
    }

    public function test_the_design_can_only_be_changed_while_the_project_is_under_review(): void
    {
        $designer = User::factory()->create(['role' => 'designer']);
        $project = $this->claimedProject($designer);
        $this->actingAs($designer)->post(route('review.approve', $project));

        $this->actingAs($designer)->put(route('review.updateDesign', $project), [
            'panel_id' => $project->panel_id,
            'panel_quantity' => 2,
            'inverter_id' => $project->inverter_id,
            'inverter_quantity' => 1,
            'electricity_price_bam_kwh' => 0.2,
            'self_consumption_percent' => 60,
            'installation_cost_bam' => 1000,
        ])->assertSessionHas('error');

        $this->assertNotSame(2, $project->fresh()->panel_quantity);
    }

    public function test_only_the_owner_can_change_submit_or_delete_a_customer_project(): void
    {
        $designer = User::factory()->create(['role' => 'designer']);
        $admin = User::factory()->create(['role' => 'admin']);
        $project = $this->calculatedProject();

        foreach ([$designer, $admin] as $user) {
            $this->actingAs($user)->post(route('projects.submit', $project))->assertForbidden();
            $this->actingAs($user)->delete(route('projects.destroy', $project))->assertForbidden();
        }

        $this->assertSame(SolarProject::STATUS_CALCULATED, $project->fresh()->status);
    }

    public function test_an_order_cannot_be_submitted_twice(): void
    {
        $project = $this->submittedProject();
        $submittedAt = $project->submitted_at;

        $this->actingAs($this->customer)->post(route('projects.submit', $project))->assertSessionHas('error');
        $this->assertEquals($submittedAt, $project->fresh()->submitted_at);
    }

    public function test_an_admin_can_move_any_claimed_project_forward(): void
    {
        $designer = User::factory()->create(['role' => 'designer']);
        $admin = User::factory()->create(['role' => 'admin']);
        $project = $this->claimedProject($designer);

        $this->actingAs($admin)->post(route('review.approve', $project));
        $this->assertSame(SolarProject::STATUS_APPROVED, $project->fresh()->status);
    }

    public function test_project_pages_render_for_every_role_that_may_see_them(): void
    {
        $designer = User::factory()->create(['role' => 'designer']);
        $admin = User::factory()->create(['role' => 'admin']);
        $project = $this->claimedProject($designer);

        $this->actingAs($this->customer)->get(route('projects.index'))->assertOk();
        $this->actingAs($this->customer)->get(route('projects.show', $project))->assertOk();
        $this->actingAs($designer)->get(route('review.show', $project))->assertOk();
        $this->actingAs($admin)->get(route('review.show', $project))->assertOk();
        $this->actingAs($admin)->get(route('admin.panels.index'))->assertOk();
    }

    public function test_every_status_change_is_recorded_with_who_made_it(): void
    {
        $designer = User::factory()->create(['role' => 'designer']);
        $project = $this->claimedProject($designer);

        $this->actingAs($designer)->post(route('review.approve', $project));
        $this->actingAs($designer)->post(route('review.schedule', $project), [
            'installation_scheduled_at' => now()->addDays(10)->toDateString(),
        ]);
        $this->actingAs($designer)->post(route('review.complete', $project));

        $history = $project->fresh()->statusChanges;

        $this->assertSame([
            SolarProject::STATUS_CALCULATED,
            SolarProject::STATUS_SUBMITTED,
            SolarProject::STATUS_UNDER_REVIEW,
            SolarProject::STATUS_APPROVED,
            SolarProject::STATUS_SCHEDULED,
            SolarProject::STATUS_COMPLETED,
        ], $history->pluck('to_status')->all());
        $this->assertSame($this->customer->id, $history[1]->user_id);
        $this->assertSame($designer->id, $history[2]->user_id);
        $this->assertSame(SolarProject::STATUS_UNDER_REVIEW, $history[3]->from_status);

        $this->actingAs($this->customer)->get(route('projects.show', $project))
            ->assertOk()
            ->assertSee('Historija statusa')
            ->assertSee('Ugradnja zakazana');
    }

    public function test_the_customer_is_emailed_about_each_step_the_designer_takes(): void
    {
        Notification::fake();
        $designer = User::factory()->create(['role' => 'designer']);
        $project = $this->claimedProject($designer);

        $this->actingAs($designer)->post(route('review.approve', $project));
        $this->actingAs($designer)->post(route('review.schedule', $project), [
            'installation_scheduled_at' => now()->addDays(10)->toDateString(),
        ]);

        // preuzimanje, odobravanje i zakazivanje; snimanje i slanje narudžbe radi sam kupac
        Notification::assertSentToTimes($this->customer, ProjectStatusChanged::class, 3);
        Notification::assertSentTo($this->customer, ProjectStatusChanged::class, function ($notification) use ($project) {
            $mail = $notification->toMail($this->customer);

            return $notification->change->to_status === SolarProject::STATUS_SCHEDULED
                && str_contains(implode(' ', $mail->introLines), $project->fresh()->installation_scheduled_at->format('d.m.Y'));
        });
    }

    public function test_the_rejection_email_includes_the_reason(): void
    {
        Notification::fake();
        $designer = User::factory()->create(['role' => 'designer']);
        $project = $this->claimedProject($designer);

        $this->actingAs($designer)->post(route('review.reject', $project), ['reason' => 'Krov je previše zasjenjen.']);

        Notification::assertSentTo($this->customer, ProjectStatusChanged::class, function ($notification) {
            $mail = $notification->toMail($this->customer);

            return $notification->change->to_status === SolarProject::STATUS_REJECTED
                && in_array('Napomena projektanta: Krov je previše zasjenjen.', $mail->introLines, true);
        });
    }

    public function test_a_blocked_transition_leaves_no_history_and_sends_no_email(): void
    {
        $designer = User::factory()->create(['role' => 'designer']);
        $project = $this->claimedProject($designer);
        $before = $project->statusChanges()->count();

        Notification::fake();
        $this->actingAs($designer)->post(route('review.complete', $project))->assertSessionHas('error');

        $this->assertSame($before, $project->statusChanges()->count());
        Notification::assertNothingSent();
    }
}
