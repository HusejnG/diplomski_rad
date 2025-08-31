<x-app-layout>
    <x-slot name="header">
        
    </x-slot>

    @php
        $action = route('proposals.update', $proposal); // ruta za PUT request
    @endphp

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('proposals.update', $proposal) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="quote_request_id" value="{{ $proposal->quote_request_id }}">

                        @include('proposals._form', ['proposal' => $proposal, 'products' => $products, 'selectedProducts' => $selectedProducts])


                    </form>
                </div>
            </div>
        </div>
    </x-app-layout>