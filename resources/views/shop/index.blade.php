{{-- resources/views/shop/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Naši proizvodi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($products->isEmpty())
                        <p>Trenutno nema dostupnih proizvoda.</p>
                    @else
                        <div class="row row-cols-1 row-cols-md-3 g-4">
                            @foreach ($products as $product)
                                <div class="col">
                                    <div class="card h-100">
                                        @if ($product->image_path)
                                            <img src="{{ asset('storage/' . $product->image_path) }}" class="card-img-top" alt="{{ $product->name }}" style="height: 200px; object-fit: cover;">
                                        @else
                                            <img src="https://via.placeholder.com/200x200?text=No+Image" class="card-img-top" alt="Nema slike" style="height: 200px; object-fit: cover;">
                                        @endif
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $product->name }}</h5>
                                            <p class="card-text">
                                                Tip: {{ ucfirst($product->type ?? 'N/A') }}<br>
                                                Proizvođač: {{ $product->manufacturer ?? 'N/A' }}<br>
                                                Model: {{ $product->model ?? 'N/A' }}<br>
                                                Snaga: {{ $product->power_w ? $product->power_w . ' W' : 'N/A' }}<br>
                                                Cijena: {{ $product->price ? $product->price . ' ' . $product->currency : 'N/A' }}
                                            </p>
                                            <a href="{{ route('products.show', $product) }}" class="btn btn-primary">Detalji</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>