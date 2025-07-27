<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kreiraj Ponudu za Zahtev #') }}{{ $quoteRequest->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('proposals.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="quote_request_id" value="{{ $quoteRequest->id }}">

                        @include('proposals._form', ['proposal' => null, 'products' => $products, 'selectedProducts' => []])

                        <button type="submit" class="btn btn-primary">Kreiraj Ponudu</button>
                        <a href="{{ route('quote-requests.show', $quoteRequest) }}" class="btn btn-secondary">Odustani</a>
                    </form>
                </div>
            </div>
        </div>
    </x-app-layout>