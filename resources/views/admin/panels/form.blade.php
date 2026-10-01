<x-app-layout>
    <x-slot name="header">
        <h2 class="h3 fw-bold mb-0" style="color:#1B512D;">{{ $panel->exists ? 'Uredi panel' : 'Novi panel' }}</h2>
    </x-slot>

    <div class="container py-4">
        <div class="card rounded-4 shadow-sm" style="max-width: 720px;">
            <div class="card-body p-4">
                <form method="POST" action="{{ $panel->exists ? route('admin.panels.update', $panel) : route('admin.panels.store') }}">
                    @csrf
                    @if($panel->exists) @method('PUT') @endif

                    <div class="row g-3">
                        <div class="col-md-6">
                            <x-input-label value="Proizvođač" />
                            <x-text-input name="manufacturer" class="mt-1 w-100" value="{{ old('manufacturer', $panel->manufacturer) }}" required />
                            <x-input-error :messages="$errors->get('manufacturer')" />
                        </div>
                        <div class="col-md-6">
                            <x-input-label value="Model" />
                            <x-text-input name="model" class="mt-1 w-100" value="{{ old('model', $panel->model) }}" required />
                            <x-input-error :messages="$errors->get('model')" />
                        </div>
                        <div class="col-md-6">
                            <x-input-label value="Tehnologija" />
                            <x-text-input name="technology" class="mt-1 w-100" value="{{ old('technology', $panel->technology ?? 'monokristalni') }}" required />
                        </div>
                        <div class="col-md-3">
                            <x-input-label value="Snaga (W)" />
                            <x-text-input type="number" name="power_w" class="mt-1 w-100" value="{{ old('power_w', $panel->power_w) }}" required />
                        </div>
                        <div class="col-md-3">
                            <x-input-label value="Efikasnost (%)" />
                            <x-text-input type="number" step="0.1" name="efficiency_percent" class="mt-1 w-100" value="{{ old('efficiency_percent', $panel->efficiency_percent) }}" />
                        </div>
                        <div class="col-md-3">
                            <x-input-label value="Dužina (mm)" />
                            <x-text-input type="number" name="length_mm" class="mt-1 w-100" value="{{ old('length_mm', $panel->length_mm ?? 1900) }}" required />
                        </div>
                        <div class="col-md-3">
                            <x-input-label value="Širina (mm)" />
                            <x-text-input type="number" name="width_mm" class="mt-1 w-100" value="{{ old('width_mm', $panel->width_mm ?? 1100) }}" required />
                        </div>
                        <div class="col-md-4">
                            <x-input-label value="Cijena (BAM)" />
                            <x-text-input type="number" step="0.01" name="price_bam" class="mt-1 w-100" value="{{ old('price_bam', $panel->price_bam) }}" required />
                        </div>
                        <div class="col-md-4">
                            <x-input-label value="Garancija (god.)" />
                            <x-text-input type="number" name="warranty_years" class="mt-1 w-100" value="{{ old('warranty_years', $panel->warranty_years ?? 12) }}" required />
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', $panel->is_active ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Aktivan u katalogu</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button class="btn btn-primary rounded-pill" type="submit">Sačuvaj</button>
                        <a href="{{ route('admin.panels.index') }}" class="btn btn-outline-secondary rounded-pill">Otkaži</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
