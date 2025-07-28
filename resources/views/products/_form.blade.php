<div class="mb-3">
    <label for="name" class="form-label">Naziv proizvoda</label>
    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $product->name ?? '') }}" required>
    @error('name')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>
<div class="mb-3">
    <label for="type" class="form-label">Tip proizvoda</label>
    <select class="form-select" id="type" name="type">
        <option value="">Odaberi tip</option>
        <option value="panel" {{ old('type', $product->type ?? '') == 'panel' ? 'selected' : '' }}>Solarni Panel</option>
        <option value="inverter" {{ old('type', $product->type ?? '') == 'inverter' ? 'selected' : '' }}>Inverter</option>
        <option value="battery" {{ old('type', $product->type ?? '') == 'battery' ? 'selected' : '' }}>Baterija</option>
        <option value="cable" {{ old('type', $product->type ?? '') == 'cable' ? 'selected' : '' }}>Kabl</option>
        <option value="construction" {{ old('type', $product->type ?? '') == 'construction' ? 'selected' : '' }}>Konstrukcija</option>
        <option value="other" {{ old('type', $product->type ?? '') == 'other' ? 'selected' : '' }}>Ostalo</option>
    </select>
    @error('type')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>
<div class="mb-3">
    <label for="manufacturer" class="form-label">Proizvođač</label>
    <input type="text" class="form-control" id="manufacturer" name="manufacturer" value="{{ old('manufacturer', $product->manufacturer ?? '') }}">
    @error('manufacturer')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>
<div class="mb-3">
    <label for="model" class="form-label">Model</label>
    <input type="text" class="form-control" id="model" name="model" value="{{ old('model', $product->model ?? '') }}">
    @error('model')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>
<div class="mb-3">
    <label for="price" class="form-label">Cijena</label>
    <input type="number" step="0.01" class="form-control" id="price" name="price" value="{{ old('price', $product->price ?? '') }}">
    @error('price')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>
<div class="mb-3">
    <label for="currency" class="form-label">Valuta</label>
    <input type="text" class="form-control" id="currency" name="currency" value="{{ old('currency', $product->currency ?? 'EUR') }}">
    @error('currency')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>
<div class="mb-3">
    <label for="power_w" class="form-label">Snaga (W)</label>
    <input type="number" step="0.01" class="form-control" id="power_w" name="power_w" value="{{ old('power_w', $product->power_w ?? '') }}">
    @error('power_w')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>
<div class="mb-3">
    <label for="length_mm" class="form-label">Dužina (mm)</label>
    <input type="number" step="0.01" class="form-control" id="length_mm" name="length_mm" value="{{ old('length_mm', $product->length_mm ?? '') }}">
    @error('length_mm')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>
<div class="mb-3">
    <label for="width_mm" class="form-label">Širina (mm)</label>
    <input type="number" step="0.01" class="form-control" id="width_mm" name="width_mm" value="{{ old('width_mm', $product->width_mm ?? '') }}">
    @error('width_mm')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>
<div class="mb-3">
    <label for="height_mm" class="form-label">Visina (mm)</label>
    <input type="number" step="0.01" class="form-control" id="height_mm" name="height_mm" value="{{ old('height_mm', $product->height_mm ?? '') }}">
    @error('height_mm')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>
<div class="mb-3">
    <label for="description" class="form-label">Opis</label>
    <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $product->description ?? '') }}</textarea>
    @error('description')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>
<div class="mb-3">
    <label for="image" class="form-label">Slika proizvoda</label>
    <input type="file" class="form-control" id="image" name="image" accept="image/*">
    @error('image')
        <div class="text-danger">{{ $message }}</div>
    @enderror
    @if(isset($product) && $product->image_path)
        <div class="mt-2">
            Trenutna slika: <br>
            <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" style="width: 100px; height: 100px; object-fit: cover;">
        </div>
    @endif
</div>