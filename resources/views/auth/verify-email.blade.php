<x-guest-layout>
    <h2 class="h4 fw-bold mb-3">{{ __('Potvrdite email adresu') }}</h2>

    <div class="mb-3 small text-muted">
        {{ __('Hvala na registraciji! Prije početka korištenja, molimo potvrdite email adresu klikom na link koji smo vam poslali. Ako niste dobili email, rado ćemo poslati novi.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success small">
            {{ __('Novi link za potvrdu je poslan na email adresu koju ste unijeli prilikom registracije.') }}
        </div>
    @endif

    <div class="d-flex align-items-center justify-content-between mt-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-primary-button>
                {{ __('Ponovo pošalji email') }}
            </x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-link text-muted small">
                {{ __('Odjavi se') }}
            </button>
        </form>
    </div>
</x-guest-layout>
