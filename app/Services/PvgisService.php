<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Klijent za PVGIS (Photovoltaic Geographical Information System) API
 * Evropske komisije - https://re.jrc.ec.europa.eu/api/v5_2/PVcalc
 *
 * Vraća procijenjenu mjesečnu i godišnju proizvodnju energije za zadatu
 * lokaciju, snagu sistema, nagib, orijentaciju i tip montaže.
 */
class PvgisService
{
    private const BASE_URL = 'https://re.jrc.ec.europa.eu/api/v5_2/PVcalc';

    /**
     * @param  float  $latitude
     * @param  float  $longitude
     * @param  float  $peakPowerKwp  instalisana (vršna) snaga sistema u kWp
     * @param  float  $systemLossPercent  procijenjeni gubici sistema u %
     * @param  float  $tiltDeg  nagib panela u stepenima (0 = horizontalno)
     * @param  float  $azimuthDeg  orijentacija: 0 = jug, -90 = istok, 90 = zapad (PVGIS konvencija)
     * @param  string  $mountingPlace  'building' (na objektu) ili 'free' (samostojeći)
     * @return array{annual_kwh: float, monthly: array<int, array{month:int, e_d: float, e_m: float, h_d: float}>}
     */
    public function calculate(
        float $latitude,
        float $longitude,
        float $peakPowerKwp,
        float $systemLossPercent,
        float $tiltDeg,
        float $azimuthDeg,
        string $mountingPlace = 'free',
    ): array {
        $cacheKey = 'pvgis:'.md5(json_encode([
            round($latitude, 4), round($longitude, 4), round($peakPowerKwp, 3),
            round($systemLossPercent, 1), round($tiltDeg, 1), round($azimuthDeg, 1), $mountingPlace,
        ]));

        return Cache::remember($cacheKey, now()->addDays(14), function () use (
            $latitude, $longitude, $peakPowerKwp, $systemLossPercent, $tiltDeg, $azimuthDeg, $mountingPlace
        ) {
            try {
                $response = Http::timeout(20)->get(self::BASE_URL, [
                    'lat' => $latitude,
                    'lon' => $longitude,
                    'peakpower' => $peakPowerKwp,
                    'loss' => $systemLossPercent,
                    'angle' => $tiltDeg,
                    'aspect' => $azimuthDeg,
                    'mountingplace' => $mountingPlace,
                    'outputformat' => 'json',
                ]);
            } catch (\Throwable $e) {
                Log::error('PVGIS zahtjev nije uspio: '.$e->getMessage());
                throw new RuntimeException('Ne mogu kontaktirati PVGIS servis. Pokušajte ponovo kasnije.');
            }

            if (! $response->successful()) {
                Log::warning('PVGIS API greška: '.$response->body());
                throw new RuntimeException('PVGIS servis je vratio grešku za zadatu lokaciju. Provjerite koordinate (lokacija mora biti u Evropi/Africi/Aziji - PVGIS pokrivenost).');
            }

            $data = $response->json();
            $monthlyRaw = $data['outputs']['monthly']['fixed'] ?? null;

            if (! is_array($monthlyRaw)) {
                throw new RuntimeException('PVGIS nije vratio očekivane podatke za zadatu lokaciju.');
            }

            $monthly = [];
            $annual = 0.0;

            foreach ($monthlyRaw as $row) {
                $eM = (float) ($row['E_m'] ?? 0);
                $monthly[] = [
                    'month' => (int) ($row['month'] ?? 0),
                    'e_d' => (float) ($row['E_d'] ?? 0),
                    'e_m' => $eM,
                    'h_d' => (float) ($row['H(i)_d'] ?? 0),
                ];
                $annual += $eM;
            }

            return [
                'annual_kwh' => round($annual, 2),
                'monthly' => $monthly,
            ];
        });
    }
}
