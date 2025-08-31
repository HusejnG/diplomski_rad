<x-app-layout>
    <style>
        /* Paleta boja: #1C7C54, #73E2A7, #DEF4C6, #1B512D, #B1CF5F */

        /* Glavne boje za cijelu stranicu */
        .primary-bg { background-color: #1B512D; } /* Tamnija zelena za pozadine */
        .secondary-bg { background-color: #DEF4C6; } /* Vrlo svijetla zelena za kontraste */
        .accent-color { color: #B1CF5F; } /* Maslinasta za akcente (ikone, linije) */
        .text-dark { color: #1B512D; } /* Tekst u tamnoj boji */
        .text-light { color: #DEF4C6; } /* Tekst u svijetloj boji */

        /* Hero sekcija - kombinacija tamnih i svijetlih tonova */
        .hero-section {
            background-color: #1B512D; /* Primarna tamna pozadina */
            color: #DEF4C6; /* Svijetli tekst za čitljivost */
            position: relative;
            overflow: hidden;
        }

        .hero-section::before,
        .hero-section::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            opacity: 0.2;
            filter: blur(50px);
            animation: pulse-morph 12s infinite ease-in-out;
        }
        .hero-section::before {
            top: -50px;
            left: -50px;
            width: 250px;
            height: 250px;
            background: #cde0c2ff; /* Akcentna maslinasta */
            animation-delay: 0s;
        }
        .hero-section::after {
            bottom: -70px;
            right: -70px;
            width: 350px;
            height: 350px;
            background: #73E2A7; /* Svijetla zelena */
            animation-delay: -6s;
        }

        @keyframes pulse-morph {
            0% { transform: scale(0.9) translate(0, 0); }
            25% { transform: scale(1.1) translate(20px, 20px); }
            50% { transform: scale(1.0) translate(0, 0); }
            75% { transform: scale(1.2) translate(-20px, -20px); }
            100% { transform: scale(0.9) translate(0, 0); }
        }

        /* Gumbi - elegantan hover efekt */
        .btn-main {
            background-color: #cde0c2ff;
            border-color: #cde0c2ff;
            color: #1B512D;
            transition: transform 0.3s ease, background-color 0.3s ease;
        }
        .btn-main:hover {
            background-color: #1B512D;
            color: #DEF4C6;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        /* Kartice - jednostavno i čisto */
        .feature-card {
            border: none;
            background-color: #cde0c2ff; /* Bijelo-zelena podloga */
            color: #1B512D; /* Tamni tekst */
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
        }
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }
        .feature-card .card-body h3 {
            color: #1B512D;
        }
        .feature-card .card-body p {
            color: #414833; /* Tamniji tekst za bolju čitljivost */
        }
        .feature-card .icon-box {
            color: #B1CF5F; /* Akcentna boja za ikone */
        }

        /* Animacija */
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

    <div class="container-fluid py-5">
        <section class="hero-section py-5">
            <div class="container py-5">
                <div class="row align-items-center flex-md-row-reverse">
                    <div class="col-12 col-lg-6 text-center text-lg-start mb-4 mb-lg-0 fade-in-up" style="animation-delay: 0.3s;">
                        <img src="{{ asset('images/slikaPaneli.png') }}"
                             alt="Kuća sa solarnim panelima"
                             class="img-fluid rounded-4 shadow-lg">
                    </div>
                    <div class="col-12 col-lg-6 text-center text-lg-start fade-in-up">
                        <h1 class="display-4 fw-bold mb-3">
                            Solarni sistem za vašu budućnost
                        </h1>
                        <p class="lead mb-4 text-light">
                            Naša platforma omogućava jednostavno i brzo planiranje solarnih elektrana. 
                            Odaberite lokaciju, izračunajte proizvodnju i povežite se sa provjerenim kompanijama.
                        </p>
                        <a href="{{ route('shop.index') }}" class="btn btn-main btn-lg rounded-pill px-4 shadow-sm">
                            Započnite odmah
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <hr class="my-5" style="border-color: #294131ff;">

        <section class="py-5 secondary-bg">
            <div class="container">
                <h2 class="display-5 fw-bold text-center mb-5 fade-in-up" style="animation-delay: 0.6s; color: #1B512D;">Kako funkcioniše?</h2>
                <div class="row row-cols-1 row-cols-md-3 g-4">
                    <div class="col fade-in-up" style="animation-delay: 0.8s;">
                        <a href="{{ route('quote-requests.index') }}" style="text-decoration: none; color: inherit;">
                            <div class="card h-100 feature-card shadow-sm rounded-4">
                                <div class="card-body text-center p-4">
                                    <div class="icon-box mb-3" style="font-size: 3rem;">
                                        <i class="bi bi-send accent-color"></i>
                                    </div>
                                    <h3 class="h5 fw-semibold mb-3">1. Pošaljite zahtjev</h3>
                                    <p class="text-muted">
                                        Unesite osnovne informacije o vašim potrebama za energijom i lokaciji.
                                    </p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col fade-in-up" style="animation-delay: 1s;">
                        <a href="{{ route('proposals.index') }}" class="text-decoration-none">
                            <div class="card h-100 feature-card shadow-sm rounded-4">
                                <div class="card-body text-center p-4">
                                    <div class="icon-box mb-3" style="font-size: 3rem;">
                                        <i class="bi bi-lightbulb accent-color"></i>
                                    </div>
                                    <h3 class="h5 fw-semibold mb-3">2. Dizajner kreira ponudu</h3>
                                    <p class="text-muted">
                                        Naši stručnjaci će dizajnirati sistem koristeći najbolje komponente.
                                    </p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col fade-in-up" style="animation-delay: 1.2s;">
                        <div class="card h-100 feature-card shadow-sm rounded-4">
                            <div class="card-body text-center p-4">
                                <div class="icon-box mb-3" style="font-size: 3rem;">
                                    <i class="bi bi-patch-check accent-color"></i>
                                </div>
                                <h3 class="h5 fw-semibold mb-3">3. Prihvatite i realizujte</h3>
                                <p class="text-muted">
                                    Pregledajte ponudu, prihvatite je i započnite instalaciju.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <hr class="my-5" style="border-color: #1B512D;">

        <footer class="text-light py-4 text-center primary-bg">
            <div class="container">
                <p class="mb-0">&copy; {{ date('Y') }} {{ config('app.name', 'Solarni Sistem') }}. Sva prava zadržana.</p>
            </div>
        </footer>
    </div>
</x-app-layout>