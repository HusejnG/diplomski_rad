<x-app-layout>
    <x-slot name="header">
        <div class="text-center fade-in-up">
            <h2 class="display-5 fw-bold text-dark">
                {{ __('Profil korisnika') }}
            </h2>
            <div class="mx-auto my-2" style="width:60px; height:3px; background:#B1CF5F; border-radius:2px;"></div>
            <p class="text-muted">Upravljajte svojim podacima i sigurnošću računa</p>
        </div>
    </x-slot>

    <style>
        body {
            background-color: #f5f7fa;
        }

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

        .card-custom {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card-custom:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.12);
        }

        /* Dugmići */
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

        .btn-danger-profile {
            background-color: #DDa15E;
            border-color: #BC6C25;
            color: #fff;
            transition: all 0.3s ease;
        }
        .btn-danger-profile:hover {
            background-color: #BC6C25;
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.2);
        }
    </style>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="p-4 sm:p-8 bg-white card-custom fade-in-up">
                <div class="max-w-xl mx-auto">
                    @include('profile.partials.update-profile-information-form', ['btnClass' => 'btn-profile'])
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white card-custom fade-in-up" style="animation-delay: 0.2s;">
                <div class="max-w-xl mx-auto">
                    @include('profile.partials.update-password-form', ['btnClass' => 'btn-profile'])
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white card-custom fade-in-up" style="animation-delay: 0.4s;">
                <div class="max-w-xl mx-auto">
                    @include('profile.partials.delete-user-form', ['btnClass' => 'btn-danger-profile'])
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
