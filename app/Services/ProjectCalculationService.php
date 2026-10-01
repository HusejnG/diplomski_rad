<?php

namespace App\Services;

use App\Models\SolarProject;

/**
 * Objedinjuje projektovanje sistema (SystemDesignService), procjenu
 * proizvodnje (PVGIS) i finansijski proračun (FinancialCalculationService)
 * u jedan rezultat koji se prikazuje korisniku i po potrebi snima kao
 * SolarProject.
 */
class ProjectCalculationService
{
    public function __construct(
        private SystemDesignService $designService,
        private FinancialCalculationService $financialService,
    ) {}

    /**
     * @param  array{
     *   surface_type: string, available_area_sqm: float, avg_monthly_consumption_kwh: float,
     *   latitude: float, longitude: float, tilt_deg?: ?float, azimuth_deg?: ?float,
     *   shading?: string, electricity_price_bam_kwh?: float, self_consumption_percent?: float
     * }  $input
     */
    public function run(array $input): array
    {
        $shading = $input['shading'] ?? 'nema';

        $design = $this->designService->design(
            surfaceType: $input['surface_type'],
            availableAreaSqm: (float) $input['available_area_sqm'],
            avgMonthlyConsumptionKwh: (float) $input['avg_monthly_consumption_kwh'],
            tiltDeg: isset($input['tilt_deg']) ? (float) $input['tilt_deg'] : null,
            azimuthDeg: isset($input['azimuth_deg']) ? (float) $input['azimuth_deg'] : null,
            shading: $shading,
        );

        $production = $this->designService->estimateProduction(
            latitude: (float) $input['latitude'],
            longitude: (float) $input['longitude'],
            systemPowerKwp: $design['system_power_kwp'],
            tiltDeg: $design['tilt_deg'],
            azimuthDeg: $design['azimuth_deg'],
            mountingPlace: $design['mounting_place'],
            shading: $shading,
        );

        $electricityPrice = (float) ($input['electricity_price_bam_kwh'] ?? 0.180);
        $selfConsumption = (float) ($input['self_consumption_percent'] ?? $this->estimateSelfConsumption(
            $design['system_power_kwp'],
            (float) $input['avg_monthly_consumption_kwh']
        ));

        $inverter = $design['inverter'];

        $financials = $this->financialService->calculate(
            totalInvestmentBam: $design['total_investment_bam'],
            annualProductionKwh: $production['annual_kwh'],
            electricityPriceBamKwh: $electricityPrice,
            selfConsumptionPercent: $selfConsumption,
            inverterWarrantyYears: $inverter->warranty_years,
            inverterReplacementCostBam: $inverter->price_bam * $design['inverter_quantity'],
        );

        return [
            'design' => $design,
            'production' => $production,
            'financials' => $financials,
            'electricity_price_bam_kwh' => $electricityPrice,
        ];
    }

    /**
     * Gruba procjena udjela samopotrošnje: manji sistem u odnosu na potrošnju
     * znači da se veći dio proizvedene energije odmah troši u domaćinstvu.
     * Ograničeno na raspon 35%-85% radi realističnosti.
     */
    private function estimateSelfConsumption(float $systemPowerKwp, float $avgMonthlyConsumptionKwh): float
    {
        $annualConsumption = $avgMonthlyConsumptionKwh * 12;
        $estimatedAnnualProduction = $systemPowerKwp * 1200;

        if ($estimatedAnnualProduction <= 0) {
            return 65.0;
        }

        $coverageRatio = $annualConsumption / $estimatedAnnualProduction;
        $percent = 40 + min(1, $coverageRatio) * 45; // 40%-85%

        return round(max(35, min(85, $percent)), 1);
    }

    /**
     * Priprema atribute spremne za snimanje u SolarProject na osnovu rezultata run().
     */
    public function toProjectAttributes(array $result): array
    {
        $design = $result['design'];
        $production = $result['production'];
        $financials = $result['financials'];

        return [
            'usable_area_sqm' => $design['usable_area_sqm'],
            'tilt_deg' => $design['tilt_deg'],
            'azimuth_deg' => $design['azimuth_deg'],
            'mounting_place' => $design['mounting_place'],
            'panel_id' => $design['panel']->id,
            'panel_quantity' => $design['panel_quantity'],
            'inverter_id' => $design['inverter']->id,
            'inverter_quantity' => $design['inverter_quantity'],
            'system_power_kwp' => $design['system_power_kwp'],
            'annual_production_kwh' => $production['annual_kwh'],
            'monthly_production' => $production['monthly'],
            'equipment_cost_bam' => $design['equipment_cost_bam'],
            'installation_cost_bam' => $design['installation_cost_bam'],
            'total_investment_bam' => $design['total_investment_bam'],
            'self_consumption_percent' => $financials['self_consumption_percent'],
            'annual_savings_year1_bam' => $financials['annual_savings_year1_bam'],
            'simple_payback_years' => $financials['simple_payback_years'],
            'npv_25y_bam' => $financials['npv_25y_bam'],
            'cashflow_25y' => $financials['cashflow'],
            'electricity_price_bam_kwh' => $result['electricity_price_bam_kwh'],
            'status' => SolarProject::STATUS_CALCULATED,
        ];
    }
}
