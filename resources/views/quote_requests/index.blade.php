<x-app-layout>
    <x-slot name="header">
        <div class="text-center my-5 fade-in-up" style="animation-delay: 0.2s;">
            <h2 class="display-4 fw-bold text-dark">
                <span style="color:#1B512D;">Zahtjevi za ponudu</span>
            </h2>
            <div style="width:80px; height:4px; background:#B1CF5F; margin:15px auto; border-radius:2px;"></div>
            <p class="lead text-muted mb-5">Pregledajte sve poslane zahtjeve</p>
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
        <div class="bg-white shadow-sm rounded-4 p-4 fade-in-up">

            {{-- Dugme za novi zahtjev --}}
            <a href="{{ route('quote-requests.create') }}" class="btn btn-main mb-4 rounded-pill shadow-sm">
                + Pošalji novi zahtjev za ponudu
            </a>

            @if (session('success'))
                <div class="alert alert-success rounded-3 shadow-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger rounded-3 shadow-sm">
                    {{ session('error') }}
                </div>
            @endif

            @if ($quoteRequests->isEmpty())
                <p class="text-center text-muted">Trenutno nema dostupnih zahtjeva za ponudu.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Kontakt Ime</th>
                                <th>Grad</th>
                                <th>Potrošnja (kWh/mjesec)</th>
                                <th>Status</th>
                                <th>Poslano</th>
                                <th>Akcije</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($quoteRequests as $request)
                                <tr>
                                    <td>{{ $request->id }}</td>
                                    <td>{{ $request->contact_name }}</td>
                                    <td>{{ $request->city }}</td>
                                    <td>{{ $request->avg_monthly_consumption_kwh }}</td>
                                    <td>
                                        <span class="badge bg-{{ $request->status === 'pending' ? 'warning' : ($request->status === 'in_progress' ? 'info' : ($request->status === 'completed' ? 'success' : 'danger')) }}">
                                            {{ ucfirst($request->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $request->created_at->format('d.m.Y H:i') }}</td>
                                    <td class="d-flex flex-wrap gap-1">
                                        <a href="{{ route('quote-requests.show', $request) }}" class="btn btn-main btn-sm rounded-pill">Pregledaj</a>

                                        @if (Auth::user()->isDesigner() && !$request->proposal)
                                            <a href="{{ route('proposals.create', ['quote_request_id' => $request->id]) }}" class="btn btn-main btn-sm rounded-pill">Kreiraj ponudu</a>
                                        @endif

                                        @if (!Auth::user()->isDesigner())
                                            @if ($request->status === 'pending')
                                                <a href="{{ route('quote-requests.edit', $request) }}" class="btn btn-warning btn-sm rounded-pill">Izmijeni</a>
                                            @endif
                                            <form action="{{ route('quote-requests.destroy', $request) }}" method="POST" style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm rounded-pill" onclick="return confirm('Da li ste sigurni da želite da obrišete ovaj zahtev?')">Obriši</button>
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
