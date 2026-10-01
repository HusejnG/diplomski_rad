<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h3 fw-bold mb-0" style="color:#1B512D;">Katalog panela</h2>
            <a href="{{ route('admin.panels.create') }}" class="btn btn-primary rounded-pill"><i class="bi bi-plus-lg me-1"></i> Dodaj panel</a>
        </div>
    </x-slot>

    <div class="container py-4">
        <div class="table-responsive bg-white rounded-4 shadow-sm">
            <table class="table align-middle mb-0">
                <thead>
                    <tr><th>Proizvođač / model</th><th>Snaga</th><th>Efikasnost</th><th>Cijena</th><th>Garancija</th><th>Aktivan</th><th></th></tr>
                </thead>
                <tbody>
                    @foreach($panels as $panel)
                        <tr>
                            <td>{{ $panel->manufacturer }} {{ $panel->model }}</td>
                            <td>{{ $panel->power_w }} W</td>
                            <td>{{ $panel->efficiency_percent }}%</td>
                            <td>{{ number_format($panel->price_bam, 2, ',', '.') }} BAM</td>
                            <td>{{ $panel->warranty_years }} god.</td>
                            <td>@if($panel->is_active) <span class="badge text-bg-success">Da</span> @else <span class="badge text-bg-secondary">Ne</span> @endif</td>
                            <td class="text-end">
                                <a href="{{ route('admin.panels.edit', $panel) }}" class="btn btn-sm btn-outline-primary rounded-pill">Uredi</a>
                                <form method="POST" action="{{ route('admin.panels.destroy', $panel) }}" class="d-inline" onsubmit="return confirm('Obrisati ovaj panel?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger rounded-pill">Obriši</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
