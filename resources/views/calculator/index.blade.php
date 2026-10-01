<x-app-layout>
    <x-slot name="header">
        <h2 class="h3 fw-bold mb-1" style="color:#1B512D;">
            {{ isset($project) ? 'Uredi proračun' : 'Kalkulator isplativosti solarnog sistema' }}
        </h2>
        <p class="text-muted mb-0">Označite lokaciju, opišite površinu i potrošnju - sistem sam projektuje opremu i računa isplativost.</p>
    </x-slot>

    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
        <style>
            #map { height: 360px; border-radius: .75rem; }
            .surface-card { cursor: pointer; border: 2px solid #e5e7eb; border-radius: .75rem; transition: all .15s ease; height: 100%; }
            .surface-card:hover { border-color: #B1CF5F; }
            .surface-card.selected { border-color: #1C7C54; background-color: #F0F8EC; }
            .surface-card input { position: absolute; opacity: 0; pointer-events: none; }
            .result-card { border: none; border-radius: .75rem; background: #F4F7F2; }
            .kpi { color: #1B512D; }
            .spinner-inline { display: none; width: 1.1rem; height: 1.1rem; border-width: .18rem; }
            #searchResults { position: absolute; z-index: 1000; width: 100%; background: #fff; border: 1px solid #dee2e6; border-top: none; border-radius: 0 0 .5rem .5rem; max-height: 220px; overflow-y: auto; display: none; }
            #searchResults .item { padding: .5rem .75rem; cursor: pointer; }
            #searchResults .item:hover { background: #F0F8EC; }
        </style>
    @endpush

    <div class="container py-4">
        <div class="row g-4">
            <!-- LIJEVA STRANA: FORMA -->
            <div class="col-lg-7">
                <form id="calcForm">
                    @csrf
                    <!-- Lokacija -->
                    <div class="card shadow-sm rounded-4 mb-4">
                        <div class="card-body p-4">
                            <h3 class="h5 fw-bold mb-3"><i class="bi bi-geo-alt-fill me-1" style="color:#1C7C54;"></i> 1. Lokacija</h3>

                            <div class="position-relative mb-3">
                                <label class="form-label">Pretraži adresu ili mjesto</label>
                                <input type="text" id="searchInput" class="form-control" placeholder="npr. Ilidža, Sarajevo">
                                <div id="searchResults"></div>
                            </div>

                            <div id="map" class="mb-3"></div>
                            <p class="text-muted small mb-3">Kliknite na mapu ili prevucite marker da precizno označite lokaciju.</p>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Geografska širina</label>
                                    <input type="number" step="0.000001" id="latitude" name="latitude" class="form-control" value="{{ old('latitude', $project->latitude ?? 43.8563) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Geografska dužina</label>
                                    <input type="number" step="0.000001" id="longitude" name="longitude" class="form-control" value="{{ old('longitude', $project->longitude ?? 18.4131) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Adresa (opcionalno)</label>
                                    <input type="text" id="address" name="address" class="form-control" value="{{ old('address', $project->address ?? '') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Grad (opcionalno)</label>
                                    <input type="text" id="city" name="city" class="form-control" value="{{ old('city', $project->city ?? '') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Povrsina -->
                    <div class="card shadow-sm rounded-4 mb-4">
                        <div class="card-body p-4">
                            <h3 class="h5 fw-bold mb-3"><i class="bi bi-bounding-box me-1" style="color:#1C7C54;"></i> 2. Površina za ugradnju</h3>

                            <div class="row row-cols-2 row-cols-md-3 g-2 mb-3" id="surfaceGrid">
                                @php $selectedSurface = old('surface_type', $project->surface_type ?? 'kosi_krov'); @endphp
                                @foreach($surfaceTypes as $key => $label)
                                    <div class="col">
                                        <label class="surface-card d-block p-3 text-center small fw-semibold {{ $selectedSurface === $key ? 'selected' : '' }}">
                                            <input type="radio" name="surface_type" value="{{ $key }}" {{ $selectedSurface === $key ? 'checked' : '' }}>
                                            <div class="mb-1" style="font-size:1.4rem;">
                                                @switch($key)
                                                    @case('kosi_krov') <i class="bi bi-house-fill"></i> @break
                                                    @case('ravni_krov_kuce') <i class="bi bi-house-door"></i> @break
                                                    @case('krov_zgrade') <i class="bi bi-building"></i> @break
                                                    @case('zemljiste_ravno') <i class="bi bi-map"></i> @break
                                                    @case('zemljiste_brdovito') <i class="bi bi-triangle"></i> @break
                                                    @default <i class="bi bi-grid-3x3-gap"></i>
                                                @endswitch
                                            </div>
                                            {{ $label }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Raspoloživa površina (m²)</label>
                                    <input type="number" step="0.1" min="2" id="available_area_sqm" name="available_area_sqm" class="form-control" value="{{ old('available_area_sqm', $project->available_area_sqm ?? 40) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Zasjenjenost</label>
                                    <select id="shading" name="shading" class="form-select">
                                        <option value="nema" {{ old('shading', $project->shading ?? 'nema') === 'nema' ? 'selected' : '' }}>Nema sjene</option>
                                        <option value="malo" {{ old('shading', $project->shading ?? '') === 'malo' ? 'selected' : '' }}>Malo (drveće/dimnjak)</option>
                                        <option value="srednje" {{ old('shading', $project->shading ?? '') === 'srednje' ? 'selected' : '' }}>Srednje</option>
                                        <option value="mnogo" {{ old('shading', $project->shading ?? '') === 'mnogo' ? 'selected' : '' }}>Mnogo</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mt-3">
                                <a class="small" data-bs-toggle="collapse" href="#advancedSettings" role="button">
                                    <i class="bi bi-sliders"></i> Napredne postavke (nagib, orijentacija, cijena struje)
                                </a>
                                <div class="collapse mt-3" id="advancedSettings">
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <label class="form-label">Nagib panela (°)</label>
                                            <input type="number" step="1" min="0" max="90" id="tilt_deg" name="tilt_deg" class="form-control" value="{{ old('tilt_deg', $project->tilt_deg ?? '') }}" placeholder="auto">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Orijentacija (0=jug)</label>
                                            <input type="number" step="1" min="-180" max="180" id="azimuth_deg" name="azimuth_deg" class="form-control" value="{{ old('azimuth_deg', $project->azimuth_deg ?? 0) }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Cijena struje (BAM/kWh)</label>
                                            <input type="number" step="0.001" min="0.01" id="electricity_price_bam_kwh" name="electricity_price_bam_kwh" class="form-control" value="{{ old('electricity_price_bam_kwh', $project->electricity_price_bam_kwh ?? 0.180) }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Samopotrošnja (%)</label>
                                            <input type="number" step="1" min="0" max="100" id="self_consumption_percent" name="self_consumption_percent" class="form-control" value="{{ old('self_consumption_percent') }}" placeholder="auto">
                                            <div class="form-text">Prazno = procjena na osnovu veličine sistema i potrošnje.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Potrosnja -->
                    <div class="card shadow-sm rounded-4 mb-4">
                        <div class="card-body p-4">
                            <h3 class="h5 fw-bold mb-3"><i class="bi bi-plug-fill me-1" style="color:#1C7C54;"></i> 3. Potrošnja električne energije</h3>
                            <label class="form-label">Prosječna mjesečna potrošnja (kWh)</label>
                            <input type="number" step="1" min="1" id="avg_monthly_consumption_kwh" name="avg_monthly_consumption_kwh" class="form-control" style="max-width: 260px;" value="{{ old('avg_monthly_consumption_kwh', $project->avg_monthly_consumption_kwh ?? 350) }}" required>
                            <div class="form-text">Pogledajte prosjek na Vašim računima za struju u posljednjih 12 mjeseci.</div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill" id="calcBtn">
                        <span class="spinner-border spinner-inline me-2" id="calcSpinner"></span>
                        <i class="bi bi-calculator me-1"></i> Izračunaj sistem i isplativost
                    </button>
                </form>

                <div class="alert alert-danger mt-3 d-none" id="calcError"></div>
            </div>

            <!-- DESNA STRANA: REZULTATI -->
            <div class="col-lg-5">
                <div id="resultsPanel" class="d-none">
                    <div class="card shadow-sm rounded-4 mb-3 result-card">
                        <div class="card-body p-4">
                            <h3 class="h5 fw-bold mb-3">Predloženi sistem</h3>
                            <div id="systemSummary" class="small"></div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <div class="card shadow-sm rounded-4 result-card h-100">
                                <div class="card-body p-3 text-center">
                                    <div class="text-muted small">Ukupna investicija</div>
                                    <div class="h4 fw-bold kpi mb-0" id="kpiInvestment">-</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card shadow-sm rounded-4 result-card h-100">
                                <div class="card-body p-3 text-center">
                                    <div class="text-muted small">Period povrata</div>
                                    <div class="h4 fw-bold kpi mb-0" id="kpiPayback">-</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card shadow-sm rounded-4 result-card h-100">
                                <div class="card-body p-3 text-center">
                                    <div class="text-muted small">Ušteda u 1. godini</div>
                                    <div class="h4 fw-bold kpi mb-0" id="kpiSavings">-</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card shadow-sm rounded-4 result-card h-100">
                                <div class="card-body p-3 text-center">
                                    <div class="text-muted small">NPV (25 god.)</div>
                                    <div class="h4 fw-bold kpi mb-0" id="kpiNpv">-</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm rounded-4 mb-3 result-card">
                        <div class="card-body p-4">
                            <h3 class="h6 fw-bold mb-3">Mjesečna proizvodnja energije</h3>
                            <canvas id="monthlyChart" height="180"></canvas>
                        </div>
                    </div>

                    <div class="card shadow-sm rounded-4 mb-3 result-card">
                        <div class="card-body p-4">
                            <h3 class="h6 fw-bold mb-3">Kumulativni novčani tok (25 godina)</h3>
                            <canvas id="cashflowChart" height="180"></canvas>
                        </div>
                    </div>

                    @auth
                        <div class="card shadow-sm rounded-4 result-card">
                            <div class="card-body p-4">
                                <h3 class="h6 fw-bold mb-3">{{ isset($project) ? 'Ažuriraj projekat' : 'Sačuvaj projekat' }}</h3>
                                <form id="saveForm" method="POST" action="{{ isset($project) ? route('projects.update', $project) : route('projects.store') }}">
                                    @csrf
                                    @if(isset($project)) @method('PUT') @endif

                                    <div class="mb-2">
                                        <label class="form-label small">Naziv projekta</label>
                                        <input type="text" name="name" class="form-control form-control-sm" value="{{ old('name', $project->name ?? '') }}" placeholder="npr. Kuća - Ilidža">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small">Kontakt osoba</label>
                                        <input type="text" name="contact_name" class="form-control form-control-sm" value="{{ old('contact_name', $project->contact_name ?? auth()->user()->name) }}" required>
                                    </div>
                                    <div class="row g-2 mb-3">
                                        <div class="col-6">
                                            <label class="form-label small">Email</label>
                                            <input type="email" name="contact_email" class="form-control form-control-sm" value="{{ old('contact_email', $project->contact_email ?? auth()->user()->email) }}" required>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label small">Telefon</label>
                                            <input type="text" name="contact_phone" class="form-control form-control-sm" value="{{ old('contact_phone', $project->contact_phone ?? '') }}">
                                        </div>
                                    </div>
                                    <input type="hidden" name="hidden_fields_will_be_synced" value="1">
                                    <button type="submit" class="btn btn-outline-primary w-100 rounded-pill">
                                        <i class="bi bi-save2 me-1"></i> {{ isset($project) ? 'Ažuriraj proračun' : 'Sačuvaj proračun' }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="card shadow-sm rounded-4 result-card">
                            <div class="card-body p-4 text-center">
                                <p class="mb-3">Prijavite se da sačuvate proračun i pošaljete narudžbu projektantu jednim klikom.</p>
                                <a href="{{ route('login') }}" class="btn btn-primary rounded-pill me-2">Prijavi se</a>
                                <a href="{{ route('register') }}" class="btn btn-outline-primary rounded-pill">Registruj se</a>
                            </div>
                        </div>
                    @endauth
                </div>

                <div id="resultsPlaceholder" class="card shadow-sm rounded-4 result-card">
                    <div class="card-body p-4 text-center text-muted">
                        <i class="bi bi-sun" style="font-size:2.5rem; color:#B1CF5F;"></i>
                        <p class="mt-3 mb-0">Popunite formu i kliknite "Izračunaj" da vidite predloženi sistem i procjenu isplativosti.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            const latInput = document.getElementById('latitude');
            const lonInput = document.getElementById('longitude');

            const map = L.map('map').setView([parseFloat(latInput.value), parseFloat(lonInput.value)], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);
            const marker = L.marker([parseFloat(latInput.value), parseFloat(lonInput.value)], { draggable: true }).addTo(map);

            function setCoords(lat, lon) {
                latInput.value = Number(lat).toFixed(6);
                lonInput.value = Number(lon).toFixed(6);
                marker.setLatLng([lat, lon]);
            }
            marker.on('dragend', e => setCoords(e.target.getLatLng().lat, e.target.getLatLng().lng));
            map.on('click', e => setCoords(e.latlng.lat, e.latlng.lng));

            // Pretraga lokacije (Nominatim / OpenStreetMap)
            const searchInput = document.getElementById('searchInput');
            const searchResults = document.getElementById('searchResults');
            let searchTimeout;

            searchInput.addEventListener('input', function () {
                clearTimeout(searchTimeout);
                const q = this.value.trim();
                if (q.length < 3) { searchResults.style.display = 'none'; return; }

                searchTimeout = setTimeout(async () => {
                    try {
                        const resp = await fetch(`https://nominatim.openstreetmap.org/search?format=json&limit=5&q=${encodeURIComponent(q)}`);
                        const data = await resp.json();
                        searchResults.innerHTML = '';
                        if (!data.length) { searchResults.style.display = 'none'; return; }

                        data.forEach(place => {
                            const div = document.createElement('div');
                            div.className = 'item';
                            div.textContent = place.display_name;
                            div.addEventListener('click', () => {
                                setCoords(place.lat, place.lon);
                                map.setView([place.lat, place.lon], 15);
                                document.getElementById('address').value = place.display_name.split(',').slice(0, 2).join(',');
                                const parts = place.display_name.split(',');
                                document.getElementById('city').value = parts.length > 2 ? parts[parts.length - 3].trim() : '';
                                searchInput.value = place.display_name;
                                searchResults.style.display = 'none';
                            });
                            searchResults.appendChild(div);
                        });
                        searchResults.style.display = 'block';
                    } catch (e) { console.error('Greška pretrage lokacije', e); }
                }, 450);
            });
            document.addEventListener('click', e => {
                if (!searchResults.contains(e.target) && e.target !== searchInput) searchResults.style.display = 'none';
            });

            // Odabir tipa povrsine
            document.querySelectorAll('.surface-card').forEach(card => {
                card.addEventListener('click', () => {
                    document.querySelectorAll('.surface-card').forEach(c => c.classList.remove('selected'));
                    card.classList.add('selected');
                    card.querySelector('input').checked = true;
                });
            });

            // Formatiranje
            const bam = n => new Intl.NumberFormat('bs-BA', { maximumFractionDigits: 0 }).format(n) + ' BAM';
            const monthNames = ['Jan','Feb','Mar','Apr','Maj','Jun','Jul','Avg','Sep','Okt','Nov','Dec'];

            let monthlyChart = null, cashflowChart = null;

            function renderCharts(production, cashflow) {
                const mCtx = document.getElementById('monthlyChart');
                if (monthlyChart) monthlyChart.destroy();
                monthlyChart = new Chart(mCtx, {
                    type: 'bar',
                    data: {
                        labels: production.monthly.map(m => monthNames[m.month - 1]),
                        datasets: [{
                            label: 'Proizvodnja (kWh)',
                            data: production.monthly.map(m => m.e_m),
                            backgroundColor: '#73E2A7',
                            borderRadius: 4,
                            maxBarThickness: 28,
                        }]
                    },
                    options: {
                        plugins: { legend: { display: false } },
                        scales: { y: { beginAtZero: true, grid: { color: '#eef2ef' } }, x: { grid: { display: false } } }
                    }
                });

                const cCtx = document.getElementById('cashflowChart');
                if (cashflowChart) cashflowChart.destroy();
                cashflowChart = new Chart(cCtx, {
                    type: 'line',
                    data: {
                        labels: cashflow.map(c => 'God. ' + c.year),
                        datasets: [{
                            label: 'Kumulativni tok (BAM)',
                            data: cashflow.map(c => c.cumulative_bam),
                            borderColor: '#1C7C54',
                            backgroundColor: 'rgba(28,124,84,0.12)',
                            fill: true,
                            tension: 0.25,
                            borderWidth: 2,
                            pointRadius: 0,
                        }]
                    },
                    options: {
                        plugins: { legend: { display: false } },
                        scales: { y: { grid: { color: '#eef2ef' } }, x: { grid: { display: false }, ticks: { maxTicksLimit: 8 } } }
                    }
                });
            }

            document.getElementById('calcForm').addEventListener('submit', async function (e) {
                e.preventDefault();
                const btn = document.getElementById('calcBtn');
                const spinner = document.getElementById('calcSpinner');
                const errorBox = document.getElementById('calcError');
                errorBox.classList.add('d-none');
                btn.disabled = true;
                spinner.style.display = 'inline-block';

                const formData = new FormData(this);
                const payload = Object.fromEntries(formData.entries());

                try {
                    const resp = await fetch('{{ route('calculator.calculate') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(payload),
                    });
                    const data = await resp.json();
                    if (!resp.ok) throw new Error(data.error || (data.errors ? Object.values(data.errors).flat().join(' ') : 'Greška u proračunu.'));

                    document.getElementById('resultsPlaceholder').classList.add('d-none');
                    document.getElementById('resultsPanel').classList.remove('d-none');

                    document.getElementById('systemSummary').innerHTML = `
                        <div class="d-flex justify-content-between border-bottom py-2"><span>Panel</span><strong>${data.system.panel.manufacturer} ${data.system.panel.model} × ${data.system.panel_quantity}</strong></div>
                        <div class="d-flex justify-content-between border-bottom py-2"><span>Invertor</span><strong>${data.system.inverter.manufacturer} ${data.system.inverter.model} × ${data.system.inverter_quantity}</strong></div>
                        <div class="d-flex justify-content-between border-bottom py-2"><span>Snaga sistema</span><strong>${data.system.system_power_kwp.toFixed(2)} kWp</strong></div>
                        <div class="d-flex justify-content-between border-bottom py-2"><span>Iskoristiva površina</span><strong>${data.system.usable_area_sqm.toFixed(1)} m²</strong></div>
                        <div class="d-flex justify-content-between py-2"><span>Godišnja proizvodnja</span><strong>${Math.round(data.production.annual_kwh).toLocaleString('bs-BA')} kWh</strong></div>
                    `;

                    document.getElementById('kpiInvestment').textContent = bam(data.system.total_investment_bam);
                    document.getElementById('kpiPayback').textContent = data.financials.simple_payback_years ? data.financials.simple_payback_years + ' god.' : 'preko 25 god.';
                    document.getElementById('kpiSavings').textContent = bam(data.financials.annual_savings_year1_bam);
                    document.getElementById('kpiNpv').textContent = bam(data.financials.npv_25y_bam);

                    renderCharts(data.production, data.financials.cashflow);

                    window.lastCalculation = payload;
                } catch (err) {
                    errorBox.textContent = err.message;
                    errorBox.classList.remove('d-none');
                } finally {
                    btn.disabled = false;
                    spinner.style.display = 'none';
                }
            });

            // Sinhronizuj sva polja forme za proracun u formu za snimanje prije slanja
            const saveForm = document.getElementById('saveForm');
            if (saveForm) {
                saveForm.addEventListener('submit', function () {
                    const calcForm = document.getElementById('calcForm');
                    new FormData(calcForm).forEach((value, key) => {
                        if (saveForm.querySelector(`[name="${key}"]`)) return;
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = key;
                        input.value = value;
                        saveForm.appendChild(input);
                    });
                });
            }

            @if(isset($project))
                document.getElementById('calcForm').dispatchEvent(new Event('submit'));
            @endif
        });
        </script>
    @endpush
</x-app-layout>
