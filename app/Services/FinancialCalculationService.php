<?php

namespace App\Services;

/**
 * Proračun finansijske isplativosti solarnog sistema.
 *
 * Model (period posmatranja 25 godina, što odgovara tipičnoj garanciji
 * proizvodnje panela):
 *  - proizvodnja opada za stopu degradacije panela svake godine,
 *  - dio proizvedene energije se direktno troši (samopotrošnja) po punoj
 *    cijeni struje, a preostali višak se predaje u mrežu po nižoj otkupnoj
 *    (izvoznoj) cijeni,
 *  - cijena struje raste po pretpostavljenoj godišnjoj stopi,
 *  - oduzimaju se godišnji troškovi održavanja i jednokratna zamjena
 *    invertora nakon isteka njegove garancije,
 *  - prost period povrata = trenutak kada kumulativni (nediskontovani) tok
 *    prvi put postane pozitivan,
 *  - NPV se računa diskontovanjem godišnjeg neto toka.
 */
class FinancialCalculationService
{
    public const HORIZON_YEARS = 25;
    public const PANEL_DEGRADATION_RATE = 0.005; // 0.5% godišnje
    public const ELECTRICITY_PRICE_ESCALATION = 0.02; // 2% godišnje
    public const EXPORT_PRICE_RATIO = 0.5; // otkupna cijena viška = 50% maloprodajne
    public const ANNUAL_MAINTENANCE_BAM = 120.0; // redovno održavanje/čišćenje/pregled
    public const DISCOUNT_RATE = 0.05; // 5% godišnje, za NPV

    /**
     * @return array{
     *   annual_savings_year1_bam: float,
     *   simple_payback_years: ?float,
     *   npv_25y_bam: float,
     *   self_consumption_percent: float,
     *   cashflow: array<int, array{year:int, production_kwh: float, savings_bam: float, costs_bam: float, net_bam: float, cumulative_bam: float}>
     * }
     */
    public function calculate(
        float $totalInvestmentBam,
        float $annualProductionKwh,
        float $electricityPriceBamKwh,
        float $selfConsumptionPercent,
        int $inverterWarrantyYears,
        float $inverterReplacementCostBam,
    ): array {
        $selfConsumptionShare = min(1, max(0, $selfConsumptionPercent / 100));

        $cashflow = [];
        $cumulative = -$totalInvestmentBam;
        $paybackYear = null;
        $npv = -$totalInvestmentBam;
        $annualSavingsYear1 = null;

        for ($year = 1; $year <= self::HORIZON_YEARS; $year++) {
            $production = $annualProductionKwh * (1 - self::PANEL_DEGRADATION_RATE) ** ($year - 1);
            $priceThisYear = $electricityPriceBamKwh * (1 + self::ELECTRICITY_PRICE_ESCALATION) ** ($year - 1);

            $selfConsumedKwh = $production * $selfConsumptionShare;
            $exportedKwh = $production - $selfConsumedKwh;

            $savings = $selfConsumedKwh * $priceThisYear + $exportedKwh * $priceThisYear * self::EXPORT_PRICE_RATIO;

            $costs = self::ANNUAL_MAINTENANCE_BAM * (1 + self::ELECTRICITY_PRICE_ESCALATION) ** ($year - 1);
            if ($inverterWarrantyYears > 0 && $year === $inverterWarrantyYears) {
                $costs += $inverterReplacementCostBam;
            }

            $net = $savings - $costs;
            $cumulative += $net;
            $npv += $net / (1 + self::DISCOUNT_RATE) ** $year;

            if ($year === 1) {
                $annualSavingsYear1 = round($savings, 2);
            }

            if ($paybackYear === null && $cumulative >= 0) {
                // Linearna interpolacija unutar godine radi preciznijeg perioda povrata.
                $previousCumulative = $cumulative - $net;
                $fraction = $net != 0 ? (-$previousCumulative / $net) : 0;
                $paybackYear = round(($year - 1) + $fraction, 2);
            }

            $cashflow[] = [
                'year' => $year,
                'production_kwh' => round($production, 1),
                'savings_bam' => round($savings, 2),
                'costs_bam' => round($costs, 2),
                'net_bam' => round($net, 2),
                'cumulative_bam' => round($cumulative, 2),
            ];
        }

        return [
            'annual_savings_year1_bam' => $annualSavingsYear1 ?? 0.0,
            'simple_payback_years' => $paybackYear,
            'npv_25y_bam' => round($npv, 2),
            'self_consumption_percent' => round($selfConsumptionShare * 100, 2),
            'cashflow' => $cashflow,
        ];
    }
}
