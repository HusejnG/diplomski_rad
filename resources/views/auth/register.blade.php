<x-app-layout>
    <x-slot name="header">
        <h2 class="text-center font-bold text-3xl text-dark mb-4" style="font-family: 'Inter', sans-serif;">
            {{ __('Registracija') }}
        </h2>
    </x-slot>

    <div class="py-12" style="background: linear-gradient(135deg, #f5f7fa, #e4ebf0); min-height: 100vh;">
        <div class="container max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="card shadow-lg rounded-4 p-4 bg-white">
                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <!-- Ime -->
                        <div class="mb-3">
                            <label for="name" class="form-label">{{ __('Ime') }}</label>
                            <input id="name" class="form-control @error('name') is-invalid @enderror" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
                            @error('name')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">{{ __('Email') }}</label>
                            <input id="email" class="form-control @error('email') is-invalid @enderror" type="email" name="email" value="{{ old('email') }}" required autocomplete="username">
                            @error('email')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Lozinka -->
                        <div class="mb-3">
                            <label for="password" class="form-label">{{ __('Lozinka') }}</label>
                            <input id="password" class="form-control @error('password') is-invalid @enderror" type="password" name="password" required autocomplete="new-password">
                            @error('password')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Potvrda Lozinke -->
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">{{ __('Potvrdi Lozinku') }}</label>
                            <input id="password_confirmation" class="form-control" type="password" name="password_confirmation" required autocomplete="new-password">
                            @error('password_confirmation')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a class="text-link" href="{{ route('login') }}">
                                {{ __('Već ste registrovani?') }}
                            </a>

                            <button type="submit" class="btn btn-primary">
                                {{ __('Registruj se') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }
        .card {
            border-radius: 0.75rem;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
        }
        .btn-primary {
            background-color: #1B512D;
            border-color: #1B512D;
        }
        .btn-primary:hover {
            background-color: #283618;
            border-color: #283618;
        }
        .text-link {
            color: #606c38;
            text-decoration: none;
        }
        .text-link:hover {
            text-decoration: underline;
        }
    </style>
</x-app-layout>
