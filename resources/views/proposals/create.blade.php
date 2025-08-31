<x-app-layout>
    <x-slot name="header">
        
    </x-slot>

       @php
        $action = route('proposals.store'); // ruta za POST request
        @endphp

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('proposals.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="quote_request_id" value="{{ $quoteRequest->id }}">

                        @include('proposals._form', ['proposal' => null, 'products' => $products, 'selectedProducts' => []])

                    </form>
                </div>
            </div>
        </div>
    </x-app-layout>