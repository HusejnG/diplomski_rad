<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Upravljanje Proizvodima') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <a href="{{ route('products.create') }}" class="btn btn-primary mb-3">Dodaj novi proizvod</a>

                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($products->isEmpty())
                        <p>Nema dodatih proizvoda.</p>
                    @else
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Slika</th>
                                    <th>Naziv</th>
                                    <th>Tip</th>
                                    <th>Cena</th>
                                    <th>Snaga (W)</th>
                                    <th>Akcije</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($products as $product)
                                    <tr>
                                        <td>
                                            @if ($product->image_path)
                                                <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" style="width: 50px; height: 50px; object-fit: cover;">
                                            @else
                                                Nema slike
                                            @endif
                                        </td>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ ucfirst($product->type ?? 'N/A') }}</td>
                                        <td>{{ $product->price ? $product->price . ' ' . $product->currency : 'N/A' }}</td>
                                        <td>{{ $product->power_w ?? 'N/A' }}</td>
                                        <td>
                                            <a href="{{ route('products.show', $product) }}" class="btn btn-info btn-sm">Pregledaj</a>
                                            <a href="{{ route('products.edit', $product) }}" class="btn btn-warning btn-sm">Izmeni</a>
                                            <form action="{{ route('products.destroy', $product) }}" method="POST" style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Da li ste sigurni da želite da obrišete ovaj proizvod?')">Obriši</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>