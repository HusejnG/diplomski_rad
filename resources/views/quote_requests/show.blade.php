<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detalji zahtjeva za ponudu #') }}{{ $quoteRequest->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <dl class="row mb-4">
                        <dt class="col-sm-4">Status:</dt>
                        <dd class="col-sm-8"><span class="badge bg-{{ $quoteRequest->status === 'pending' ? 'warning' : ($quoteRequest->status === 'in_progress' ? 'info' : ($quoteRequest->status === 'completed' ? 'success' : 'danger')) }}">{{ ucfirst($quoteRequest->status) }}</span></dd>

                        <dt class="col-sm-4">Poslao:</dt>
                        <dd class="col-sm-8">{{ $quoteRequest->user->name }} ({{ $quoteRequest->user->email }})</dd>

                        <dt class="col-sm-4">Kontakt ime:</dt>
                        <dd class="col-sm-8">{{ $quoteRequest->contact_name }}</dd>

                        <dt class="col-sm-4">Kontakt email:</dt>
                        <dd class="col-sm-8">{{ $quoteRequest->contact_email }}</dd>

                        <dt class="col-sm-4">Kontakt telefon:</dt>
                        <dd class="col-sm-8">{{ $quoteRequest->contact_phone ?? 'N/A' }}</dd>

                        <dt class="col-sm-4">Adresa:</dt>
                        <dd class="col-sm-8">{{ $quoteRequest->address }}, {{ $quoteRequest->city }}, {{ $quoteRequest->country }}</dd>

                        @if ($quoteRequest->latitude && $quoteRequest->longitude)
                            <dt class="col-sm-4">Koordinate:</dt>
                            <dd class="col-sm-8">{{ $quoteRequest->latitude }}, {{ $quoteRequest->longitude }}</dd>
                        @endif

                        <dt class="col-sm-4">Tip krova:</dt>
                        <dd class="col-sm-8">{{ $quoteRequest->roof_type ?? 'N/A' }}</dd>

                        <dt class="col-sm-4">Površina krova (m²):</dt>
                        <dd class="col-sm-8">{{ $quoteRequest->roof_area_sqm ?? 'N/A' }}</dd>

                        <dt class="col-sm-4">Prosječna mjesečna potrošnja (kWh):</dt>
                        <dd class="col-sm-8">{{ $quoteRequest->avg_monthly_consumption_kwh }} kWh</dd>

                        <dt class="col-sm-4">Napomene:</dt>
                        <dd class="col-sm-8">{{ $quoteRequest->notes ?? 'Nema napomena.' }}</dd>

                        <dt class="col-sm-4">Kreirano:</dt>
                        <dd class="col-sm-8">{{ $quoteRequest->created_at->format('d.m.Y H:i') }}</dd>

                        <dt class="col-sm-4">Posljednja izmjena:</dt>
                        <dd class="col-sm-8">{{ $quoteRequest->updated_at->format('d.m.Y H:i') }}</dd>
                    </dl>

                    <div class="mt-4">
                        <a href="{{ route('quote-requests.index') }}" class="btn btn-secondary">Nazad na listu</a>
                        @if ($quoteRequest->status === 'pending')
                            <a href="{{ route('quote-requests.edit', $quoteRequest) }}" class="btn btn-warning">Izmijeni zahtjev</a>
                        @endif

                        @if (!$proposal) 
                            <a href="{{ route('proposals.create', ['quote_request_id' => $quoteRequest->id]) }}" class="btn btn-success ms-2">Kreiraj ponudu</a>
                        @else
                            <a href="{{ route('proposals.show', $proposal) }}" class="btn btn-info ms-2">Pogledaj ponudu #{{ $proposal->id }}</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
