<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Izmijeni zahtjev za ponudu #') }}{{ $quoteRequest->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('quote-requests.update', $quoteRequest) }}" method="POST">
                        @csrf
                        @method('PUT')
                        @include('quote_requests._form')
                        <button type="submit" class="btn btn-primary">Ažuriraj zahtjev</button>
                        <a href="{{ route('quote-requests.index') }}" class="btn btn-secondary">Odustani</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>