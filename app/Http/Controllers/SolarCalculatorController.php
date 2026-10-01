<?php

namespace App\Http\Controllers;

use App\Http\Requests\CalculateSolarProjectRequest;
use App\Models\SolarProject;
use App\Services\ProjectCalculationService;
use Illuminate\Http\JsonResponse;

/**
 * Javni kalkulator isplativosti - dostupan bez prijave. Korisnik unosi
 * lokaciju, tip i dimenzije površine te prosječnu potrošnju, a sistem sam
 * projektuje opremu i proračunava proizvodnju i isplativost.
 *
 * Rezultat se ovdje samo prikazuje (ne snima); za snimanje i slanje
 * narudžbe potrebna je prijava (vidi SolarProjectController).
 */
class SolarCalculatorController extends Controller
{
    public function index()
    {
        return view('calculator.index', [
            'surfaceTypes' => SolarProject::SURFACE_TYPES,
        ]);
    }

    public function calculate(CalculateSolarProjectRequest $request, ProjectCalculationService $calculationService): JsonResponse
    {
        try {
            $result = $calculationService->run($request->validated());
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        $design = $result['design'];

        return response()->json([
            'system' => [
                'panel' => $design['panel']->only(['id', 'manufacturer', 'model', 'power_w', 'price_bam']),
                'panel_quantity' => $design['panel_quantity'],
                'inverter' => $design['inverter']->only(['id', 'manufacturer', 'model', 'rated_power_kw', 'price_bam']),
                'inverter_quantity' => $design['inverter_quantity'],
                'system_power_kwp' => $design['system_power_kwp'],
                'usable_area_sqm' => $design['usable_area_sqm'],
                'tilt_deg' => $design['tilt_deg'],
                'azimuth_deg' => $design['azimuth_deg'],
                'equipment_cost_bam' => $design['equipment_cost_bam'],
                'installation_cost_bam' => $design['installation_cost_bam'],
                'total_investment_bam' => $design['total_investment_bam'],
            ],
            'production' => $result['production'],
            'financials' => $result['financials'],
            'electricity_price_bam_kwh' => $result['electricity_price_bam_kwh'],
        ]);
    }
}
