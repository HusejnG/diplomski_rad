<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Zahtevi za Ponudu') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <a href="{{ route('quote-requests.create') }}" class="btn btn-primary mb-3">Pošalji novi zahtev za ponudu</a>

                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if ($quoteRequests->isEmpty())
                        <p>Nema dostupnih zahteva za ponudu.</p>
                    @else
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Kontakt Ime</th>
                                    <th>Grad</th>
                                    <th>Potrošnja (kWh/mesec)</th>
                                    <th>Status</th>
                                    <th>Poslato</th>
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
                                        <td><span class="badge bg-{{ $request->status === 'pending' ? 'warning' : ($request->status === 'in_progress' ? 'info' : ($request->status === 'completed' ? 'success' : 'danger')) }}">{{ ucfirst($request->status) }}</span></td>
                                        <td>{{ $request->created_at->format('d.m.Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('quote-requests.show', $request) }}" class="btn btn-info btn-sm">Pregledaj</a>
                                            {{-- Uklonjen uslov za prikaz Izmeni/Obriši dugmadi --}}
                                            <a href="{{ route('quote-requests.edit', $request) }}" class="btn btn-warning btn-sm">Izmeni</a>
                                            <form action="{{ route('quote-requests.destroy', $request) }}" method="POST" style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Da li ste sigurni da želite da obrišete ovaj zahtev?')">Obriši</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
