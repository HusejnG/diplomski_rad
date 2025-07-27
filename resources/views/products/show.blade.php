<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detalji Proizvoda: ') }} {{ $product->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="row">
                        <div class="col-md-4">
                            @if ($product->image_path)
                                <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" class="img-fluid rounded shadow-sm">
                            @else
                                <p>Nema dostupne slike.</p>
                            @endif
                        </div>
                        <div class="col-md-8">
                            <dl class="row">
                                <dt class="col-sm-3">Naziv:</dt>
                                <dd class="col-sm-9">{{ $product->name }}</dd>

                                <dt class="col-sm-3">Tip:</dt>
                                <dd class="col-sm-9">{{ ucfirst($product->type ?? 'N/A') }}</dd>

                                <dt class="col-sm-3">Proizvođač:</dt>
                                <dd class="col-sm-9">{{ $product->manufacturer ?? 'N/A' }}</dd>

                                <dt class="col-sm-3">Model:</dt>
                                <dd class="col-sm-9">{{ $product->model ?? 'N/A' }}</dd>

                                <dt class="col-sm-3">Cena:</dt>
                                <dd class="col-sm-9">{{ $product->price ? $product->price . ' ' . $product->currency : 'N/A' }}</dd>

                                <dt class="col-sm-3">Snaga:</dt>
                                <dd class="col-sm-9">{{ $product->power_w ? $product->power_w . ' W' : 'N/A' }}</dd>

                                <dt class="col-sm-3">Dimenzije (DxŠxV):</dt>
                                <dd class="col-sm-9">
                                    @if ($product->length_mm && $product->width_mm && $product->height_mm)
                                        {{ $product->length_mm }}mm x {{ $product->width_mm }}mm x {{ $product->height_mm }}mm
                                    @elseif ($product->length_mm && $product->width_mm)
                                        {{ $product->length_mm }}mm x {{ $product->width_mm }}mm
                                    @else
                                        N/A
                                    @endif
                                </dd>

                                <dt class="col-sm-3">Opis:</dt>
                                <dd class="col-sm-9">{{ $product->description ?? 'N/A' }}</dd>

                                <dt class="col-sm-3">Dodato:</dt>
                                <dd class="col-sm-9">{{ $product->created_at->format('d.m.Y H:i') }}</dd>
                            </dl>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('products.index') }}" class="btn btn-secondary">Nazad na listu</a>
                        <a href="{{ route('products.edit', $product) }}" class="btn btn-warning">Izmeni</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>