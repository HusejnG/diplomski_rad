<?php

namespace App\Services;

use App\Models\Inverter;
use App\Models\Panel;
use App\Models\SolarProject;
use RuntimeException;

/**
 * Automatsko projektovanje fotonaponskog sistema.
 *
 * Na osnovu raspoložive površine (i njenog tipa), procijenjene potrošnje
 * korisnika i internog kataloga opreme, bira preporučeni panel, broj panela
 * i odgovarajući invertor tako da sistem što bolje odgovara i prostoru i
 * potrebama potrošnje, bez nepotrebnog predimenzionisanja.
 */
class SystemDesignService
{
    // Okvirna prosječna specifična proizvodnja u BiH (kWh proizvedeno godišnje po 1 kWp
    // instalisane snage), korištena SAMO kao prva procjena prije nego PVGIS vrati tačan
    // podatak za konkretnu lokaciju, nagib i orijentaciju.
    private const ESTIMATED_SPECIFIC_YIELD_KWH_PER_KWP = 1200;

    // Standardna cijena ugradnje (radovi, kablovi, konstrukcija, projektovanje) po kWp.
    private const INSTALLATION_COST_BAM_PER_KWP = 900;

    public function __construct(private PvgisService $pvgis) {}

    /**
     * @return array{
     *   panel: Panel, panel_quantity: int, inverter: Inverter, inverter_quantity: int,
     *   system_power_kwp: float, usable_area_sqm: float, tilt_deg: float, azimuth_deg: float,
     *   mounting_place: string, equipment_cost_bam: float, installation_cost_bam: float,
     *   total_investment_bam: float
     * }
     */
    public function design(
        string $surfaceType,
        float $availableAreaSqm,
        float $avgMonthlyConsumptionKwh,
        ?float $tiltDeg = null,
        ?float $azimuthDeg = null,
        string $shading = 'nema',
    ): array {
        $utilisation = SolarProject::AREA_UTILISATION[$surfaceType] ?? 0.5;
        $usableArea = round($availableAreaSqm * $utilisation, 2);

        $tiltDeg ??= SolarProject::DEFAULT_TILT[$surfaceType] ?? 30;
        $azimuthDeg ??= 0;
        $mountingPlace = SolarProject::MOUNTING_PLACE[$surfaceType] ?? 'free';

        $panel = Panel::active()->orderByDesc('efficiency_percent')->first();

        if (! $panel) {
            throw new RuntimeException('Katalog panela je prazan - dodajte barem jedan panel.');
        }

        $panelAreaSqm = $panel->area_sqm;
        $maxPanelsByArea = (int) floor($usableArea / $panelAreaSqm);

        if ($maxPanelsByArea < 1) {
            throw new RuntimeException('Raspoloživa površina je premala za ugradnju panela. Provjerite unesenu površinu.');
        }

        $annualConsumptionKwh = $avgMonthlyConsumptionKwh * 12;
        $targetKwp = $annualConsumptionKwh / self::ESTIMATED_SPECIFIC_YIELD_KWH_PER_KWP;
        $panelsByConsumption = max(1, (int) ceil(($targetKwp * 1000) / $panel->power_w));

        $panelQuantity = min($maxPanelsByArea, $panelsByConsumption);
        $panelQuantity = max(1, $panelQuantity);

        $systemPowerKwp = round(($panelQuantity * $panel->power_w) / 1000, 3);

        [$inverter, $inverterQuantity] = $this->pickInverter($systemPowerKwp);

        $equipmentCost = round($panelQuantity * $panel->price_bam + $inverterQuantity * $inverter->price_bam, 2);
        $installationCost = round($systemPowerKwp * self::INSTALLATION_COST_BAM_PER_KWP, 2);

        return [
            'panel' => $panel,
            'panel_quantity' => $panelQuantity,
            'inverter' => $inverter,
            'inverter_quantity' => $inverterQuantity,
            'system_power_kwp' => $systemPowerKwp,
            'usable_area_sqm' => $usableArea,
            'tilt_deg' => $tiltDeg,
            'azimuth_deg' => $azimuthDeg,
            'mounting_place' => $mountingPlace,
            'equipment_cost_bam' => $equipmentCost,
            'installation_cost_bam' => $installationCost,
            'total_investment_bam' => round($equipmentCost + $installationCost, 2),
            'max_panels_by_area' => $maxPanelsByArea,
            'panels_by_consumption' => $panelsByConsumption,
        ];
    }

    /**
     * Bira najmanji invertor čija je maksimalna DC snaga dovoljna za cijeli sistem.
     * Ako nijedan pojedinačni invertor nije dovoljno velik, koristi se najveći
     * dostupan u više komada.
     *
     * @return array{0: Inverter, 1: int}
     */
    private function pickInverter(float $systemPowerKwp): array
    {
        $inverter = Inverter::active()
            ->where('max_pv_power_kw', '>=', $systemPowerKwp)
            ->orderBy('max_pv_power_kw')
            ->first();

        if ($inverter) {
            return [$inverter, 1];
        }

        $largest = Inverter::active()->orderByDesc('max_pv_power_kw')->first();

        if (! $largest) {
            throw new RuntimeException('Katalog invertora je prazan - dodajte barem jedan invertor.');
        }

        $quantity = max(1, (int) ceil($systemPowerKwp / $largest->max_pv_power_kw));

        return [$largest, $quantity];
    }

    /**
     * Pokreće PVGIS proračun za dizajnirani sistem i primjenjuje gubitak zbog
     * sjenčanja koji PVGIS sam po sebi ne poznaje.
     *
     * @return array{annual_kwh: float, monthly: array}
     */
    public function estimateProduction(
        float $latitude,
        float $longitude,
        float $systemPowerKwp,
        float $tiltDeg,
        float $azimuthDeg,
        string $mountingPlace,
        string $shading = 'nema',
    ): array {
        $result = $this->pvgis->calculate(
            latitude: $latitude,
            longitude: $longitude,
            peakPowerKwp: $systemPowerKwp,
            systemLossPercent: 14,
            tiltDeg: $tiltDeg,
            azimuthDeg: $azimuthDeg,
            mountingPlace: $mountingPlace,
        );

        $shadingLoss = SolarProject::SHADING_LOSS[$shading] ?? 0.0;

        if ($shadingLoss > 0) {
            $result['annual_kwh'] = round($result['annual_kwh'] * (1 - $shadingLoss), 2);
            foreach ($result['monthly'] as &$row) {
                $row['e_m'] = round($row['e_m'] * (1 - $shadingLoss), 2);
                $row['e_d'] = round($row['e_d'] * (1 - $shadingLoss), 2);
            }
            unset($row);
        }

        return $result;
    }
}
