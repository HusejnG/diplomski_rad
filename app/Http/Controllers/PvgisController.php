<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PvgisController extends Controller
{
    public function index()
    {
        return view('pvgis.index');
    }

    public function calculate(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'peakPower' => 'required|numeric|min:0.1',
            'systemLoss' => 'required|numeric|between:0,100',
        ]);

        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        $peakPower = $request->input('peakPower');
        $systemLoss = $request->input('systemLoss');

        try {
            $apiUrl = "https://re.jrc.ec.europa.eu/api/v5_2/PVcalc";
            $response = Http::get($apiUrl, [
                'lat' => $latitude,
                'lon' => $longitude,
                'peakpower' => $peakPower,
                'loss' => $systemLoss,
                'outputformat' => 'json',
                'angle' => 0, 
                'aspect' => 0, 
                'mountingplace' => 'free', 
                
            ]);

            if ($response->successful()) {
                return response()->json($response->json());
            } else {
                \Log::error('PVGIS API error: ' . $response->status() . ' - ' . $response->body());
                return response()->json([
                    'error' => 'Greška pri pozivu PVGIS API-ja.',
                    'details' => $response->json()
                ], $response->status());
            }

        } catch (\Exception $e) {
            \Log::error('PVGIS kalkulacija izuzetak: ' . $e->getMessage());
            return response()->json(['error' => 'Došlo je do greške prilikom obrade zahtjeva na serveru.'], 500);
        }
    }
}