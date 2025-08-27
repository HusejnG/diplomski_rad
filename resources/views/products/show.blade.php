<x-app-layout>
<x-slot name="header">
<h2 class="font-semibold text-xl text-gray-800 leading-tight">
{{ __('Detalji proizvoda: ') }} {{ $product->name }}
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

                            <dt class="col-sm-3">Cijena:</dt>
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

                            <dt class="col-sm-3">Dodano:</dt>
                            <dd class="col-sm-9">{{ $product->created_at->format('d.m.Y H:i') }}</dd>
                        </dl>
                    </div>
                </div>
                
                <!-- Kontejner za dugmad, koristi Bootstrap klase -->
                <div class="d-flex mt-4">
                    @if(Auth::check() && Auth::user()->isAdmin())
                        <div class="d-flex flex-column flex-sm-row justify-content-start gap-3 mt-4">

                            <a href="{{ route('products.edit', $product->id) }}" class="btn btn-primary btn-lg">
                                Uredi
                            </a>

                            <form action="{{ route('products.destroy', $product->id) }}" method="POST"
                                onsubmit="return confirm('Jeste li sigurni da želite obrisati ovaj proizvod?');"
                                class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-lg">
                                    Obriši
                                </button>
                            </form>
                        </div>
                    @endif

                    <a href="{{ route('shop.index') }}" class="btn btn-secondary ms-auto">Nazad na listu</a>
                </div>
            </div>
        </div>
    </div>
</div>

</x-app-layout>