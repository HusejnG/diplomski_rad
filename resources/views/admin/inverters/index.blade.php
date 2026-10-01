<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h3 fw-bold mb-0" style="color:#1B512D;">Katalog invertora</h2>
            <a href="{{ route('admin.inverters.create') }}" class="btn btn-primary rounded-pill"><i class="bi bi-plus-lg me-1"></i> Dodaj invertor</a>
        </div>
    </x-slot>

    <div class="container py-4">
        <div class="table-responsive bg-white rounded-4 shadow-sm">
            <table class="table align-middle mb-0">
                <thead>
                    <tr><th>Proizvođač / model</th><th>Tip</th><th>Nazivna snaga</th><th>Maks. DC snaga</th><th>Cijena</th><th>Aktivan</th><th></th></tr>
                </thead>
                <tbody>
                    @foreach($inverters as $inverter)
                        <tr>
                            <td>{{ $inverter->manufacturer }} {{ $inverter->model }}</td>
                            <td>{{ ucfirst($inverter->type) }}</td>
                            <td>{{ $inverter->rated_power_kw }} kW</td>
                            <td>{{ $inverter->max_pv_power_kw }} kW</td>
                            <td>{{ number_format($inverter->price_bam, 2, ',', '.') }} BAM</td>
                            <td>@if($inverter->is_active) <span class="badge text-bg-success">Da</span> @else <span class="badge text-bg-secondary">Ne</span> @endif</td>
                            <td class="text-end">
                                <a href="{{ route('admin.inverters.edit', $inverter) }}" class="btn btn-sm btn-outline-primary rounded-pill">Uredi</a>
                                <form method="POST" action="{{ route('admin.inverters.destroy', $inverter) }}" class="d-inline" onsubmit="return confirm('Obrisati ovaj invertor?')">
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
