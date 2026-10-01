<x-guest-layout>
    <h2 class="h4 fw-bold mb-3">{{ __('Zaboravljena lozinka') }}</h2>

    <div class="mb-3 small text-muted">
        {{ __('Unesite email adresu i poslat ćemo vam link za resetovanje lozinke.') }}
    </div>

    <x-auth-session-status class="mb-3" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="mb-3">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="mt-1 w-100" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="d-flex justify-content-end mt-4">
            <x-primary-button>
                {{ __('Pošalji link za reset') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
