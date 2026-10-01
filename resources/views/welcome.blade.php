<x-app-layout>
    <style>
        .hero-section {
            background-color: #1B512D;
            color: #DEF4C6;
            position: relative;
            overflow: hidden;
        }
        .hero-section::before, .hero-section::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            opacity: 0.18;
            filter: blur(60px);
        }
        .hero-section::before { top: -60px; left: -60px; width: 280px; height: 280px; background: #B1CF5F; }
        .hero-section::after { bottom: -80px; right: -80px; width: 380px; height: 380px; background: #73E2A7; }

        .btn-main {
            background-color: #DEF4C6;
            border-color: #DEF4C6;
            color: #1B512D;
            font-weight: 600;
            transition: transform .2s ease, background-color .2s ease;
        }
        .btn-main:hover { background-color: #B1CF5F; border-color: #B1CF5F; color: #1B512D; transform: translateY(-2px); }

        .step-card { border: none; background-color: #F4F7F2; transition: transform .2s ease, box-shadow .2s ease; height: 100%; }
        .step-card:hover { transform: translateY(-6px); box-shadow: 0 15px 30px rgba(0,0,0,.08); }
        .step-number {
            width: 42px; height: 42px; border-radius: 50%;
            background-color: #1C7C54; color: #fff; font-weight: 700;
            display: flex; align-items: center; justify-content: center;
        }
        .stat-box { color: #1B512D; }
    </style>

    <section class="hero-section py-5">
        <div class="container py-5">
            <div class="row align-items-center">
                <div class="col-12 col-lg-6 mb-4 mb-lg-0">
                    <h1 class="display-5 fw-bold mb-3">Isplati li se solarna elektrana na vašoj lokaciji?</h1>
                    <p class="lead mb-4" style="color: #DEF4C6;">
                        Označite lokaciju na mapi, odaberite tip površine (kosi ili ravni krov, zemljište, brdovit teren...),
                        unesite dimenzije i prosječnu potrošnju - sistem automatski projektuje solarnu elektranu koristeći
                        stvarne podatke o sunčevom zračenju (PVGIS) i izračunava period povrata investicije.
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="{{ route('calculator.index') }}" class="btn btn-main btn-lg rounded-pill px-4 shadow-sm">
                            <i class="bi bi-calculator me-1"></i> Izračunaj isplativost
                        </a>
                        @guest
                            <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg rounded-pill px-4">
                                Registruj se
                            </a>
                        @endguest
                    </div>
                </div>
                <div class="col-12 col-lg-6 text-center">
                    <img src="{{ asset('images/slikaPaneli.png') }}" alt="Solarni paneli na kući" class="img-fluid rounded-4 shadow-lg">
                </div>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <h2 class="h1 fw-bold text-center mb-2" style="color: #1B512D;">Kako funkcioniše?</h2>
            <p class="text-center text-muted mb-5">Od ideje do ugrađenog sistema - sve na jednom mjestu.</p>

            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
                <div class="col">
                    <div class="card step-card shadow-sm rounded-4 p-3">
                        <div class="card-body">
                            <div class="step-number mb-3">1</div>
                            <h3 class="h6 fw-bold">Lokacija i površina</h3>
                            <p class="text-muted small mb-0">Označite lokaciju na mapi ili je pretražite, odaberite tip površine (krov, zemljište...) i unesite njene dimenzije.</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card step-card shadow-sm rounded-4 p-3">
                        <div class="card-body">
                            <div class="step-number mb-3">2</div>
                            <h3 class="h6 fw-bold">Automatsko projektovanje</h3>
                            <p class="text-muted small mb-0">Sistem bira odgovarajuće panele i invertor iz kataloga i izračunava godišnju proizvodnju energije preko PVGIS servisa.</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card step-card shadow-sm rounded-4 p-3">
                        <div class="card-body">
                            <div class="step-number mb-3">3</div>
                            <h3 class="h6 fw-bold">Isplativost i povrat investicije</h3>
                            <p class="text-muted small mb-0">Dobijate procjenu investicije, godišnje uštede, period povrata i projekciju za narednih 25 godina.</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card step-card shadow-sm rounded-4 p-3">
                        <div class="card-body">
                            <div class="step-number mb-3">4</div>
                            <h3 class="h6 fw-bold">Narudžba i ugradnja</h3>
                            <p class="text-muted small mb-0">Jednim klikom pošaljete narudžbu - projektant pregleda i odobrava prijedlog, te zakazuje ugradnju.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5" style="background-color: #DEF4C6;">
        <div class="container">
            <div class="row text-center g-4 stat-box">
                <div class="col-6 col-md-3">
                    <div class="display-6 fw-bold">PVGIS</div>
                    <div class="small text-muted">Zvanični podaci EU o sunčevom zračenju</div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="display-6 fw-bold">25 god.</div>
                    <div class="small text-muted">Horizont finansijske projekcije</div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="display-6 fw-bold">6 tipova</div>
                    <div class="small text-muted">Podržanih površina za ugradnju</div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="display-6 fw-bold">1 klik</div>
                    <div class="small text-muted">Za slanje narudžbe projektantu</div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 text-center">
        <div class="container">
            <h2 class="h3 fw-bold mb-3" style="color: #1B512D;">Spremni da provjerite svoju lokaciju?</h2>
            <a href="{{ route('calculator.index') }}" class="btn btn-primary btn-lg rounded-pill px-5">
                Pokreni kalkulator
            </a>
        </div>
    </section>
</x-app-layout>
