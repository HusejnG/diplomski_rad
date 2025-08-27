<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Solarni Sistem') }} - PVGIS Kalkulator</title>

    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <!-- Leaflet CSS CDN -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
     crossorigin=""/> 

    <!-- Custom CSS for consistency -->
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            color: #212529;
        }
        .navbar-brand {
            font-weight: 700;
            color: #0d6efd !important;
        }
        .card {
            border-radius: 0.75rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        .btn-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
        .btn-primary:hover {
            background-color: #0b5ed7;
            border-color: #0a58ca;
        }
        .loading-spinner {
            display: none;
            width: 2rem;
            height: 2rem;
            border: 0.25em solid currentColor;
            border-right-color: transparent;
            border-radius: 50%;
            animation: .75s linear infinite spinner-border;
        }
        @keyframes spinner-border {
            to { transform: rotate(360deg); }
        }
        /* Style for the map container */
        #map {
            height: 400px; 
            width: 100%;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }
    </style>
    <!-- Fonts (Inter) -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
</head>
<body class="d-flex flex-column min-vh-100">

    <!-- Navigation Header-->
    <header class="navbar navbar-expand-lg navbar-light bg-white shadow-sm py-3">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                {{ config('app.name', 'Solarni Sistem') }}
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @if (Route::has('login'))
                        @auth
                            <li class="nav-item">
                                <a href="{{ url('/dashboard') }}" class="nav-link text-dark">
                                    Dashboard
                                </a>
                            </li>
                        @else
                            <li class="nav-item">
                                <a href="{{ route('login') }}" class="nav-link text-dark">
                                    Prijavi se
                                </a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a href="{{ route('register') }}" class="btn btn-primary rounded-md shadow-sm">
                                        Registruj se
                                    </a>
                                </li>
                            @endif
                        @endauth
                    @endif
                </ul>
            </div>
        </div>
    </header>

    <main class="flex-grow-1 py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card p-4">
                        <h2 class="card-title text-center mb-4 fw-bold">PVGIS Kalkulator</h2>
                        <p class="text-center text-muted mb-4">Unesite detalje ili odaberite lokaciju na mapi za procjenu performansi solarnog sistema.</p>

                        <!-- Map Container -->
                        <div id="map"></div>

                        <form id="pvgisForm" class="row g-3">
                            <div class="col-md-6">
                                <label for="latitude" class="form-label">Geografska širina (Latitude)</label>
                                <input type="number" step="0.0001" class="form-control" id="latitude" value="43.8563" required>
                            </div>
                            <div class="col-md-6">
                                <label for="longitude" class="form-label">Geografska dužina (Longitude)</label>
                                <input type="number" step="0.0001" class="form-control" id="longitude" value="18.4131" required>
                            </div>
                            <div class="col-md-6">
                                <label for="peakPower" class="form-label">Instalirana snaga PV sistema (kWp)</label>
                                <input type="number" step="0.1" class="form-control" id="peakPower" value="1" required>
                                <div class="form-text">Npr. 1 kWp</div>
                            </div>
                            <div class="col-md-6">
                                <label for="systemLoss" class="form-label">Gubitak sistema (%)</label>
                                <input type="number" step="0.1" class="form-control" id="systemLoss" value="14" required>
                                <div class="form-text">Npr. 14% (podrazumijevano)</div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary w-100" id="calculateBtn">
                                    Izračunaj
                                    <span class="loading-spinner spinner-border spinner-border-sm ms-2" role="status" aria-hidden="true"></span>
                                </button>
                            </div>
                        </form>

                        <div id="results" class="mt-5" style="display: none;">
                            <h4 class="mb-3 text-center">Rezultati PVGIS analize</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Mjesec</th>
                                            <th>Prosječna dnevna proizvodnja (kWh)</th>
                                            <th>Prosječna mjesečna proizvodnja (kWh)</th>
                                            <th>Prosječno dnevno zračenje (kWh/m²)</th>
                                        </tr>
                                    </thead>
                                    <tbody id="resultsTableBody">
                                    </tbody>
                                </table>
                            </div>
                            <div id="totalAnnualProduction" class="mt-3 text-center fw-bold fs-5">
                            </div>
                            <div id="messages" class="mt-3 alert alert-info" style="display: none;">
                            </div>
                        </div>

                        <div id="errorAlert" class="mt-5 alert alert-danger" style="display: none;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-dark text-white py-4 text-center mt-auto">
        <div class="container">
            <p class="mb-0">&copy; {{ date('Y') }} {{ config('app.name', 'Solarni Sistem') }}. Sva prava zadržana.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
     crossorigin=""></script> 

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const initialLat = 43.8563;
            const initialLon = 18.4131;
            
            const map = L.map('map').setView([initialLat, initialLon], 13); 

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            const marker = L.marker([initialLat, initialLon], {
                draggable: true
            }).addTo(map);

            function updateCoordinates(lat, lon) {
                document.getElementById('latitude').value = lat.toFixed(4);
                document.getElementById('longitude').value = lon.toFixed(4);
            }

            marker.on('dragend', function(e) {
                const latlng = marker.getLatLng();
                updateCoordinates(latlng.lat, latlng.lng);
            });

            map.on('click', function(e) {
                marker.setLatLng(e.latlng);
                updateCoordinates(e.latlng.lat, e.latlng.lng);
            });

            updateCoordinates(initialLat, initialLon);
        });


        document.getElementById('pvgisForm').addEventListener('submit', async function(event) {
            event.preventDefault(); 

            const latitude = document.getElementById('latitude').value;
            const longitude = document.getElementById('longitude').value;
            const peakPower = document.getElementById('peakPower').value;
            const systemLoss = document.getElementById('systemLoss').value;

            const calculateBtn = document.getElementById('calculateBtn');
            const loadingSpinner = calculateBtn.querySelector('.loading-spinner');
            const resultsDiv = document.getElementById('results');
            const resultsTableBody = document.getElementById('resultsTableBody');
            const totalAnnualProductionDiv = document.getElementById('totalAnnualProduction');
            const messagesDiv = document.getElementById('messages');
            const errorAlert = document.getElementById('errorAlert');

            resultsDiv.style.display = 'none';
            errorAlert.style.display = 'none';
            messagesDiv.style.display = 'none';
            resultsTableBody.innerHTML = '';
            totalAnnualProductionDiv.innerHTML = '';
            messagesDiv.innerHTML = '';

            loadingSpinner.style.display = 'inline-block';
            calculateBtn.disabled = true;

            try {
                const response = await fetch('{{ route('pvgis.calculate') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' 
                    },
                    body: JSON.stringify({
                        latitude: latitude,
                        longitude: longitude,
                        peakPower: peakPower,
                        systemLoss: systemLoss
                    })
                });
                const data = await response.json();

                if (response.ok) {
                    if (data.outputs && data.outputs.monthly && Array.isArray(data.outputs.monthly.fixed)) {
                        let monthlyData = data.outputs.monthly.fixed; 

                        let totalAnnualProduction = 0;
                        monthlyData.forEach(monthData => {
                            const monthNames = ["Januar", "Februar", "Mart", "April", "Maj", "Juni",
                                                "Juli", "August", "Septembar", "Oktobar", "Novembar", "Decembar"];
                            const monthName = monthNames[monthData.month - 1]; 

                            const E_d_value = monthData.E_d !== undefined && monthData.E_d !== null ? monthData.E_d.toFixed(2) : '0.00';
                            const E_m_value = monthData.E_m !== undefined && monthData.E_m !== null ? monthData.E_m.toFixed(2) : '0.00';
                            const H_d_value = monthData['H(i)_d'] !== undefined && monthData['H(i)_d'] !== null ? monthData['H(i)_d'].toFixed(2) : '0.00';

                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${monthName}</td>
                                <td>${E_d_value} kWh</td>
                                <td>${E_m_value} kWh</td>
                                <td>${H_d_value} kWh/m²</td>
                            `;
                            resultsTableBody.appendChild(row);
                            totalAnnualProduction += (monthData.E_m || 0); 
                        });

                        totalAnnualProductionDiv.innerHTML = `Ukupna godišnja proizvodnja: <strong>${totalAnnualProduction.toFixed(2)} kWh</strong>`;
                        resultsDiv.style.display = 'block';

                        if (data.messages && data.messages.length > 0) {
                            messagesDiv.innerHTML = '<strong>Poruke sa PVGIS API-ja:</strong><ul>' + data.messages.map(msg => `<li>${msg}</li>`).join('') + '</ul>';
                            messagesDiv.style.display = 'block';
                        }

                    } else {
                        errorAlert.innerHTML = 'Nema podataka za prikaz. Provjerite ulazne parametre. <br> Odgovor API-ja: <pre>' + JSON.stringify(data, null, 2) + '</pre>';
                        errorAlert.style.display = 'block';
                    }
                } else {
                    errorAlert.textContent = `Greška: ${data.error || 'Nepoznata greška.'} ${data.details ? JSON.stringify(data.details) : ''}`;
                    errorAlert.style.display = 'block';
                }

            } catch (error) {
                console.error('Došlo je do greške:', error);
                errorAlert.textContent = 'Došlo je do greške prilikom obrade zahtjeva. Molimo pokušajte ponovo.';
                errorAlert.style.display = 'block';
            } finally {
                loadingSpinner.style.display = 'none';
                calculateBtn.disabled = false;
            }
        });
    </script>
</body>
</html>