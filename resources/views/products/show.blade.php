<x-app-layout>
    <x-slot name="header">
        <div class="text-center fade-in-up" style="animation-delay: 0.2s;">
            <h2 class="text-2xl font-bold text-gray-800">
                <span style="color:#B1CF5F;">Detalji</span> proizvoda
            </h2>
            <p class="text-muted mt-1">{{ $product->name }}</p>
        </div>
    </x-slot>

    <main style="background: linear-gradient(to bottom, #DEF4C6, #fff);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 mb-8">
            <div class="bg-white shadow-md rounded-2xl p-6">
                <div class="row g-4">
                    <!-- Slika proizvoda -->
                    <div class="col-md-4 text-center">
                        @if ($product->image_path)
                            <img src="{{ asset('storage/' . $product->image_path) }}" 
                                 alt="{{ $product->name }}" 
                                 class="img-fluid rounded-2xl shadow-sm">
                        @else
                            <img src="{{ asset('images/no_image.png') }}" 
                                class="card-img-top rounded-top-4" 
                                alt="Nema slike" 
                                style="height: 220px; object-fit: cover;">
                        @endif
                    </div>

                    <!-- Detalji proizvoda -->
                    <div class="col-md-8">
                        <dl class="row gy-3">
                            <dt class="col-sm-4 text-gray-600">Naziv:</dt>
                            <dd class="col-sm-8 font-medium">{{ $product->name }}</dd>

                            <dt class="col-sm-4 text-gray-600">Tip:</dt>
                            <dd class="col-sm-8">{{ ucfirst($product->type ?? 'N/A') }}</dd>

                            <dt class="col-sm-4 text-gray-600">Proizvođač:</dt>
                            <dd class="col-sm-8">{{ $product->manufacturer ?? 'N/A' }}</dd>

                            <dt class="col-sm-4 text-gray-600">Model:</dt>
                            <dd class="col-sm-8">{{ $product->model ?? 'N/A' }}</dd>

                            <dt class="col-sm-4 text-gray-600">Cijena:</dt>
                            <dd class="col-sm-8 text-green-600 fw-bold">
                                {{ $product->price ? $product->price . ' ' . $product->currency : 'N/A' }}
                            </dd>

                            <dt class="col-sm-4 text-gray-600">Snaga:</dt>
                            <dd class="col-sm-8">{{ $product->power_w ? $product->power_w . ' W' : 'N/A' }}</dd>

                            <dt class="col-sm-4 text-gray-600">Dimenzije:</dt>
                            <dd class="col-sm-8">
                                @if ($product->length_mm && $product->width_mm && $product->height_mm)
                                    {{ $product->length_mm }} × {{ $product->width_mm }} × {{ $product->height_mm }} mm
                                @elseif ($product->length_mm && $product->width_mm)
                                    {{ $product->length_mm }} × {{ $product->width_mm }} mm
                                @else
                                    N/A
                                @endif
                            </dd>

                            <dt class="col-sm-4 text-gray-600">Opis:</dt>
                            <dd class="col-sm-8">{{ $product->description ?? 'N/A' }}</dd>

                            <dt class="col-sm-4 text-gray-600">Dodano:</dt>
                            <dd class="col-sm-8">{{ $product->created_at->format('d.m.Y H:i') }}</dd>
                        </dl>
                    </div>
                </div>

                <!-- Dugmad -->
                <div class="d-flex flex-wrap justify-content-between align-items-center mt-5 gap-3">
                    <div class="d-flex gap-3">
                        @if(Auth::check() && (Auth::user()->isAdmin() || Auth::user()->isDesigner()))
                            <a href="{{ route('products.edit', $product->id) }}" class="btn btn-success px-4">
                                ✏️ Uredi
                            </a>

                            <form action="{{ route('products.destroy', $product->id) }}" method="POST"
                                  onsubmit="return confirm('Jeste li sigurni da želite obrisati ovaj proizvod?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger px-4">
                                    🗑️ Obriši
                                </button>
                            </form>
                        @endif
                    </div>

                    <a href="{{ route('shop.index') }}" class="btn btn-outline-secondary px-4">⬅️ Nazad na listu</a>
                </div>
            </div>

            
        </div>
    </main>
</x-app-layout>
