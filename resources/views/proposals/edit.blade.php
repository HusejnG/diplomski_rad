<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Izmijeni ponudu #') }}{{ $proposal->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('proposals.update', $proposal) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="quote_request_id" value="{{ $proposal->quote_request_id }}">

                        @include('proposals._form', ['proposal' => $proposal, 'products' => $products, 'selectedProducts' => $selectedProducts])

                        <button type="submit" class="btn btn-primary">Ažuriraj ponudu</button>
                        <a href="{{ route('proposals.show', $proposal) }}" class="btn btn-secondary">Odustani</a>
                    </form>
                </div>
            </div>
        </div>
    </x-app-layout>