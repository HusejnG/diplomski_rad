<x-app-layout>
    <x-slot name="header">
        <h2 class="text-center font-bold text-3xl text-dark mb-4" style="font-family: 'Inter', sans-serif;">
            {{ __('Pošalji zahtjev za ponudu') }}
        </h2>
    </x-slot>

    <style>
        .btn-main {
            background-color: #cde0c2ff;
            border-color: #6f8175ff;
            color: #1B512D;
            transition: all 0.3s ease;
        }
        .btn-main:hover {
            background-color: #1B512D;
            color: #DEF4C6;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        .btn-secondary-custom {
            background-color: #e0e0e0;
            border-color: #bdbdbd;
            color: #333;
            transition: all 0.3s ease;
        }
        .btn-secondary-custom:hover {
            background-color: #bdbdbd;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
    </style>

    <div class="py-12" style="background: linear-gradient(135deg, #f5f7fa, #e4ebf0); min-height: 100vh;">
        <div class="container max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="card shadow-lg rounded-4 p-4" style="background-color: #ffffff;">
                <div class="card-body">
                    <form action="{{ route('quote-requests.store') }}" method="POST">
                        @csrf

                        {{-- Uključi formu --}}
                        @include('quote_requests._form')

                        <div class="mt-4 d-flex flex-wrap gap-2">
                            <button type="submit" class="btn btn-main mb-2">
                                <i class="bi bi-send me-1"></i> Pošalji zahtjev
                            </button>
                            <a href="{{ route('quote-requests.index') }}" class="btn btn-secondary-custom mb-2">
                                <i class="bi bi-x-circle me-1"></i> Odustani
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
