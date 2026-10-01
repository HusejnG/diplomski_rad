<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inverter;
use Illuminate\Http\Request;

class InverterController extends Controller
{
    public function index()
    {
        $inverters = Inverter::orderBy('manufacturer')->orderBy('model')->get();

        return view('admin.inverters.index', compact('inverters'));
    }

    public function create()
    {
        return view('admin.inverters.form', ['inverter' => new Inverter()]);
    }

    public function store(Request $request)
    {
        Inverter::create($this->validated($request));

        return redirect()->route('admin.inverters.index')->with('success', 'Invertor je dodan u katalog.');
    }

    public function edit(Inverter $inverter)
    {
        return view('admin.inverters.form', compact('inverter'));
    }

    public function update(Request $request, Inverter $inverter)
    {
        $inverter->update($this->validated($request));

        return redirect()->route('admin.inverters.index')->with('success', 'Invertor je ažuriran.');
    }

    public function destroy(Inverter $inverter)
    {
        $inverter->delete();

        return redirect()->route('admin.inverters.index')->with('success', 'Invertor je uklonjen iz kataloga.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'manufacturer' => ['required', 'string', 'max:255'],
            'model' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:255'],
            'rated_power_kw' => ['required', 'numeric', 'min:0.5', 'max:1000'],
            'max_pv_power_kw' => ['required', 'numeric', 'min:0.5', 'max:1500'],
            'phases' => ['required', 'integer', 'in:1,3'],
            'price_bam' => ['required', 'numeric', 'min:1', 'max:200000'],
            'warranty_years' => ['required', 'integer', 'min:1', 'max:30'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
