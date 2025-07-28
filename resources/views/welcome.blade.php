<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Solarni Sistem') }}</title>

    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa; 
            color: #212529; 
        }
        .navbar-brand {
            font-weight: 700;
            color: #0d6efd !important; 
        }
        .btn-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
        .btn-primary:hover {
            background-color: #0b5ed7;
            border-color: #0a58ca;
        }
        .btn-outline-primary {
            color: #0d6efd;
            border-color: #0d6efd;
        }
        .btn-outline-primary:hover {
            background-color: #0d6efd;
            color: #fff;
        }
        .hero-section {
            background-color: #e9ecef; 
            padding: 6rem 0;
        }
        .feature-card {
            background-color: #fff;
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            padding: 1.5rem;
            text-align: center;
        }
        .feature-card h3 {
            color: #0d6efd;
        }
        body.dark-mode {
            background-color: #212529;
            color: #f8f9fa;
        }
        .dark-mode .bg-white {
            background-color: #343a40 !important;
        }
        .dark-mode .text-gray-900 {
            color: #f8f9fa !important;
        }
        .dark-mode .text-gray-600 {
            color: #ced4da !important;
        }
        .dark-mode .navbar-brand {
            color: #6daffb !important;
        }
        .dark-mode .btn-outline-primary {
            color: #6daffb;
            border-color: #6daffb;
        }
        .dark-mode .btn-outline-primary:hover {
            background-color: #6daffb;
            color: #212529;
        }
        .dark-mode .hero-section {
            background-color: #343a40;
        }
        .dark-mode .feature-card {
            background-color: #495057;
        }
        .dark-mode .feature-card h3 {
            color: #6daffb;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

    <!-- Navigation Header -->
    <header class="navbar navbar-expand-lg navbar-light bg-white shadow-sm py-3">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                {{ config('app.name', 'Solarni Sistem') }}
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @if (Route::has('login'))
                        @auth
                            <li class="nav-item">
                                <a href="{{ url('/dashboard') }}" class="nav-link text-dark">
                                    Dashboard
                                </a>
                            </li>
                        @else
                            <li class="nav-item">
                                <a href="{{ route('login') }}" class="nav-link text-dark">
                                    Prijavi se
                                </a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a href="{{ route('register') }}" class="btn btn-primary rounded-md shadow-sm">
                                        Registruj se
                                    </a>
                                </li>
                            @endif
                        @endauth
                    @endif
                </ul>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <main class="flex-grow-1 d-flex align-items-center justify-content-center py-5">
        <div class="container">
            <div class="row align-items-center bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="col-md-6 p-5 d-flex flex-column justify-content-center text-center text-md-start">
                    <h1 class="display-4 fw-bold mb-3">
                        Projektujte Svoj Solarni Sistem
                    </h1>
                    <p class="lead text-muted mb-4">
                        Jednostavno kreirajte zahtjeve za ponudu i dizajnirajte efikasne solarne sisteme za vaš dom ili posao.
                    </p>
                    <div class="d-flex flex-column flex-sm-row justify-content-center justify-content-md-start gap-3">
                        <a href="{{ route('register') }}" class="btn btn-primary btn-lg rounded-lg shadow">
                            Započnite odmah
                        </a>
                        <a href="{{ route('shop.index') }}" class="btn btn-outline-primary btn-lg rounded-lg shadow">
                            Pregledajte proizvode
                        </a>
                    </div>
                </div>
                <div class="col-md-6 d-flex align-items-center justify-content-center p-4">
                    <!-- Placeholder Image for Solar Panels -->
                    <img src="https://placehold.co/400x300/0d6efd/FFFFFF?text=Solarni+Sistem" alt="Solar Panel Illustration" class="img-fluid rounded-lg shadow-sm">
                </div>
            </div>
        </div>
    </main>

    <!-- Features/About Section -->
    <section class="bg-light py-5">
        <div class="container text-center">
            <h2 class="display-5 fw-bold mb-5">Kako funkcioniše?</h2>
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <div class="col">
                    <div class="feature-card">
                        <h3 class="h5 fw-semibold mb-3">1. Pošaljite zahtjev</h3>
                        <p class="text-muted">Unesite osnovne informacije o vašim potrebama za energijom i lokaciji.</p>
                    </div>
                </div>
                <div class="col">
                    <div class="feature-card">
                        <h3 class="h5 fw-semibold mb-3">2. Dizajner kreira ponudu</h3>
                        <p class="text-muted">Naši stručnjaci će dizajnirati sistem koristeći najbolje komponente.</p>
                    </div>
                </div>
                <div class="col">
                    <div class="feature-card">
                        <h3 class="h5 fw-semibold mb-3">3. Prihvatite i realizujte</h3>
                        <p class="text-muted">Pregledajte ponudu, prihvatite je i započnite instalaciju.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 text-center mt-auto">
        <div class="container">
            <p class="mb-0">&copy; {{ date('Y') }} {{ config('app.name', 'Solarni Sistem') }}. Sva prava zadržana.</p>
        </div>
    </footer>

    <!-- Bootstrap JS CDN (Bundle with Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
