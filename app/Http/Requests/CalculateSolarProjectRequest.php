<?php

namespace App\Http\Requests;

use App\Models\SolarProject;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CalculateSolarProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'surface_type' => ['required', Rule::in(array_keys(SolarProject::SURFACE_TYPES))],
            'available_area_sqm' => ['required', 'numeric', 'min:2', 'max:100000'],
            'tilt_deg' => ['nullable', 'numeric', 'between:0,90'],
            'azimuth_deg' => ['nullable', 'numeric', 'between:-180,180'],
            'shading' => ['nullable', Rule::in(array_keys(SolarProject::SHADING_LOSS))],
            'avg_monthly_consumption_kwh' => ['required', 'numeric', 'min:1', 'max:100000'],
            'electricity_price_bam_kwh' => ['nullable', 'numeric', 'min:0.01', 'max:2'],
            // Udio proizvodnje koji se odmah troši u domaćinstvu. Ako ga
            // korisnik ne unese, procjenjuje ga ProjectCalculationService.
            'self_consumption_percent' => ['nullable', 'numeric', 'between:0,100'],
        ];
    }

    public function messages(): array
    {
        return [
            'available_area_sqm.min' => 'Raspoloživa površina je premala za smislen proračun (min. 2 m²).',
        ];
    }
}
