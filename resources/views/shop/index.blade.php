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
                    {{-- Gumb "Dodaj novi proizvod" vidljiv samo administratoru i dizajneru --}}
                    @if(Auth::check() && (Auth::user()->isAdmin() || Auth::user()->isDesigner()))
                        <a href="{{ route('products.create') }}" class="btn btn-primary mb-3">Dodaj novi proizvod</a>
                    @endif

                    {{-- Obrazac za pretraživanje i filtriranje --}}
                    <form action="{{ route('shop.index') }}" method="GET" class="mb-4">
                        <div class="row g-3 align-items-end">
                            {{-- Pretraga po nazivu --}}
                            <div class="col-md-4">
                                <label for="search" class="form-label">Pretražite proizvode:</label>
                                <input type="text" class="form-control" id="search" name="search" placeholder="Naziv, opis, model..." value="{{ request('search') }}">
                            </div>

                            {{-- Filter po tipu (sada dinamičan i za NULL) --}}
                            <div class="col-md-3">
                                <label for="type" class="form-label">Filtriraj po tipu:</label>
                                <select id="type" name="type" class="form-select">
                                    <option value="">Svi tipovi</option>
                                    <option value="null" {{ request('type') == 'null' ? 'selected' : '' }}>N/A</option>
                                    @foreach($availableTypes as $type)
                                        @if($type)
                                            <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                                {{ ucfirst($type) }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            {{-- Filter po proizvođaču --}}
                            <div class="col-md-3">
                                <label for="manufacturer" class="form-label">Filtriraj po proizvođaču:</label>
                                <input type="text" class="form-control" id="manufacturer" name="manufacturer" placeholder="Npr. Jinko, Huawei..." value="{{ request('manufacturer') }}">
                            </div>

                            {{-- Gumb za filtriranje --}}
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-dark w-100">Filtriraj</button>
                            </div>
                        </div>
                    </form>

                    {{-- Prikaz poruka o uspjehu, ako ih ima --}}
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

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
                                            
                                            <div class="d-flex justify-content-between align-items-center mt-3">
                                                <a href="{{ route('products.show', $product) }}" class="btn btn-primary">Detalji</a>

                                                {{-- Gumbi za uređivanje i brisanje vidljivi samo administratoru i dizajneru --}}
                                                @if(Auth::check() && (Auth::user()->isAdmin() || Auth::user()->isDesigner()))
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('products.edit', $product) }}" class="btn btn-warning">Izmijeni</a>
                                                        <form action="{{ route('products.destroy', $product) }}" method="POST" style="display:inline-block;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger" onclick="return confirm('Da li ste sigurni da želite da obrišete ovaj proizvod?')">Obriši</button>
                                                        </form>
                                                    </div>
                                                @endif
                                            </div>
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
