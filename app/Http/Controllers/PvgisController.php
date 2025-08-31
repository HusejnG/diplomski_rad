<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Product;

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

        try {
            $response = Http::get('https://re.jrc.ec.europa.eu/api/v5_2/PVcalc', [
                'lat' => $request->latitude,
                'lon' => $request->longitude,
                'peakpower' => $request->peakPower,
                'loss' => $request->systemLoss,
                'outputformat' => 'json',
                'angle' => 0,
                'aspect' => 0,
                'mountingplace' => 'free',
            ]);

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'error' => 'Greška pri pozivu PVGIS API-ja.',
                'details' => $response->body()
            ], $response->status());

        } catch (\Exception $e) {
            \Log::error('PVGIS kalkulacija izuzetak: ' . $e->getMessage());
            return response()->json(['error' => 'Došlo je do greške prilikom obrade zahtjeva na serveru.'], 500);
        }
    }

    public function suggestProducts(Request $request)
    {
        $request->validate([
            'peakPower' => 'required|numeric|min:0.1',
        ]);

        $peakPowerW = $request->peakPower * 1000;

        $products = Product::where('power_w', '>=', $peakPowerW)
            ->limit(10)
            ->get(['id', 'name', 'description', 'price', 'currency', 'power_w', 'image_path']);

        // Pretvori storage putanje u URL
        $products->transform(function($p) {
            $p->image_path = asset('storage/product_images/' . basename($p->image_path));
            return $p;
        });

        return response()->json(['products' => $products]);
    }
}
