<div class="mb-3">
    <label for="title" class="form-label">Naslov ponude</label>
    <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $proposal->title ?? 'Ponuda za solarni sistem') }}" required>
    @error('title')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>
<div class="mb-3">
    <label for="description" class="form-label">Opis ponude (opciono)</label>
    <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $proposal->description ?? '') }}</textarea>
    @error('description')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>

@if(isset($proposal)) 
<div class="mb-3">
    <label for="status" class="form-label">Status ponude</label>
    <select class="form-select" id="status" name="status">
        <option value="draft" {{ old('status', $proposal->status ?? '') == 'draft' ? 'selected' : '' }}>Nacrt</option>
        <option value="sent" {{ old('status', $proposal->status ?? '') == 'sent' ? 'selected' : '' }}>Poslano</option>
        <option value="accepted" {{ old('status', $proposal->status ?? '') == 'accepted' ? 'selected' : '' }}>Prihvaćeno</option>
        <option value="rejected" {{ old('status', $proposal->status ?? '') == 'rejected' ? 'selected' : '' }}>Odbijeno</option>
    </select>
    @error('status')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>
@endif

<h4 class="mt-4">Odaberi proizvode:</h4>
<div id="product-selection-container">
    @if(isset($selectedProducts) && !empty($selectedProducts))
        @foreach($selectedProducts as $productId => $quantity)
            @php
                $product = $products->firstWhere('id', $productId);
            @endphp
            @if($product)
                <div class="row mb-3 product-row">
                    <div class="col-md-6">
                        <label for="product_id_{{ $productId }}" class="form-label">Proizvod</label>
                        <select class="form-select product-select" name="product_ids[]" id="product_id_{{ $productId }}">
                            @foreach($products as $p)
                                <option value="{{ $p->id }}" data-price="{{ $p->price }}" {{ $p->id == $productId ? 'selected' : '' }}>
                                    {{ $p->name }} ({{ $p->power_w }}W, {{ $p->price }} {{ $p->currency }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="quantity_{{ $productId }}" class="form-label">Količina</label>
                        <input type="number" class="form-control product-quantity" name="quantities[]" id="quantity_{{ $productId }}" value="{{ old('quantities.' . $loop->index, $quantity) }}" min="1">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-danger remove-product-row">Ukloni</button>
                    </div>
                </div>
            @endif
        @endforeach
    @else
        <div class="row mb-3 product-row">
            <div class="col-md-6">
                <label for="product_id_0" class="form-label">Proizvod</label>
                <select class="form-select product-select" name="product_ids[]" id="product_id_0">
                    <option value="">Odaberi proizvod</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                            {{ $product->name }} ({{ $product->power_w }}W, {{ $product->price }} {{ $product->currency }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="quantity_0" class="form-label">Količina</label>
                <input type="number" class="form-control product-quantity" name="quantities[]" id="quantity_0" value="1" min="1">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-danger remove-product-row">Ukloni</button>
            </div>
        </div>
    @endif
</div>
<button type="button" id="add-product-row" class="btn btn-success mb-3">Dodaj još proizvoda</button>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('product-selection-container');
        const addProductBtn = document.getElementById('add-product-row');
        let productRowIndex = {{ isset($selectedProducts) && !empty($selectedProducts) ? count($selectedProducts) : 1 }}; 

        addProductBtn.addEventListener('click', function () {
            const newRow = document.createElement('div');
            newRow.classList.add('row', 'mb-3', 'product-row');
            newRow.innerHTML = `
                <div class="col-md-6">
                    <label for="product_id_${productRowIndex}" class="form-label">Proizvod</label>
                    <select class="form-select product-select" name="product_ids[]" id="product_id_${productRowIndex}">
                        <option value="">Odaberi proizvod</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                                {{ $product->name }} ({{ $product->power_w }}W, {{ $product->price }} {{ $product->currency }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="quantity_${productRowIndex}" class="form-label">Količina</label>
                    <input type="number" class="form-control product-quantity" name="quantities[]" id="quantity_${productRowIndex}" value="1" min="1">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-danger remove-product-row">Ukloni</button>
                </div>
            `;
            container.appendChild(newRow);
            productRowIndex++;
        });

        container.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-product-row')) {
                if (container.querySelectorAll('.product-row').length > 1) {
                    e.target.closest('.product-row').remove();
                } else {
                    alert('Morate imati barem jedan proizvod u ponudi.');
                }
            }
        });
    });
</script>