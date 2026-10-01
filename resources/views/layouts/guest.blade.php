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

        <style>
            body {
                font-family: 'Inter', sans-serif;
                background: linear-gradient(135deg, #f5f7fa, #e4ebf0);
                min-height: 100vh;
            }
            .btn-primary { background-color: #1B512D; border-color: #1B512D; }
            .btn-primary:hover { background-color: #1C7C54; border-color: #1C7C54; }
        </style>
    </head>
    <body class="d-flex flex-column justify-content-center align-items-center py-5">
        <a href="/" class="mb-4">
            <img src="{{ asset('images/logoSolarni.png') }}" alt="{{ config('app.name') }}" style="height: 70px;">
        </a>

        <div class="card shadow-lg rounded-4 border-0" style="width: 100%; max-width: 480px;">
            <div class="card-body p-4">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
