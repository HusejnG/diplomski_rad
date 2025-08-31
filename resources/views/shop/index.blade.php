<x-app-layout>
    <x-slot name="header">
        <div class="text-center my-5 fade-in-up" style="animation-delay: 0.2s;">
            <h2 class="display-4 fw-bold text-dark">
                <span style="color:#1B512D;">Naši proizvodi</span> 
            </h2>
            <div style="width:80px; height:4px; background:#B1CF5F; margin:15px auto; border-radius:2px;"></div>
            <p class="lead text-muted mb-5">Pregledajte našu ponudu solarne opreme i komponenti</p>
        </div>
    </x-slot>



    <style>
        /* Paleta boja */
        .primary-bg { background-color: #1B512D; }
        .secondary-bg { background-color: #DEF4C6; }
        .accent-color { color: #eaf5ccff; }
        .text-dark { color: #1B512D; }
        .btn-main {
            background-color: #cde0c2ff;
            border-color: #6f8175ff;
            color: #1B512D;
            transition: all 0.3s ease;
        }
        .btn-main:hover {
            background-color: #1B512D;
            color: #DEF4C6;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        .product-card {
            border: none;
            background-color: #cde0c2ff;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 25px rgba(0,0,0,0.15);
        }
        .fade-in-up {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.8s forwards;
        }
        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    <div class="container">
        <div class="bg-white shadow-sm rounded-4 p-5 fade-in-up">

            {{-- Gumb "Dodaj novi proizvod" --}}
            @if(Auth::check() && (Auth::user()->isAdmin() || Auth::user()->isDesigner()))
                <a href="{{ route('products.create') }}" class="btn btn-main mb-4 rounded-pill shadow-sm">
                    + Dodaj novi proizvod
                </a>
            @endif

            {{-- Pretraga i filteri --}}
            <form action="{{ route('shop.index') }}" method="GET" class="mb-5">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="search" class="form-label fw-semibold text-dark">Pretražite proizvode:</label>
                        <input type="text" class="form-control shadow-sm" id="search" name="search" placeholder="Naziv, opis, model..." value="{{ request('search') }}">
                    </div>

                    <div class="col-md-3">
                        <label for="type" class="form-label fw-semibold text-dark">Filtriraj po tipu:</label>
                        <select id="type" name="type" class="form-select shadow-sm">
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

                    <div class="col-md-3">
                        <label for="manufacturer" class="form-label fw-semibold text-dark">Filtriraj po proizvođaču:</label>
                        <input type="text" class="form-control shadow-sm" id="manufacturer" name="manufacturer" placeholder="Npr. Jinko, Huawei..." value="{{ request('manufacturer') }}">
                    </div>

                    <div class="col-md-2">
                        <button type="submit" class="btn btn-dark w-100 rounded-pill shadow-sm">Filtriraj</button>
                    </div>
                </div>
            </form>

            {{-- Poruke o uspjehu --}}
            @if (session('success'))
                <div class="alert alert-success rounded-3 shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Proizvodi --}}
            @if ($products->isEmpty())
                <p class="text-center text-muted">Trenutno nema dostupnih proizvoda.</p>
            @else
                <div class="row row-cols-1 row-cols-md-3 g-4">
                    @foreach ($products as $product)
                        <div class="col fade-in-up" style="animation-delay: {{ $loop->iteration * 0.1 }}s;">
                            <div class="card h-100 product-card rounded-4 shadow-sm">
                                @if ($product->image_path)
                                    <img src="{{ asset('storage/' . $product->image_path) }}" 
                                        class="card-img-top rounded-top-4" 
                                        alt="{{ $product->name }}" 
                                        style="height: 220px; object-fit: cover;">
                                @else
                                    <img src="{{ asset('images/no_image.png') }}" 
                                        class="card-img-top rounded-top-4" 
                                        alt="Nema slike" 
                                        style="height: 220px; object-fit: cover;">
                                @endif
                                <div class="card-body p-4">
                                    <h5 class="fw-bold mb-3 text-dark">{{ $product->name }}</h5>
                                    <p class="mb-3 text-muted">
                                        <strong>Tip:</strong> {{ ucfirst($product->type ?? 'N/A') }}<br>
                                        <strong>Proizvođač:</strong> {{ $product->manufacturer ?? 'N/A' }}<br>
                                        <strong>Model:</strong> {{ $product->model ?? 'N/A' }}<br>
                                        <strong>Snaga:</strong> {{ $product->power_w ? $product->power_w . ' W' : 'N/A' }}<br>
                                        <strong>Cijena:</strong> {{ $product->price ? $product->price . ' ' . $product->currency : 'N/A' }}
                                    </p>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <a href="{{ route('products.show', $product) }}" class="btn btn-main rounded-pill shadow-sm">Detalji</a>

                                        @if(Auth::check() && (Auth::user()->isAdmin() || Auth::user()->isDesigner()))
                                            <div class="btn-group">
                                                <a href="{{ route('products.edit', $product) }}" class="btn btn-warning rounded-pill">Izmijeni</a>
                                                <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('Da li ste sigurni da želite da obrišete ovaj proizvod?')" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger rounded-pill">Obriši</button>
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
</x-app-layout>
