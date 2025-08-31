<x-app-layout>
    <x-slot name="header">
        
    </x-slot>

    @php
        $action = route('products.update', $product->id);
    @endphp

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        @include('products._form')
                        
                       
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>