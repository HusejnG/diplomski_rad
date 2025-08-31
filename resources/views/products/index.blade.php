<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Upravljanje proizvodima') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[#fefae0] shadow-lg rounded-2xl overflow-hidden">
                <div class="p-6">
                    {{-- Gumb "Dodaj novi proizvod" vidljiv samo administratoru --}}
                    @if(Auth::check() && Auth::user()->isAdmin())
                        <a href="{{ route('products.create') }}" class="btn" style="background-color: #606c38; color: #fefae0;">Dodaj novi proizvod</a>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success mt-3">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($products->isEmpty())
                        <p class="mt-4 text-center text-gray-600">Nema dodanih proizvoda.</p>
                    @else
                        <div class="table-responsive mt-4">
                            <table class="table align-middle text-center">
                                <thead class="text-white" style="background-color: #283618;">
                                    <tr>
                                        <th scope="col">Slika</th>
                                        <th scope="col">Naziv</th>
                                        <th scope="col">Tip</th>
                                        <th scope="col">Cijena</th>
                                        <th scope="col">Snaga (W)</th>
                                        @if(Auth::check() && (Auth::user()->isAdmin() || Auth::user()->isDesigner()))
                                            <th scope="col">Akcije</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($products as $product)
                                        <tr class="align-middle" style="transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#ddebd0'" onmouseout="this.style.backgroundColor='transparent'">
                                            <td>
                                                @if ($product->image_path)
                                                    <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" class="rounded shadow-sm" style="width: 60px; height: 60px; object-fit: cover;">
                                                @else
                                                    <span class="text-muted">Nema slike</span>
                                                @endif
                                            </td>
                                            <td>{{ $product->name }}</td>
                                            <td>{{ ucfirst($product->type ?? 'N/A') }}</td>
                                            <td>{{ $product->price ? $product->price . ' ' . $product->currency : 'N/A' }}</td>
                                            <td>{{ $product->power_w ?? 'N/A' }}</td>
                                            @if(Auth::check() && (Auth::user()->isAdmin() || Auth::user()->isDesigner()))
                                                <td>
                                                    <a href="{{ route('products.show', $product) }}" class="btn btn-sm" style="background-color: #dda15e; color: #fefae0;">Pregledaj</a>
                                                    <a href="{{ route('products.edit', $product) }}" class="btn btn-sm" style="background-color: #bc6c25; color: #fefae0;">Izmijeni</a>
                                                    <form action="{{ route('products.destroy', $product) }}" method="POST" style="display:inline-block;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm" style="background-color: #e63946; color: #fefae0;" onclick="return confirm('Da li ste sigurni da želite da obrišete ovaj proizvod?')">Obriši</button>
                                                    </form>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
