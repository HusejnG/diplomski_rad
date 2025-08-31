<section class="fade-in-up">
    <header class="mb-4">
        <h2 class="text-xl fw-bold text-dark">
            {{ __('Obriši profil') }}
        </h2>

        <p class="mt-1 text-muted">
            {{ __('Kada obrišete svoj profil, svi njegovi resursi i podaci će biti trajno obrisani. Prije brisanja preuzmite podatke koje želite sačuvati.') }}
        </p>
    </header>

    <x-danger-button
        class="btn-delete-account"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >
        {{ __('Obriši nalog') }}
    </x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg fw-bold text-dark">
                {{ __('Da li ste sigurni da želite obrisati svoj profil?') }}
            </h2>

            <p class="mt-1 text-muted">
                {{ __('Kada obrišete svoj profil, svi njegovi resursi i podaci će biti trajno obrisani. Unesite svoju šifru kako biste potvrdili brisanje.') }}
            </p>

            <div class="mt-4 mb-4">
                <x-input-label for="password" value="{{ __('Lozinka') }}" class="sr-only" />
                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="form-control mt-1 w-75"
                    placeholder="{{ __('Lozinka') }}"
                />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2 text-danger" />
            </div>

            <div class="d-flex justify-content-end gap-2">
                <x-secondary-button x-on:click="$dispatch('close')" class="btn-secondary">
                    {{ __('Otkaži') }}
                </x-secondary-button>

                <x-danger-button class="btn-danger px-3 py-2">
                    {{ __('Obriši profil') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>

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

        .btn-delete-account {
            background-color: #BC6C25;
            border-color: #606C38;
            color: #FEFAE0;
            transition: all 0.3s ease;
        }
        .btn-delete-account:hover {
            background-color: #283618;
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.2);
        }

        .btn-secondary {
            background-color: #FEFAE0;
            color: #606C38;
            border: 1px solid #606C38;
            transition: all 0.3s ease;
        }
        .btn-secondary:hover {
            background-color: #606C38;
            color: #FEFAE0;
            transform: translateY(-2px);
        }

        .btn-danger {
            background-color: #BC6C25;
            color: #FEFAE0;
            border: none;
            transition: all 0.3s ease;
        }
        .btn-danger:hover {
            background-color: #283618;
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.2);
        }
    </style>
</section>
