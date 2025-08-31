<div class="card mb-4 shadow-sm">
    <div class="card-header bg-success text-white fw-bold">
        Osobni podaci
    </div>
    <div class="card-body">
        <div class="mb-3">
            <label for="contact_name" class="form-label fw-semibold">Vaše ime i prezime</label>
            <input type="text" class="form-control" id="contact_name" name="contact_name" value="{{ old('contact_name', $quoteRequest->contact_name ?? Auth::user()->name) }}" required>
            @error('contact_name')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="contact_email" class="form-label fw-semibold">Vaš email</label>
            <input type="email" class="form-control" id="contact_email" name="contact_email" value="{{ old('contact_email', $quoteRequest->contact_email ?? Auth::user()->email) }}" required>
            @error('contact_email')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="contact_phone" class="form-label fw-semibold">Vaš telefon (opciono)</label>
            <input type="text" class="form-control" id="contact_phone" name="contact_phone" value="{{ old('contact_phone', $quoteRequest->contact_phone ?? '') }}">
            @error('contact_phone')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="card mb-4 shadow-sm">
    <div class="card-header bg-success text-white fw-bold">
        Lokacija i potrošnja
    </div>
    <div class="card-body">
        <div class="mb-3">
            <label for="address" class="form-label fw-semibold">Adresa</label>
            <input type="text" class="form-control" id="address" name="address" value="{{ old('address', $quoteRequest->address ?? '') }}" required>
            @error('address')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="city" class="form-label fw-semibold">Grad</label>
            <input type="text" class="form-control" id="city" name="city" value="{{ old('city', $quoteRequest->city ?? '') }}" required>
            @error('city')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="country" class="form-label fw-semibold">Država</label>
            <input type="text" class="form-control" id="country" name="country" value="{{ old('country', $quoteRequest->country ?? 'Bosnia and Herzegovina') }}">
            @error('country')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="roof_type" class="form-label fw-semibold">Tip krova (opciono)</label>
            <select class="form-select" id="roof_type" name="roof_type">
                <option value="">Odaberi tip krova</option>
                <option value="kosi" {{ old('roof_type', $quoteRequest->roof_type ?? '') == 'kosi' ? 'selected' : '' }}>Kosi krov</option>
                <option value="ravan" {{ old('roof_type', $quoteRequest->roof_type ?? '') == 'ravan' ? 'selected' : '' }}>Ravan krov</option>
                <option value="drugi" {{ old('roof_type', $quoteRequest->roof_type ?? '') == 'drugi' ? 'selected' : '' }}>Drugi tip</option>
            </select>
            @error('roof_type')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="roof_area_sqm" class="form-label fw-semibold">Površina krova (m²) (opciono)</label>
            <input type="number" step="0.01" class="form-control" id="roof_area_sqm" name="roof_area_sqm" value="{{ old('roof_area_sqm', $quoteRequest->roof_area_sqm ?? '') }}">
            @error('roof_area_sqm')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="avg_monthly_consumption_kwh" class="form-label fw-semibold">Prosječna mjesečna potrošnja energije (kWh)</label>
            <input type="number" step="0.01" class="form-control" id="avg_monthly_consumption_kwh" name="avg_monthly_consumption_kwh" value="{{ old('avg_monthly_consumption_kwh', $quoteRequest->avg_monthly_consumption_kwh ?? '') }}" required>
            @error('avg_monthly_consumption_kwh')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="notes" class="form-label fw-semibold">Dodatne napomene (opciono)</label>
            <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Npr. specifične zahtjeve za instalaciju...">{{ old('notes', $quoteRequest->notes ?? '') }}</textarea>
            @error('notes')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
