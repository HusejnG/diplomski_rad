<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
            <div>
                <h2 class="h3 fw-bold mb-1" style="color:#1B512D;">{{ $project->name }}</h2>
                <p class="text-muted mb-0">{{ $project->address ?: ($project->latitude.', '.$project->longitude) }}</p>
            </div>
            <x-status-badge :status="$project->status" />
        </div>
    </x-slot>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    @endpush

    <div class="container py-4">
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="row g-4">
            <div class="col-lg-8">
                <!-- Status tok -->
                <div class="card rounded-4 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h3 class="h6 fw-bold mb-3">Status narudžbe</h3>
                        @php
                            $steps = ['calculated' => 'Izračunato', 'submitted' => 'Poslana narudžba', 'under_review' => 'U obradi', 'approved' => 'Odobreno', 'scheduled' => 'Ugradnja zakazana', 'completed' => 'Završeno'];
                            $order = array_keys($steps);
                            $currentIndex = array_search($project->status, $order);
                        @endphp
                        @if($project->status === 'rejected')
                            <div class="alert alert-danger mb-0">
                                <strong>Zahtjev je odbijen.</strong>
                                @if($project->designer_notes) <div class="mt-1">{{ $project->designer_notes }}</div> @endif
                            </div>
                        @else
                            <div class="d-flex justify-content-between position-relative">
                                @foreach($steps as $key => $label)
                                    <div class="text-center flex-fill">
                                        <div class="mx-auto rounded-circle d-flex align-items-center justify-content-center mb-1"
                                             style="width:32px;height:32px;background-color: {{ $currentIndex !== false && array_search($key, $order) <= $currentIndex ? '#1C7C54' : '#e5e7eb' }}; color:#fff;">
                                            <i class="bi bi-check-lg" style="{{ $currentIndex !== false && array_search($key, $order) <= $currentIndex ? '' : 'opacity:0' }}"></i>
                                        </div>
                                        <div class="small {{ $currentIndex !== false && array_search($key, $order) <= $currentIndex ? 'fw-semibold' : 'text-muted' }}">{{ $label }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if($project->installation_scheduled_at)
                            <div class="alert alert-primary mt-3 mb-0">
                                <i class="bi bi-calendar-event me-1"></i> Ugradnja zakazana za <strong>{{ $project->installation_scheduled_at->translatedFormat('d.m.Y') }}</strong>
                            </div>
                        @endif

                        @if($project->designer_notes && $project->status !== 'rejected')
                            <div class="alert alert-light border mt-3 mb-0">
                                <strong>Napomena projektanta:</strong> {{ $project->designer_notes }}
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Sistem -->
                <div class="card rounded-4 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h3 class="h6 fw-bold mb-3">Predloženi sistem</h3>
                        <div class="row small">
                            <div class="col-sm-6 d-flex justify-content-between border-bottom py-2"><span>Panel</span><strong>{{ $project->panel?->label }} × {{ $project->panel_quantity }}</strong></div>
                            <div class="col-sm-6 d-flex justify-content-between border-bottom py-2"><span>Invertor</span><strong>{{ $project->inverter?->label }} × {{ $project->inverter_quantity }}</strong></div>
                            <div class="col-sm-6 d-flex justify-content-between border-bottom py-2"><span>Snaga sistema</span><strong>{{ $project->system_power_kwp }} kWp</strong></div>
                            <div class="col-sm-6 d-flex justify-content-between border-bottom py-2"><span>Tip površine</span><strong>{{ $project->surface_type_label }}</strong></div>
                            <div class="col-sm-6 d-flex justify-content-between border-bottom py-2"><span>Iskoristiva površina</span><strong>{{ $project->usable_area_sqm }} m²</strong></div>
                            <div class="col-sm-6 d-flex justify-content-between border-bottom py-2"><span>Godišnja proizvodnja</span><strong>{{ number_format($project->annual_production_kwh, 0, ',', '.') }} kWh</strong></div>
                        </div>
                    </div>
                </div>

                <div class="card rounded-4 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h3 class="h6 fw-bold mb-3">Mjesečna proizvodnja energije</h3>
                        <canvas id="monthlyChart" height="180"></canvas>
                    </div>
                </div>

                <div class="card rounded-4 shadow-sm">
                    <div class="card-body p-4">
                        <h3 class="h6 fw-bold mb-3">Kumulativni novčani tok (25 godina)</h3>
                        <canvas id="cashflowChart" height="180"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card rounded-4 shadow-sm mb-4" style="background:#F4F7F2; border:none;">
                    <div class="card-body p-4">
                        <h3 class="h6 fw-bold mb-3">Finansijski pregled</h3>
                        <div class="d-flex justify-content-between border-bottom py-2"><span>Oprema</span><strong>{{ number_format($project->equipment_cost_bam, 0, ',', '.') }} BAM</strong></div>
                        <div class="d-flex justify-content-between border-bottom py-2"><span>Ugradnja</span><strong>{{ number_format($project->installation_cost_bam, 0, ',', '.') }} BAM</strong></div>
                        <div class="d-flex justify-content-between border-bottom py-2"><span>Ukupna investicija</span><strong>{{ number_format($project->total_investment_bam, 0, ',', '.') }} BAM</strong></div>
                        <div class="d-flex justify-content-between border-bottom py-2"><span>Ušteda (1. god.)</span><strong>{{ number_format($project->annual_savings_year1_bam, 0, ',', '.') }} BAM</strong></div>
                        <div class="d-flex justify-content-between border-bottom py-2"><span>Period povrata</span><strong>{{ $project->simple_payback_years ?? '-' }} god.</strong></div>
                        <div class="d-flex justify-content-between py-2"><span>NPV (25 god.)</span><strong>{{ number_format($project->npv_25y_bam, 0, ',', '.') }} BAM</strong></div>
                    </div>
                </div>

                @if($project->isEditableByCustomer())
                    <div class="d-grid gap-2">
                        <form method="POST" action="{{ route('projects.submit', $project) }}">
                            @csrf
                            <button class="btn btn-primary w-100 rounded-pill" type="submit" onclick="return confirm('Poslati narudžbu projektantu?')">
                                <i class="bi bi-send-fill me-1"></i> Naruči ugradnju
                            </button>
                        </form>
                        <a href="{{ route('projects.edit', $project) }}" class="btn btn-outline-secondary rounded-pill">Uredi proračun</a>
                        <form method="POST" action="{{ route('projects.destroy', $project) }}" onsubmit="return confirm('Obrisati ovaj projekat?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-outline-danger w-100 rounded-pill" type="submit">Obriši</button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            const monthly = @json($project->monthly_production ?? []);
            const cashflow = @json($project->cashflow_25y ?? []);
            const monthNames = ['Jan','Feb','Mar','Apr','Maj','Jun','Jul','Avg','Sep','Okt','Nov','Dec'];

            new Chart(document.getElementById('monthlyChart'), {
                type: 'bar',
                data: {
                    labels: monthly.map(m => monthNames[m.month - 1]),
                    datasets: [{ label: 'kWh', data: monthly.map(m => m.e_m), backgroundColor: '#73E2A7', borderRadius: 4, maxBarThickness: 28 }]
                },
                options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid: { color: '#eef2ef' } }, x: { grid: { display: false } } } }
            });

            new Chart(document.getElementById('cashflowChart'), {
                type: 'line',
                data: {
                    labels: cashflow.map(c => 'God. ' + c.year),
                    datasets: [{ label: 'BAM', data: cashflow.map(c => c.cumulative_bam), borderColor: '#1C7C54', backgroundColor: 'rgba(28,124,84,0.12)', fill: true, tension: .25, borderWidth: 2, pointRadius: 0 }]
                },
                options: { plugins: { legend: { display: false } }, scales: { y: { grid: { color: '#eef2ef' } }, x: { grid: { display: false }, ticks: { maxTicksLimit: 8 } } } }
            });
        });
        </script>
    @endpush
</x-app-layout>
