<x-guest-layout>
    <h2 class="h4 fw-bold mb-3">{{ __('Nova lozinka') }}</h2>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="mb-3">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="mt-1 w-100" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mb-3">
            <x-input-label for="password" :value="__('Lozinka')" />
            <x-text-input id="password" class="mt-1 w-100" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mb-3">
            <x-input-label for="password_confirmation" :value="__('Potvrdi lozinku')" />
            <x-text-input id="password_confirmation" class="mt-1 w-100" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="d-flex justify-content-end mt-4">
            <x-primary-button>
                {{ __('Postavi novu lozinku') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
