<x-guest-layout>
    <h2 class="h4 fw-bold mb-3">{{ __('Potvrda lozinke') }}</h2>

    <div class="mb-3 small text-muted">
        {{ __('Ovo je zaštićeni dio aplikacije. Molimo potvrdite lozinku prije nastavka.') }}
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div class="mb-3">
            <x-input-label for="password" :value="__('Lozinka')" />
            <x-text-input id="password" class="mt-1 w-100"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="d-flex justify-content-end mt-4">
            <x-primary-button>
                {{ __('Potvrdi') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
