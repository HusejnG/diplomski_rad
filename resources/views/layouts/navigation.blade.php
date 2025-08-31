<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    /* Paleta boja: #1C7C54, #73E2A7, #DEF4C6, #1B512D, #B1CF5F */
    body {
        font-family: 'Poppins', sans-serif;
    }

    .navbar-green {
        background-color: #1B512D; /* Tamna pozadina navigacije */
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .navbar-green .navbar-brand img {
        height: 70px;
        transition: transform 0.3s ease;
    }

    .navbar-green .navbar-brand:hover img {
        transform: scale(1.05);
    }

    .navbar-green .nav-link {
        color: #DEF4C6;
        transition: color 0.3s ease, background-color 0.3s ease;
        padding: 0.5rem 1rem;
    }

    .navbar-green .nav-link:hover {
        color: #B1CF5F;
    }

    .navbar-green .nav-link.active {
        color: #B1CF5F;
        font-weight: 600;
    }

    .navbar-green .dropdown-menu {
        background-color: #1B512D;
        border: none;
        border-radius: 0.5rem;
    }

    .navbar-green .dropdown-item {
        color: #DEF4C6;
        transition: background-color 0.3s ease;
    }

    .navbar-green .dropdown-item:hover {
        background-color: #1C7C54;
        color: #fff;
    }

    .navbar-green .navbar-toggler {
        border-color: rgba(222, 244, 198, 0.3);
    }

    .navbar-green .navbar-toggler-icon {
        filter: invert(1);
    }
</style>

<nav class="navbar navbar-expand-lg navbar-dark navbar-green shadow-sm">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand" href="{{ url('/') }}">
            <img src="{{ asset('images/logoSolarni.png') }}" alt="Solarni Sistem">
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" 
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Lijevi linkovi -->
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                @auth
                    @if(Auth::user()->isDesigner() || Auth::user()->isAdmin() || Auth::user()->isCustomer())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('quote-requests.*') ? 'active' : '' }}" 
                               href="{{ route('quote-requests.index') }}">
                                {{ __('Zahtjevi za ponudu') }}
                            </a>
                        </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('proposals.*') ? 'active' : '' }}" 
                           href="{{ route('proposals.index') }}">
                            {{ __('Upravljanje ponudama') }}
                        </a>
                    </li>
                @endauth

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('shop.index') ? 'active' : '' }}" 
                       href="{{ route('shop.index') }}">
                        {{ __('Proizvodi') }}
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('pvgis.index') ? 'active' : '' }}" 
                       href="{{ route('pvgis.index') }}">
                        {{ __('PVGIS Kalkulator') }}
                    </a>
                </li>
            </ul>

            <!-- Desni linkovi -->
            <ul class="navbar-nav ms-auto">
                @guest
                    @if (Route::has('login'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">{{ __('Prijavi se') }}</a>
                        </li>
                    @endif

                    @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">{{ __('Registruj se') }}</a>
                        </li>
                    @endif
                @else
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" 
                           data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ Auth::user()->name }}
                        </a>

                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                {{ __('Profil') }}
                            </a>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    {{ __('Odjavi se') }}
                                </button>
                            </form>
                        </div>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>
