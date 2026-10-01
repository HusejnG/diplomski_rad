<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Solarko') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

        @vite(['resources/scss/app.scss', 'resources/js/app.js'])

        @stack('styles')

        <style>
            body { font-family: 'Inter', sans-serif; background-color: #F4F7F2; }
            .btn-primary { background-color: #1B512D; border-color: #1B512D; }
            .btn-primary:hover, .btn-primary:focus { background-color: #1C7C54; border-color: #1C7C54; }
            .btn-outline-primary { color: #1B512D; border-color: #1B512D; }
            .btn-outline-primary:hover { background-color: #1B512D; border-color: #1B512D; }
            a { color: #1C7C54; }
            .badge-status { font-size: .8rem; font-weight: 600; padding: .4em .75em; border-radius: 999px; }
        </style>
    </head>
    <body>
        <div class="min-vh-100 d-flex flex-column">
            @include('layouts.navigation')

            @isset($header)
                <header class="bg-white border-bottom">
                    <div class="container py-4">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main class="flex-grow-1">
                @if (session('success'))
                    <div class="container mt-3">
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="container mt-3">
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    </div>
                @endif

                {{ $slot }}
            </main>

            <footer class="bg-dark text-white-50 py-4 text-center mt-auto">
                <div class="container small">
                    &copy; {{ date('Y') }} {{ config('app.name', 'Solarko') }} &mdash; sistem za projektovanje i procjenu isplativosti solarnih elektrana.
                </div>
            </footer>
        </div>

        @stack('scripts')
    </body>
</html>
