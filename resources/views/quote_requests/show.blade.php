<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detalji Zahteva za Ponudu #') }}{{ $quoteRequest->id }}
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

                        <dt class="col-sm-4">Kontakt Ime:</dt>
                        <dd class="col-sm-8">{{ $quoteRequest->contact_name }}</dd>

                        <dt class="col-sm-4">Kontakt Email:</dt>
                        <dd class="col-sm-8">{{ $quoteRequest->contact_email }}</dd>

                        <dt class="col-sm-4">Kontakt Telefon:</dt>
                        <dd class="col-sm-8">{{ $quoteRequest->contact_phone ?? 'N/A' }}</dd>

                        <dt class="col-sm-4">Adresa:</dt>
                        <dd class="col-sm-8">{{ $quoteRequest->address }}, {{ $quoteRequest->city }}, {{ $quoteRequest->country }}</dd>

                        @if ($quoteRequest->latitude && $quoteRequest->longitude)
                            <dt class="col-sm-4">Koordinate:</dt>
                            <dd class="col-sm-8">{{ $quoteRequest->latitude }}, {{ $quoteRequest->longitude }}</dd>
                        @endif

                        <dt class="col-sm-4">Tip Krova:</dt>
                        <dd class="col-sm-8">{{ $quoteRequest->roof_type ?? 'N/A' }}</dd>

                        <dt class="col-sm-4">Površina Krova (m²):</dt>
                        <dd class="col-sm-8">{{ $quoteRequest->roof_area_sqm ?? 'N/A' }}</dd>

                        <dt class="col-sm-4">Prosečna Mesečna Potrošnja (kWh):</dt>
                        <dd class="col-sm-8">{{ $quoteRequest->avg_monthly_consumption_kwh }} kWh</dd>

                        <dt class="col-sm-4">Napomene:</dt>
                        <dd class="col-sm-8">{{ $quoteRequest->notes ?? 'Nema napomena.' }}</dd>

                        <dt class="col-sm-4">Kreirano:</dt>
                        <dd class="col-sm-8">{{ $quoteRequest->created_at->format('d.m.Y H:i') }}</dd>

                        <dt class="col-sm-4">Poslednja izmena:</dt>
                        <dd class="col-sm-8">{{ $quoteRequest->updated_at->format('d.m.Y H:i') }}</dd>
                    </dl>

                    <div class="mt-4">
                        <a href="{{ route('quote-requests.index') }}" class="btn btn-secondary">Nazad na listu</a>
                        @if ($quoteRequest->status === 'pending')
                            <a href="{{ route('quote-requests.edit', $quoteRequest) }}" class="btn btn-warning">Izmeni Zahtev</a>
                        @endif

                        @can('create-proposal') {{-- Samo projektant može da kreira ponudu --}}
                            @if (!$proposal) {{-- Ako ponuda još ne postoji --}}
                                <a href="{{ route('proposals.create', ['quote_request_id' => $quoteRequest->id]) }}" class="btn btn-success ms-2">Kreiraj Ponudu</a>
                            @else
                                <p class="mt-3 alert alert-info">Ponuda za ovaj zahtev je već kreirana: <a href="{{ route('proposals.show', $proposal) }}">#{{ $proposal->id }}</a></p>
                            @endif
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>