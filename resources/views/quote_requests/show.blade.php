<x-app-layout>
    <x-slot name="header">
        <h2 class="text-center font-bold text-3xl text-dark mb-4" style="font-family: 'Inter', sans-serif;">
            {{ __('Detalji zahtjeva za ponudu #') }}{{ $quoteRequest->id }}
        </h2>
    </x-slot>

    <div class="py-12" style="background: linear-gradient(135deg, #f5f7fa, #e4ebf0); min-height: 100vh;">
        <div class="container max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="card shadow-lg rounded-4 p-4" style="background-color: #ffffff;">
                <div class="card-body">

                    <dl class="row mb-4">
                        @php
                            $statusColors = [
                                'pending' => '#f0ad4e',
                                'in_progress' => '#5bc0de',
                                'completed' => '#28a745',
                                'rejected' => '#dc3545'
                            ];
                        @endphp

                        <dt class="col-sm-4 fw-semibold">Status:</dt>
                        <dd class="col-sm-8">
                            <span class="badge rounded-pill text-white px-3 py-2"
                                style="background-color: {{ $statusColors[$quoteRequest->status] ?? '#6c757d' }}">
                                {{ ucfirst($quoteRequest->status) }}
                            </span>
                        </dd>

                        <dt class="col-sm-4 fw-semibold">Poslao:</dt>
                        <dd class="col-sm-8">{{ $quoteRequest->user->name }} ({{ $quoteRequest->user->email }})</dd>

                        <dt class="col-sm-4 fw-semibold">Kontakt ime:</dt>
                        <dd class="col-sm-8">{{ $quoteRequest->contact_name }}</dd>

                        <dt class="col-sm-4 fw-semibold">Kontakt email:</dt>
                        <dd class="col-sm-8">
                            <a href="mailto:{{ $quoteRequest->contact_email }}" class="text-success text-decoration-none">
                                {{ $quoteRequest->contact_email }}
                            </a>
                        </dd>

                        <dt class="col-sm-4 fw-semibold">Kontakt telefon:</dt>
                        <dd class="col-sm-8">{{ $quoteRequest->contact_phone ?? 'N/A' }}</dd>

                        <dt class="col-sm-4 fw-semibold">Adresa:</dt>
                        <dd class="col-sm-8">{{ $quoteRequest->address }}, {{ $quoteRequest->city }}, {{ $quoteRequest->country }}</dd>

                        @if ($quoteRequest->latitude && $quoteRequest->longitude)
                            <dt class="col-sm-4 fw-semibold">Koordinate:</dt>
                            <dd class="col-sm-8">{{ $quoteRequest->latitude }}, {{ $quoteRequest->longitude }}</dd>
                        @endif

                        <dt class="col-sm-4 fw-semibold">Tip krova:</dt>
                        <dd class="col-sm-8">{{ ucfirst($quoteRequest->roof_type ?? 'N/A') }}</dd>

                        <dt class="col-sm-4 fw-semibold">Površina krova (m²):</dt>
                        <dd class="col-sm-8">{{ $quoteRequest->roof_area_sqm ?? 'N/A' }}</dd>

                        <dt class="col-sm-4 fw-semibold">Prosječna mjesečna potrošnja (kWh):</dt>
                        <dd class="col-sm-8">{{ $quoteRequest->avg_monthly_consumption_kwh }} kWh</dd>

                        <dt class="col-sm-4 fw-semibold">Napomene:</dt>
                        <dd class="col-sm-8">{{ $quoteRequest->notes ?? 'Nema napomena.' }}</dd>

                        <dt class="col-sm-4 fw-semibold">Kreirano:</dt>
                        <dd class="col-sm-8">{{ $quoteRequest->created_at->format('d.m.Y H:i') }}</dd>

                        <dt class="col-sm-4 fw-semibold">Posljednja izmjena:</dt>
                        <dd class="col-sm-8">{{ $quoteRequest->updated_at->format('d.m.Y H:i') }}</dd>
                    </dl>

                    <div class="mt-4 d-flex flex-wrap gap-2">
                        <a href="{{ route('quote-requests.index') }}" class="btn btn-outline-secondary mb-2" style="transition: all 0.3s;">
                            <i class="bi bi-arrow-left me-1"></i> Nazad na listu
                        </a>

                        @if (auth()->user()->role === 'customer' && $quoteRequest->status === 'pending')
                            <a href="{{ route('quote-requests.edit', $quoteRequest) }}" class="btn btn-outline-warning mb-2" style="transition: all 0.3s;">
                                <i class="bi bi-pencil-square me-1"></i> Izmijeni zahtjev
                            </a>
                        @endif

                        @if (auth()->user()->role === 'admin' || auth()->user()->role === 'designer')
                            @if (!$proposal)
                                <a href="{{ route('proposals.create', ['quote_request_id' => $quoteRequest->id]) }}" class="btn btn-outline-success mb-2" style="transition: all 0.3s;">
                                    <i class="bi bi-plus-circle me-1"></i> Kreiraj ponudu
                                </a>
                            @else
                                <a href="{{ route('proposals.show', $proposal) }}" class="btn btn-outline-info mb-2" style="transition: all 0.3s;">
                                    <i class="bi bi-eye me-1"></i> Pogledaj ponudu #{{ $proposal->id }}
                                </a>
                            @endif
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
