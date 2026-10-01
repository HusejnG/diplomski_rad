<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h3 fw-bold mb-0" style="color:#1B512D;">Moji projekti</h2>
            <a href="{{ route('calculator.index') }}" class="btn btn-primary rounded-pill">
                <i class="bi bi-plus-lg me-1"></i> Novi proračun
            </a>
        </div>
    </x-slot>

    <div class="container py-4">
        @if($projects->isEmpty())
            <div class="card rounded-4 shadow-sm">
                <div class="card-body p-5 text-center text-muted">
                    <i class="bi bi-sun" style="font-size: 2.5rem; color:#B1CF5F;"></i>
                    <p class="mt-3 mb-3">Još uvijek nemate nijedan solarni projekat.</p>
                    <a href="{{ route('calculator.index') }}" class="btn btn-primary rounded-pill">Napravite prvi proračun</a>
                </div>
            </div>
        @else
            <div class="table-responsive bg-white rounded-4 shadow-sm">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Naziv</th>
                            <th>Lokacija</th>
                            <th>Snaga</th>
                            <th>Investicija</th>
                            <th>Povrat</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($projects as $project)
                            <tr>
                                <td>{{ $project->name }}</td>
                                <td class="text-muted small">{{ $project->city ?: $project->latitude.', '.$project->longitude }}</td>
                                <td>{{ $project->system_power_kwp }} kWp</td>
                                <td>{{ number_format($project->total_investment_bam, 0, ',', '.') }} BAM</td>
                                <td>{{ $project->simple_payback_years ? $project->simple_payback_years.' god.' : '-' }}</td>
                                <td><x-status-badge :status="$project->status" /></td>
                                <td class="text-end">
                                    <a href="{{ route('projects.show', $project) }}" class="btn btn-sm btn-outline-primary rounded-pill">Detalji</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-app-layout>
