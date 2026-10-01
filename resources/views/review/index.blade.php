<x-app-layout>
    <x-slot name="header">
        <h2 class="h3 fw-bold mb-0" style="color:#1B512D;">Obrada narudžbi</h2>
    </x-slot>

    <div class="container py-4">
        <h3 class="h6 fw-bold text-uppercase text-muted mb-3">Nove narudžbe na čekanju ({{ $queue->count() }})</h3>
        @if($queue->isEmpty())
            <p class="text-muted mb-5">Trenutno nema novih narudžbi.</p>
        @else
            <div class="table-responsive bg-white rounded-4 shadow-sm mb-5">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Korisnik</th><th>Lokacija</th><th>Snaga</th><th>Investicija</th><th>Poslano</th><th></th></tr></thead>
                    <tbody>
                        @foreach($queue as $project)
                            <tr>
                                <td>{{ $project->user->name }}</td>
                                <td class="text-muted small">{{ $project->city ?: $project->latitude.', '.$project->longitude }}</td>
                                <td>{{ $project->system_power_kwp }} kWp</td>
                                <td>{{ number_format($project->total_investment_bam, 0, ',', '.') }} BAM</td>
                                <td class="text-muted small">{{ $project->submitted_at?->diffForHumans() }}</td>
                                <td class="text-end">
                                    <a href="{{ route('review.show', $project) }}" class="btn btn-sm btn-primary rounded-pill">Pregledaj</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <h3 class="h6 fw-bold text-uppercase text-muted mb-3">Moji aktivni projekti ({{ $myProjects->count() }})</h3>
        @if($myProjects->isEmpty())
            <p class="text-muted mb-5">Nema aktivnih projekata u obradi.</p>
        @else
            <div class="table-responsive bg-white rounded-4 shadow-sm mb-5">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Korisnik</th><th>Lokacija</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                        @foreach($myProjects as $project)
                            <tr>
                                <td>{{ $project->user->name }}</td>
                                <td class="text-muted small">{{ $project->city ?: $project->latitude.', '.$project->longitude }}</td>
                                <td><x-status-badge :status="$project->status" /></td>
                                <td class="text-end">
                                    <a href="{{ route('review.show', $project) }}" class="btn btn-sm btn-outline-primary rounded-pill">Detalji</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <h3 class="h6 fw-bold text-uppercase text-muted mb-3">Nedavno završeno / odbijeno</h3>
        @if($completed->isEmpty())
            <p class="text-muted">Nema.</p>
        @else
            <div class="table-responsive bg-white rounded-4 shadow-sm">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Korisnik</th><th>Lokacija</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                        @foreach($completed as $project)
                            <tr>
                                <td>{{ $project->user->name }}</td>
                                <td class="text-muted small">{{ $project->city ?: $project->latitude.', '.$project->longitude }}</td>
                                <td><x-status-badge :status="$project->status" /></td>
                                <td class="text-end">
                                    <a href="{{ route('review.show', $project) }}" class="btn btn-sm btn-outline-secondary rounded-pill">Detalji</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-app-layout>
