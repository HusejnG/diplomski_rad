<div class="container my-5">
    <div class="bg-white shadow-md rounded-2xl p-5 p-md-6" style="background: #DEF4C6;">
        <h3 class="mb-4 text-xl fw-bold text-center" style="color: #1B512D;">
            {{ isset($proposal) ? 'Uredi ponudu' : 'Kreiraj novu ponudu' }}
        </h3>

        <form action="{{ $action }}" method="POST">
            @csrf
            @if(isset($proposal))
                @method('PUT')
            @endif

            {{-- Naslov ponude --}}
            <div class="mb-3">
                <label for="title" class="form-label fw-semibold">Naslov ponude</label>
                <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $proposal->title ?? 'Ponuda za solarni sistem') }}" required>
                @error('title') <div class="text-danger mt-1">{{ $message }}</div> @enderror
            </div>

            {{-- Opis ponude --}}
            <div class="mb-3">
                <label for="description" class="form-label fw-semibold">Opis ponude (opciono)</label>
                <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $proposal->description ?? '') }}</textarea>
                @error('description') <div class="text-danger mt-1">{{ $message }}</div> @enderror
            </div>

            {{-- Odabir proizvoda --}}
            <h4 class="mt-4 mb-3">Odaberi proizvode:</h4>
            <div id="product-selection-container">
                @if(isset($selectedProducts) && !empty($selectedProducts))
                    @foreach($selectedProducts as $productId => $quantity)
                        @php $product = $products->firstWhere('id', $productId); @endphp
                        @if($product)
                            <div class="row mb-3 product-row g-3 align-items-end">
                                <div class="col-md-6">
                                    <label for="product_id_{{ $productId }}" class="form-label">Proizvod</label>
                                    <select class="form-select" name="product_ids[]" id="product_id_{{ $productId }}">
                                        @foreach($products as $p)
                                            <option value="{{ $p->id }}" data-price="{{ $p->price }}" {{ $p->id == $productId ? 'selected' : '' }}>
                                                {{ $p->name }} ({{ $p->power_w }}W, {{ $p->price }} {{ $p->currency }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="quantity_{{ $productId }}" class="form-label">Količina</label>
                                    <input type="number" class="form-control" name="quantities[]" id="quantity_{{ $productId }}" value="{{ old('quantities.' . $loop->index, $quantity) }}" min="1">
                                </div>
                                <div class="col-md-2 d-flex">
                                    <button type="button" class="btn btn-danger w-100 remove-product-row">Ukloni</button>
                                </div>
                            </div>
                        @endif
                    @endforeach
                @else
                    <div class="row mb-3 product-row g-3 align-items-end">
                        <div class="col-md-6">
                            <label for="product_id_0" class="form-label">Proizvod</label>
                            <select class="form-select" name="product_ids[]" id="product_id_0">
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
                            <input type="number" class="form-control" name="quantities[]" id="quantity_0" value="1" min="1">
                        </div>
                        <div class="col-md-2 d-flex">
                            <button type="button" class="btn btn-danger w-100 remove-product-row">Ukloni</button>
                        </div>
                    </div>
                @endif
            </div>

            <button type="button" id="add-product-row" class="btn btn-success mb-3">Dodaj još proizvoda</button>

            <div class="mt-4 d-flex justify-content-between flex-wrap gap-3">
                <button type="submit" class="btn btn-success px-4 py-2 fw-semibold">💾 Sačuvaj ponudu</button>
                <a href="{{ route('proposals.index') }}" class="btn btn-outline-secondary px-4 py-2">⬅️ Nazad</a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('product-selection-container');
    const addProductBtn = document.getElementById('add-product-row');
    let productRowIndex = {{ isset($selectedProducts) && !empty($selectedProducts) ? count($selectedProducts) : 1 }};

    addProductBtn.addEventListener('click', function () {
        const newRow = document.createElement('div');
        newRow.classList.add('row', 'mb-3', 'product-row', 'g-3', 'align-items-end');
        newRow.innerHTML = `
            <div class="col-md-6">
                <label for="product_id_${productRowIndex}" class="form-label">Proizvod</label>
                <select class="form-select" name="product_ids[]" id="product_id_${productRowIndex}">
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
                <input type="number" class="form-control" name="quantities[]" id="quantity_${productRowIndex}" value="1" min="1">
            </div>
            <div class="col-md-2 d-flex">
                <button type="button" class="btn btn-danger w-100 remove-product-row">Ukloni</button>
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
