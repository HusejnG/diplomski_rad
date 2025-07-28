<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ponude') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
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

                    @if ($proposals->isEmpty())
                        <p>Nema dostupnih ponuda.</p>
                    @else
                        <table class="table table-striped">
                            <thead>
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
                                        <td><a href="{{ route('quote-requests.show', $proposal->quoteRequest) }}">#{{ $proposal->quoteRequest->id }}</a></td>
                                        <td>{{ $proposal->quoteRequest->user->name }}</td>
                                        <td>{{ $proposal->designer->name }}</td>
                                        <td>{{ $proposal->total_price ? $proposal->total_price . ' ' . $proposal->currency : 'N/A' }}</td>
                                        <td><span class="badge bg-{{ $proposal->status === 'draft' ? 'secondary' : ($proposal->status === 'sent' ? 'primary' : ($proposal->status === 'accepted' ? 'success' : 'danger')) }}">{{ ucfirst($proposal->status) }}</span></td>
                                        <td>{{ $proposal->created_at->format('d.m.Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('proposals.show', $proposal) }}" class="btn btn-info btn-sm">Pregledaj</a>
                                            {{-- Uklonjen @can('manage-proposals', $proposal) --}}
                                            <a href="{{ route('proposals.edit', $proposal) }}" class="btn btn-warning btn-sm">Izmijni</a>
                                            <form action="{{ route('proposals.destroy', $proposal) }}" method="POST" style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Da li ste sigurni da želite da obrišete ovu ponudu?')">Obriši</button>
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
