<section class="p-4 sm:p-8 bg-white shadow sm:rounded-lg mb-6">
    <header class="mb-4">
        <h2 class="text-lg fw-bold text-dark">
            {{ __('Informacije o profilu') }}
        </h2>

        <p class="mt-1 text-muted">
            {{ __('Ažurirajte informacije o svom profilu i email adresu.') }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Ime')" />
            <x-text-input id="name" name="name" type="text" class="form-control mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2 text-danger" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="form-control mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2 text-danger" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2">
                    <p class="text-sm text-dark">
                        {{ __('Vaša email adresa nije potvrđena.') }}

                        <button form="send-verification" class="btn-link text-primary ms-1">
                            {{ __('Kliknite ovdje da pošaljete verifikacioni email ponovo.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-success text-sm">
                            {{ __('Novi verifikacioni link je poslan na vašu email adresu.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="d-flex align-items-center gap-2 mt-4">
            <x-primary-button class="btn btn-success px-4 py-2">
                {{ __('Sačuvaj') }}
            </x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-muted ms-2"
                >
                    {{ __('Sačuvano.') }}
                </p>
            @endif
        </div>
    </form>

    <style>
        .btn-link {
            background: none;
            border: none;
            padding: 0;
            font-size: 0.875rem;
            cursor: pointer;
            color: #606C38;
            text-decoration: underline;
        }
        .btn-link:hover {
            color: #BC6C25;
        }
    </style>
</section>
