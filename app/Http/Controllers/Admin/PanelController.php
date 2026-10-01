<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Panel;
use Illuminate\Http\Request;

class PanelController extends Controller
{
    public function index()
    {
        $panels = Panel::orderBy('manufacturer')->orderBy('model')->get();

        return view('admin.panels.index', compact('panels'));
    }

    public function create()
    {
        return view('admin.panels.form', ['panel' => new Panel()]);
    }

    public function store(Request $request)
    {
        Panel::create($this->validated($request));

        return redirect()->route('admin.panels.index')->with('success', 'Panel je dodan u katalog.');
    }

    public function edit(Panel $panel)
    {
        return view('admin.panels.form', compact('panel'));
    }

    public function update(Request $request, Panel $panel)
    {
        $panel->update($this->validated($request));

        return redirect()->route('admin.panels.index')->with('success', 'Panel je ažuriran.');
    }

    public function destroy(Panel $panel)
    {
        $panel->delete();

        return redirect()->route('admin.panels.index')->with('success', 'Panel je uklonjen iz kataloga.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'manufacturer' => ['required', 'string', 'max:255'],
            'model' => ['required', 'string', 'max:255'],
            'technology' => ['required', 'string', 'max:255'],
            'power_w' => ['required', 'integer', 'min:50', 'max:1000'],
            'efficiency_percent' => ['nullable', 'numeric', 'min:5', 'max:35'],
            'length_mm' => ['required', 'integer', 'min:500', 'max:3000'],
            'width_mm' => ['required', 'integer', 'min:300', 'max:2000'],
            'price_bam' => ['required', 'numeric', 'min:1', 'max:100000'],
            'warranty_years' => ['required', 'integer', 'min:1', 'max:30'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
