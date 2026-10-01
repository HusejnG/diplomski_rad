<?php

namespace Tests\Unit;

use App\Services\FinancialCalculationService;
use PHPUnit\Framework\TestCase;

class FinancialCalculationServiceTest extends TestCase
{
    private function calculate(array $overrides = []): array
    {
        $args = array_merge([
            'totalInvestmentBam' => 10000.0,
            'annualProductionKwh' => 6000.0,
            'electricityPriceBamKwh' => 0.20,
            'selfConsumptionPercent' => 60.0,
            'inverterWarrantyYears' => 10,
            'inverterReplacementCostBam' => 1500.0,
        ], $overrides);

        return (new FinancialCalculationService)->calculate(...$args);
    }

    public function test_it_projects_25_years(): void
    {
        $result = $this->calculate();

        $this->assertCount(FinancialCalculationService::HORIZON_YEARS, $result['cashflow']);
        $this->assertSame(1, $result['cashflow'][0]['year']);
        $this->assertSame(25, $result['cashflow'][24]['year']);
    }

    public function test_first_year_savings_value_self_consumed_energy_fully_and_exported_energy_at_the_export_price(): void
    {
        $result = $this->calculate(['selfConsumptionPercent' => 60.0]);

        // 60 % po 0,20 BAM + 40 % po pola cijene: 6000 * (0,6 * 0,20 + 0,4 * 0,10) = 960
        $this->assertEqualsWithDelta(960.0, $result['annual_savings_year1_bam'], 0.01);

        $allSelfConsumed = $this->calculate(['selfConsumptionPercent' => 100.0]);
        $this->assertEqualsWithDelta(1200.0, $allSelfConsumed['annual_savings_year1_bam'], 0.01);
    }

    public function test_production_degrades_every_year(): void
    {
        $cashflow = $this->calculate()['cashflow'];

        $this->assertEqualsWithDelta(6000.0, $cashflow[0]['production_kwh'], 0.1);
        $this->assertEqualsWithDelta(6000.0 * (1 - FinancialCalculationService::PANEL_DEGRADATION_RATE), $cashflow[1]['production_kwh'], 0.1);
        $this->assertLessThan($cashflow[0]['production_kwh'], $cashflow[24]['production_kwh']);
    }

    public function test_the_inverter_is_replaced_once_when_its_warranty_ends(): void
    {
        $cashflow = $this->calculate(['inverterWarrantyYears' => 10, 'inverterReplacementCostBam' => 1500.0])['cashflow'];

        $this->assertGreaterThan($cashflow[8]['costs_bam'] + 1400, $cashflow[9]['costs_bam']);  // godina 10
        $this->assertLessThan($cashflow[9]['costs_bam'] - 1400, $cashflow[10]['costs_bam']);    // godina 11
    }

    public function test_the_payback_period_lies_where_the_cumulative_cash_flow_turns_positive(): void
    {
        $result = $this->calculate();
        $payback = $result['simple_payback_years'];
        $cashflow = $result['cashflow'];

        $this->assertNotNull($payback);
        $yearOfPayback = (int) ceil($payback);

        $this->assertGreaterThanOrEqual(0, $cashflow[$yearOfPayback - 1]['cumulative_bam']);
        if ($yearOfPayback > 1) {
            $this->assertLessThan(0, $cashflow[$yearOfPayback - 2]['cumulative_bam']);
        }
    }

    public function test_a_system_that_never_pays_back_has_no_payback_period(): void
    {
        $result = $this->calculate(['annualProductionKwh' => 100.0]);

        $this->assertNull($result['simple_payback_years']);
        $this->assertLessThan(0, $result['npv_25y_bam']);
    }

    public function test_npv_is_lower_than_the_undiscounted_result(): void
    {
        $result = $this->calculate();

        $this->assertLessThan(end($result['cashflow'])['cumulative_bam'], $result['npv_25y_bam']);
    }

    public function test_self_consumption_is_clamped_to_0_to_100_percent(): void
    {
        $this->assertEquals(100.0, $this->calculate(['selfConsumptionPercent' => 150.0])['self_consumption_percent']);
        $this->assertEquals(0.0, $this->calculate(['selfConsumptionPercent' => -10.0])['self_consumption_percent']);
    }
}
