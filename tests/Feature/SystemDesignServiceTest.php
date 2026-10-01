<?php

namespace Tests\Feature;

use App\Models\Inverter;
use App\Models\Panel;
use App\Services\SystemDesignService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class SystemDesignServiceTest extends TestCase
{
    use RefreshDatabase;

    private function panel(array $attributes = []): Panel
    {
        // 1900 x 1100 mm = 2,09 m² po panelu
        return Panel::factory()->create(array_merge([
            'power_w' => 450, 'efficiency_percent' => 21, 'length_mm' => 1900, 'width_mm' => 1100,
            'price_bam' => 300, 'is_active' => true,
        ], $attributes));
    }

    private function inverter(float $maxPvKw, array $attributes = []): Inverter
    {
        return Inverter::factory()->create(array_merge([
            'rated_power_kw' => $maxPvKw / 1.5, 'max_pv_power_kw' => $maxPvKw, 'phases' => 1,
            'price_bam' => 1000 + $maxPvKw * 100, 'warranty_years' => 10, 'is_active' => true,
        ], $attributes));
    }

    private function design(float $area, float $monthlyKwh, string $surface = 'kosi_krov'): array
    {
        return app(SystemDesignService::class)->design($surface, $area, $monthlyKwh);
    }

    public function test_a_small_roof_limits_the_number_of_panels(): void
    {
        $this->panel();
        $this->inverter(10);

        // kosi krov: 85 % iskoristivo -> 10 m² * 0,85 = 8,5 m² -> 4 panela, iako potrošnja traži više
        $design = $this->design(10, 1000);

        $this->assertSame(4, $design['max_panels_by_area']);
        $this->assertSame(4, $design['panel_quantity']);
        $this->assertGreaterThan(4, $design['panels_by_consumption']);
    }

    public function test_low_consumption_limits_the_system_on_a_large_roof(): void
    {
        $this->panel();
        $this->inverter(10);

        // 200 kWh/mjesec = 2400 kWh/god -> ~2 kWp -> 5 panela od 450 W
        $design = $this->design(200, 200);

        $this->assertSame(5, $design['panel_quantity']);
        $this->assertEqualsWithDelta(2.25, $design['system_power_kwp'], 0.001);
    }

    public function test_the_surface_type_changes_the_usable_area(): void
    {
        $this->panel();
        $this->inverter(10);

        $roof = $this->design(100, 5000, 'kosi_krov');
        $ground = $this->design(100, 5000, 'zemljiste_ravno');

        $this->assertEqualsWithDelta(85.0, $roof['usable_area_sqm'], 0.01);
        $this->assertEqualsWithDelta(45.0, $ground['usable_area_sqm'], 0.01);
        $this->assertSame('building', $roof['mounting_place']);
        $this->assertSame('free', $ground['mounting_place']);
    }

    public function test_a_surface_too_small_for_one_panel_is_rejected(): void
    {
        $this->panel();
        $this->inverter(10);

        $this->expectException(RuntimeException::class);
        $this->design(2, 300); // 1,7 m² iskoristivo < 2,09 m² panela
    }

    public function test_the_smallest_inverter_that_fits_is_chosen(): void
    {
        $this->panel();
        $this->inverter(3);
        $fitting = $this->inverter(6);
        $this->inverter(12);

        $design = $this->design(200, 300); // ~3,15 kWp

        $this->assertTrue($design['inverter']->is($fitting));
        $this->assertSame(1, $design['inverter_quantity']);
    }

    public function test_several_of_the_largest_inverter_are_used_when_none_is_big_enough(): void
    {
        $this->panel();
        $largest = $this->inverter(5);
        $this->inverter(3);

        $design = $this->design(1000, 1500); // ~15 kWp

        $this->assertTrue($design['inverter']->is($largest));
        $this->assertSame((int) ceil($design['system_power_kwp'] / 5), $design['inverter_quantity']);
    }

    public function test_inactive_catalogue_items_are_ignored(): void
    {
        $this->panel(['efficiency_percent' => 23, 'is_active' => false]);
        $active = $this->panel(['efficiency_percent' => 20]);
        $this->inverter(10);

        $this->assertTrue($this->design(40, 300)['panel']->is($active));
    }

    public function test_the_investment_adds_up(): void
    {
        $this->panel(['price_bam' => 300]);
        $this->inverter(10, ['price_bam' => 2000]);

        $design = $this->design(40, 300);

        $this->assertEqualsWithDelta(
            $design['panel_quantity'] * 300 + 2000,
            $design['equipment_cost_bam'],
            0.01,
        );
        $this->assertEqualsWithDelta(
            $design['equipment_cost_bam'] + $design['installation_cost_bam'],
            $design['total_investment_bam'],
            0.01,
        );
    }
}
