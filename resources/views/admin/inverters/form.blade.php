<x-app-layout>
    <x-slot name="header">
        <h2 class="h3 fw-bold mb-0" style="color:#1B512D;">{{ $inverter->exists ? 'Uredi invertor' : 'Novi invertor' }}</h2>
    </x-slot>

    <div class="container py-4">
        <div class="card rounded-4 shadow-sm" style="max-width: 720px;">
            <div class="card-body p-4">
                <form method="POST" action="{{ $inverter->exists ? route('admin.inverters.update', $inverter) : route('admin.inverters.store') }}">
                    @csrf
                    @if($inverter->exists) @method('PUT') @endif

                    <div class="row g-3">
                        <div class="col-md-6">
                            <x-input-label value="Proizvođač" />
                            <x-text-input name="manufacturer" class="mt-1 w-100" value="{{ old('manufacturer', $inverter->manufacturer) }}" required />
                        </div>
                        <div class="col-md-6">
                            <x-input-label value="Model" />
                            <x-text-input name="model" class="mt-1 w-100" value="{{ old('model', $inverter->model) }}" required />
                        </div>
                        <div class="col-md-4">
                            <x-input-label value="Tip" />
                            <select name="type" class="form-select mt-1">
                                @foreach(['string' => 'String', 'hibridni' => 'Hibridni', 'mikroinverter' => 'Mikroinverter'] as $val => $label)
                                    <option value="{{ $val }}" {{ old('type', $inverter->type ?? 'string') === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <x-input-label value="Nazivna AC snaga (kW)" />
                            <x-text-input type="number" step="0.1" name="rated_power_kw" class="mt-1 w-100" value="{{ old('rated_power_kw', $inverter->rated_power_kw) }}" required />
                        </div>
                        <div class="col-md-4">
                            <x-input-label value="Maks. DC snaga panela (kW)" />
                            <x-text-input type="number" step="0.1" name="max_pv_power_kw" class="mt-1 w-100" value="{{ old('max_pv_power_kw', $inverter->max_pv_power_kw) }}" required />
                        </div>
                        <div class="col-md-4">
                            <x-input-label value="Broj faza" />
                            <select name="phases" class="form-select mt-1">
                                <option value="1" {{ old('phases', $inverter->phases ?? 1) == 1 ? 'selected' : '' }}>1</option>
                                <option value="3" {{ old('phases', $inverter->phases ?? 1) == 3 ? 'selected' : '' }}>3</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <x-input-label value="Cijena (BAM)" />
                            <x-text-input type="number" step="0.01" name="price_bam" class="mt-1 w-100" value="{{ old('price_bam', $inverter->price_bam) }}" required />
                        </div>
                        <div class="col-md-4">
                            <x-input-label value="Garancija (god.)" />
                            <x-text-input type="number" name="warranty_years" class="mt-1 w-100" value="{{ old('warranty_years', $inverter->warranty_years ?? 10) }}" required />
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', $inverter->is_active ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Aktivan u katalogu</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button class="btn btn-primary rounded-pill" type="submit">Sačuvaj</button>
                        <a href="{{ route('admin.inverters.index') }}" class="btn btn-outline-secondary rounded-pill">Otkaži</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
