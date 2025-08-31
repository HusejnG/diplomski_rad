<x-app-layout>
    <x-slot name="header">
        <div class="text-center my-5 fade-in-up" style="animation-delay: 0.2s;">
            <h2 class="display-4 fw-bold text-dark">
                <span style="color:#1B512D;">Ponude</span>
            </h2>
            <div style="width:80px; height:4px; background:#B1CF5F; margin:15px auto; border-radius:2px;"></div>
            <p class="lead text-muted mb-5">Pregledajte sve poslane ponude</p>
        </div>
    </x-slot>

    <style>
        .btn-main {
            background-color: #cde0c2ff;
            border-color: #6f8175ff;
            color: #1B512D;
            transition: all 0.3s ease;
        }
        .btn-main:hover {
            background-color: #1B512D;
            color: #DEF4C6;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        .fade-in-up {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.8s forwards;
        }
        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        body {
            background-color: #f5f7fa;
        }
        .card-custom {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card-custom:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.12);
        }
    </style>

    <div class="container">
        <div class="card-custom bg-white p-4 fade-in-up">

            {{-- Alert poruke --}}
            @if (session('success'))
                <div class="alert alert-success rounded-3 shadow-sm mb-3">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger rounded-3 shadow-sm mb-3">
                    {{ session('error') }}
                </div>
            @endif


            @if ($proposals->isEmpty())
                <p class="text-center text-muted">Nema dostupnih ponuda.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Naslov</th>
                                <th>Zahtjev #</th>
                                <th>Kupac</th>
                                <th>Projektant</th>
                                <th>Ukupna cijena</th>
                                <th>Status</th>
                                <th>Kreirano</th>
                                <th>Akcije</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($proposals as $proposal)
                                <tr>
                                    <td>{{ $proposal->id }}</td>
                                    <td>{{ $proposal->title }}</td>
                                    <td>
                                        <a href="{{ route('quote-requests.show', $proposal->quoteRequest) }}">
                                            #{{ $proposal->quoteRequest->id }}
                                        </a>
                                    </td>
                                    <td>{{ $proposal->quoteRequest->user->name }}</td>
                                    <td>{{ $proposal->designer->name }}</td>
                                    <td>{{ $proposal->total_price ? $proposal->total_price . ' ' . $proposal->currency : 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-{{ 
                                            $proposal->status === 'draft' ? 'secondary' : 
                                            ($proposal->status === 'sent' ? 'primary' : 
                                            ($proposal->status === 'accepted' ? 'success' : 'danger')) 
                                        }}">
                                            {{ ucfirst($proposal->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $proposal->created_at->format('d.m.Y H:i') }}</td>
                                    <td class="d-flex flex-wrap gap-1">
                                        <a href="{{ route('proposals.show', $proposal) }}" class="btn btn-main btn-sm rounded-pill">Pregledaj</a>

                                        {{-- Akcijski gumbi --}}
                                        @if(Auth::user()->isAdmin() || (Auth::user()->isDesigner() && Auth::user()->id === $proposal->designer_id))
                                            @if ($proposal->status === 'sent')
                                                <a href="{{ route('proposals.edit', $proposal) }}" class="btn btn-warning btn-sm rounded-pill">Izmijeni</a>
                                            @endif
                                            <form action="{{ route('proposals.destroy', $proposal) }}" method="POST" class="d-inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm rounded-pill" onclick="return confirm('Da li ste sigurni da želite da obrišete ovu ponudu?')">
                                                    Obriši
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
