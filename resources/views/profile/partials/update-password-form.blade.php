<section class="fade-in-up">
    <header class="mb-4">
        <h2 class="text-xl fw-bold text-dark">
            {{ __('Promjena šifre') }}
        </h2>
        <p class="mt-1 text-muted">
            {{ __('Osigurajte da vaš profil koristi dugu, nasumičnu šifre radi veće sigurnosti.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6">
        @csrf
        @method('put')

        <div class="mb-3">
            <x-input-label for="update_password_current_password" :value="__('Trenutna šifra')" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="form-control mt-1" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2 text-danger" />
        </div>

        <div class="mb-3">
            <x-input-label for="update_password_password" :value="__('Nova šifra')" />
            <x-text-input id="update_password_password" name="password" type="password" class="form-control mt-1" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2 text-danger" />
        </div>

        <div class="mb-3">
            <x-input-label for="update_password_password_confirmation" :value="__('Potvrdi šifru')" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="form-control mt-1" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2 text-danger" />
        </div>

        <div class="d-flex align-items-center gap-3 mt-4">
            <button type="submit" class="btn btn-profile px-4 py-2">
                {{ __('Spremi') }}
            </button>

            @if (session('status') === 'password-updated')
                <p class="text-muted mb-0" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)">
                    {{ __('Sačuvano.') }}
                </p>
            @endif
        </div>
    </form>

    <style>
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

        /* Dugme u paleti boja */
        .btn-profile {
            background-color: #606C38;
            border-color: #283618;
            color: #FEFAE0;
            transition: all 0.3s ease;
        }
        .btn-profile:hover {
            background-color: #BC6C25;
            color: #FEFAE0;
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.2);
        }
    </style>
</section>
