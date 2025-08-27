<x-app-layout>
    {{-- 'header' slot se koristi za naslov stranice, koji se prikazuje u navigaciji --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dobrodošli') }}
        </h2>
    </x-slot>

    {{-- Ovdje počinje sadržaj tvoje "hero" sekcije i svih ostalih dijelova --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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
                            <img src="https://placehold.co/400x300/0d6efd/FFFFFF?text=Solarni+Sistem" alt="Solar Panel Illustration" class="img-fluid rounded-lg shadow-sm">
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    {{-- Features/About Section --}}
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

    {{-- Footer --}}
    <footer class="bg-dark text-white py-4 text-center mt-auto">
        <div class="container">
            <p class="mb-0">&copy; {{ date('Y') }} {{ config('app.name', 'Solarni Sistem') }}. Sva prava zadržana.</p>
        </div>
    </footer>
</x-app-layout>
