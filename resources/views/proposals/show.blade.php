<x-app-layout>
    <x-slot name="header">
        <h2 class="fw-bold text-xl text-dark" align="center">
            Detalji Ponude #{{ $proposal->id }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="container">
            {{-- Kartica sa osnovnim podacima ponude --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-success text-white">
                    Osnovni podaci
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-sm-4 fw-semibold">Naslov:</div>
                        <div class="col-sm-8">{{ $proposal->title }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 fw-semibold">Zahtjev za ponudu:</div>
                        <div class="col-sm-8">
                            <a href="{{ route('quote-requests.show', $proposal->quoteRequest) }}" class="text-success text-decoration-none fw-semibold">
                                #{{ $proposal->quoteRequest->id }} ({{ $proposal->quoteRequest->contact_name }})
                            </a>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 fw-semibold">Projektant:</div>
                        <div class="col-sm-8">{{ $proposal->designer->name }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 fw-semibold">Status:</div>
                        <div class="col-sm-8">
                            <span class="badge 
                                {{ $proposal->status === 'draft' ? 'bg-secondary' : 
                                   ($proposal->status === 'sent' ? 'bg-primary' : 
                                   ($proposal->status === 'accepted' ? 'bg-success' : 'bg-danger')) }}">
                                {{ ucfirst($proposal->status) }}
                            </span>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 fw-semibold">Ukupna cijena:</div>
                        <div class="col-sm-8">{{ $proposal->total_price ? $proposal->total_price . ' ' . $proposal->currency : 'N/A' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 fw-semibold">Opis:</div>
                        <div class="col-sm-8">{{ $proposal->description ?? 'Nema opisa.' }}</div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 fw-semibold">Kreirano:</div>
                        <div class="col-sm-8">{{ $proposal->created_at->format('d.m.Y H:i') }}</div>
                    </div>
                </div>
            </div>

            {{-- Tabela proizvoda --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-success text-white">
                    Sastav ponude
                </div>
                <div class="card-body">
                    @if ($proposal->products->isEmpty())
                        <p class="text-muted">Ova ponuda ne sadrži proizvode.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Proizvod</th>
                                        <th>Tip</th>
                                        <th>Količina</th>
                                        <th>Cijena po komadu</th>
                                        <th>Ukupno</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $totalSum = 0; @endphp
                                    @foreach ($proposal->products as $product)
                                        @php
                                            $lineTotal = ($product->pivot->price_at_time_of_proposal ?? 0) * ($product->pivot->quantity ?? 0);
                                            $totalSum += $lineTotal;
                                        @endphp
                                        <tr>
                                            <td>
                                                <a href="{{ route('products.show', $product) }}" class="text-success text-decoration-none fw-semibold">
                                                    {{ $product->name }}
                                                </a>
                                            </td>
                                            <td>{{ ucfirst($product->type ?? 'N/A') }}</td>
                                            <td>{{ $product->pivot->quantity }}</td>
                                            <td>{{ $product->pivot->price_at_time_of_proposal ?? 'N/A' }} {{ $proposal->currency }}</td>
                                            <td>{{ $lineTotal ?? 'N/A' }} {{ $proposal->currency }}</td>
                                        </tr>
                                    @endforeach
                                    <tr class="fw-bold">
                                        <td colspan="4" class="text-end">Ukupno:</td>
                                        <td>{{ $totalSum }} {{ $proposal->currency }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Akcijska dugmad --}}
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('proposals.index') }}" class="btn btn-secondary">Nazad na listu ponuda</a>

                @auth
                    {{-- Dizajner --}}
                    @if(Auth::user()->isDesigner() && Auth::id() === $proposal->designer_id && $proposal->status === 'sent')
                        <a href="{{ route('proposals.edit', $proposal) }}" class="btn btn-warning">Izmijeni</a>
                        <form action="{{ route('proposals.destroy', $proposal) }}" method="POST" class="d-inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Da li ste sigurni da želite da obrišete ovu ponudu?')">Obriši</button>
                        </form>
                    @endif

                    {{-- Klijent --}}
                    @if(Auth::id() === $proposal->quoteRequest->user_id)
                        @if($proposal->status === 'sent')
                            <form action="{{ route('proposals.accept', $proposal) }}" method="POST" class="d-inline-block me-2">
                                @csrf
                                <button type="submit" class="btn btn-success" onclick="return confirm('Da li ste sigurni da želite da prihvatite ovu ponudu?')">Prihvati ponudu</button>
                            </form>
                            <form action="{{ route('proposals.reject', $proposal) }}" method="POST" class="d-inline-block">
                                @csrf
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Da li ste sigurni da želite da odbijete ovu ponudu?')">Odbij ponudu</button>
                            </form>
                        @elseif($proposal->status === 'accepted')
                            <div class="alert alert-success mt-3 w-100">Ovu ponudu ste prihvatili.</div>
                        @elseif($proposal->status === 'rejected')
                            <div class="alert alert-danger mt-3 w-100">Ovu ponudu ste odbili.</div>
                        @endif
                    @endif
                @endauth
            </div>
        </div>
    </div>
</x-app-layout>
