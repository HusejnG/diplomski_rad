<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
            <div>
                <h2 class="h3 fw-bold mb-1" style="color:#1B512D;">{{ $project->name }}</h2>
                <p class="text-muted mb-0">{{ $project->contact_name }} &middot; {{ $project->contact_email }} @if($project->contact_phone) &middot; {{ $project->contact_phone }} @endif</p>
            </div>
            <x-status-badge :status="$project->status" />
        </div>
    </x-slot>

    <div class="container py-4">
        @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card rounded-4 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h3 class="h6 fw-bold mb-3">Lokacija i površina</h3>
                        <div class="row small">
                            <div class="col-sm-6 d-flex justify-content-between border-bottom py-2"><span>Adresa</span><strong>{{ $project->address ?: '-' }}</strong></div>
                            <div class="col-sm-6 d-flex justify-content-between border-bottom py-2"><span>Koordinate</span><strong>{{ $project->latitude }}, {{ $project->longitude }}</strong></div>
                            <div class="col-sm-6 d-flex justify-content-between border-bottom py-2"><span>Tip površine</span><strong>{{ $project->surface_type_label }}</strong></div>
                            <div class="col-sm-6 d-flex justify-content-between border-bottom py-2"><span>Raspoloživa površina</span><strong>{{ $project->available_area_sqm }} m²</strong></div>
                            <div class="col-sm-6 d-flex justify-content-between border-bottom py-2"><span>Nagib / orijentacija</span><strong>{{ $project->tilt_deg }}° / {{ $project->azimuth_deg }}°</strong></div>
                            <div class="col-sm-6 d-flex justify-content-between border-bottom py-2"><span>Zasjenjenost</span><strong>{{ ucfirst($project->shading) }}</strong></div>
                            <div class="col-sm-6 d-flex justify-content-between py-2"><span>Prosj. mjesečna potrošnja</span><strong>{{ $project->avg_monthly_consumption_kwh }} kWh</strong></div>
                        </div>
                    </div>
                </div>

                @if(in_array($project->status, ['under_review']))
                    <div class="card rounded-4 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h3 class="h6 fw-bold mb-3">Prilagodi predloženi sistem</h3>
                            <form method="POST" action="{{ route('review.updateDesign', $project) }}">
                                @csrf @method('PUT')
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small">Panel</label>
                                        <select name="panel_id" class="form-select form-select-sm">
                                            @foreach($panels as $panel)
                                                <option value="{{ $panel->id }}" {{ $project->panel_id === $panel->id ? 'selected' : '' }}>{{ $panel->label }} - {{ $panel->price_bam }} BAM</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small">Broj panela</label>
                                        <input type="number" min="1" name="panel_quantity" class="form-control form-control-sm" value="{{ $project->panel_quantity }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small">Invertor</label>
                                        <select name="inverter_id" class="form-select form-select-sm">
                                            @foreach($inverters as $inverter)
                                                <option value="{{ $inverter->id }}" {{ $project->inverter_id === $inverter->id ? 'selected' : '' }}>{{ $inverter->label }} - {{ $inverter->price_bam }} BAM</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small">Broj invertora</label>
                                        <input type="number" min="1" name="inverter_quantity" class="form-control form-control-sm" value="{{ $project->inverter_quantity }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small">Cijena struje (BAM/kWh)</label>
                                        <input type="number" step="0.001" name="electricity_price_bam_kwh" class="form-control form-control-sm" value="{{ $project->electricity_price_bam_kwh }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small">Samopotrošnja (%)</label>
                                        <input type="number" step="1" min="0" max="100" name="self_consumption_percent" class="form-control form-control-sm" value="{{ $project->self_consumption_percent }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small">Trošak ugradnje (BAM)</label>
                                        <input type="number" step="1" min="0" name="installation_cost_bam" class="form-control form-control-sm" value="{{ $project->installation_cost_bam }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small">Napomena za korisnika</label>
                                        <textarea name="designer_notes" class="form-control form-control-sm" rows="2">{{ $project->designer_notes }}</textarea>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-outline-primary rounded-pill mt-3">
                                    <i class="bi bi-arrow-repeat me-1"></i> Ponovo izračunaj i sačuvaj
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-5">
                <div class="card rounded-4 shadow-sm mb-4" style="background:#F4F7F2; border:none;">
                    <div class="card-body p-4">
                        <h3 class="h6 fw-bold mb-3">Trenutni sistem i isplativost</h3>
                        <div class="d-flex justify-content-between border-bottom py-2 small"><span>Panel</span><strong>{{ $project->panel?->label }} × {{ $project->panel_quantity }}</strong></div>
                        <div class="d-flex justify-content-between border-bottom py-2 small"><span>Invertor</span><strong>{{ $project->inverter?->label }} × {{ $project->inverter_quantity }}</strong></div>
                        <div class="d-flex justify-content-between border-bottom py-2 small"><span>Snaga</span><strong>{{ $project->system_power_kwp }} kWp</strong></div>
                        <div class="d-flex justify-content-between border-bottom py-2 small"><span>Godišnja proizvodnja</span><strong>{{ number_format($project->annual_production_kwh, 0, ',', '.') }} kWh</strong></div>
                        <div class="d-flex justify-content-between border-bottom py-2 small"><span>Ukupna investicija</span><strong>{{ number_format($project->total_investment_bam, 0, ',', '.') }} BAM</strong></div>
                        <div class="d-flex justify-content-between border-bottom py-2 small"><span>Period povrata</span><strong>{{ $project->simple_payback_years ?? '-' }} god.</strong></div>
                        <div class="d-flex justify-content-between py-2 small"><span>NPV (25 god.)</span><strong>{{ number_format($project->npv_25y_bam, 0, ',', '.') }} BAM</strong></div>
                    </div>
                </div>

                <x-status-timeline :project="$project" />

                <div class="card rounded-4 shadow-sm">
                    <div class="card-body p-4">
                        <h3 class="h6 fw-bold mb-3">Radnje</h3>

                        @if($project->status === 'submitted')
                            <form method="POST" action="{{ route('review.claim', $project) }}">
                                @csrf
                                <button class="btn btn-primary w-100 rounded-pill" type="submit">
                                    <i class="bi bi-hand-index-thumb me-1"></i> Preuzmi u obradu
                                </button>
                            </form>
                        @endif

                        @if($project->status === 'under_review')
                            <form method="POST" action="{{ route('review.approve', $project) }}" class="mb-2">
                                @csrf
                                <button class="btn btn-success w-100 rounded-pill" type="submit">
                                    <i class="bi bi-check-lg me-1"></i> Odobri sistem
                                </button>
                            </form>
                            <form method="POST" action="{{ route('review.reject', $project) }}" onsubmit="return attachReason(this)">
                                @csrf
                                <input type="hidden" name="reason" class="reasonInput">
                                <button class="btn btn-outline-danger w-100 rounded-pill" type="submit">
                                    <i class="bi bi-x-lg me-1"></i> Odbij zahtjev
                                </button>
                            </form>
                        @endif

                        @if($project->status === 'approved')
                            <form method="POST" action="{{ route('review.schedule', $project) }}">
                                @csrf
                                <label class="form-label small">Datum ugradnje</label>
                                <input type="date" name="installation_scheduled_at" class="form-control mb-2" min="{{ now()->toDateString() }}" required>
                                <button class="btn btn-primary w-100 rounded-pill" type="submit">
                                    <i class="bi bi-calendar-check me-1"></i> Zakaži ugradnju
                                </button>
                            </form>
                        @endif

                        @if($project->status === 'scheduled')
                            <form method="POST" action="{{ route('review.complete', $project) }}">
                                @csrf
                                <button class="btn btn-success w-100 rounded-pill" type="submit" onclick="return confirm('Označiti projekat kao završen?')">
                                    <i class="bi bi-flag-fill me-1"></i> Označi kao završeno
                                </button>
                            </form>
                        @endif

                        @if(in_array($project->status, ['completed', 'rejected']))
                            <p class="text-muted small mb-0">Nema dodatnih radnji - projekat je zaključen.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function attachReason(form) {
            const reason = prompt('Unesite razlog odbijanja:');
            if (!reason) return false;
            form.querySelector('.reasonInput').value = reason;
            return true;
        }
    </script>
</x-app-layout>
