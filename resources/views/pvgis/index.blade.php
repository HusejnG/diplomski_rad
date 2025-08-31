<x-app-layout>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Solarni Sistem') }} - PVGIS Kalkulator</title>

    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    
    <!-- Leaflet CSS CDN -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/> 

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            color: #212529;
        }
        .card { border-radius: 0.75rem; box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15); }
        .btn-primary { background-color: #606c38; border-color: #606c38; }
        .btn-primary:hover { background-color: #283618; border-color: #283618; }
        .loading-spinner { display: none; width: 2rem; height: 2rem; border: 0.25em solid currentColor; border-right-color: transparent; border-radius: 50%; animation: .75s linear infinite spinner-border; }
        @keyframes spinner-border { to { transform: rotate(360deg); } }
        #map { height: 400px; width: 100%; border-radius: 0.5rem; margin-bottom: 1.5rem; }
        .carousel-item { display: flex; justify-content: center; }
    </style>
</head>

<body class="d-flex flex-column min-vh-100">

<main class="flex-grow-1 py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card p-4">
                    <h2 class="card-title text-center mb-4 fw-bold">PVGIS Kalkulator</h2>
                    <p class="text-center text-muted mb-4">Unesite detalje ili odaberite lokaciju na mapi za procjenu performansi solarnog sistema.</p>

                    <!-- Map -->
                    <div id="map"></div>

                    <!-- Form -->
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
                        </div>
                        <div class="col-md-6">
                            <label for="systemLoss" class="form-label">Gubitak sistema (%)</label>
                            <input type="number" step="0.1" class="form-control" id="systemLoss" value="14" required>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary w-100" id="calculateBtn">
                                Izračunaj
                                <span class="loading-spinner spinner-border spinner-border-sm ms-2"></span>
                            </button>
                        </div>
                    </form>

                    <!-- Results -->
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
                                <tbody id="resultsTableBody"></tbody>
                            </table>
                        </div>
                        <div id="totalAnnualProduction" class="mt-3 text-center fw-bold fs-5"></div>
                        <div id="messages" class="mt-3 alert alert-info" style="display: none;"></div>
                    </div>

                    <!-- Products -->
                    <div id="productsSuggestions" class="mt-4" style="display: none;">
                        <h5 class="mb-3">Predloženi proizvodi za Vašu snagu:</h5>
                        <div id="productsCarousel" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner" id="productsCarouselInner"></div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#productsCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon"></span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#productsCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon"></span>
                            </button>
                        </div>
                    </div>

                    <!-- Error -->
                    <div id="errorAlert" class="mt-5 alert alert-danger" style="display: none;"></div>

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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Map
    const initialLat = 43.8563;
    const initialLon = 18.4131;
    const map = L.map('map').setView([initialLat, initialLon], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);
    const marker = L.marker([initialLat, initialLon], { draggable: true }).addTo(map);

    function updateCoordinates(lat, lon) {
        document.getElementById('latitude').value = lat.toFixed(4);
        document.getElementById('longitude').value = lon.toFixed(4);
    }
    marker.on('dragend', e => updateCoordinates(e.target.getLatLng().lat, e.target.getLatLng().lng));
    map.on('click', e => { marker.setLatLng(e.latlng); updateCoordinates(e.latlng.lat, e.latlng.lng); });
    updateCoordinates(initialLat, initialLon);

    // Form
    const resultsDiv = document.getElementById('results');
    const resultsTableBody = document.getElementById('resultsTableBody');
    const totalAnnualProductionDiv = document.getElementById('totalAnnualProduction');
    const messagesDiv = document.getElementById('messages');
    const errorAlert = document.getElementById('errorAlert');
    const productsDiv = document.getElementById('productsSuggestions');
    const productsContainer = document.getElementById('productsCarouselInner');
    const calculateBtn = document.getElementById('calculateBtn');
    const loadingSpinner = calculateBtn.querySelector('.loading-spinner');

    document.getElementById('pvgisForm').addEventListener('submit', async function(event) {
        event.preventDefault();

        const latitude = document.getElementById('latitude').value;
        const longitude = document.getElementById('longitude').value;
        const peakPower = document.getElementById('peakPower').value;
        const systemLoss = document.getElementById('systemLoss').value;

        resultsDiv.style.display = 'none';
        productsDiv.style.display = 'none';
        errorAlert.style.display = 'none';
        messagesDiv.style.display = 'none';
        resultsTableBody.innerHTML = '';
        totalAnnualProductionDiv.innerHTML = '';
        messagesDiv.innerHTML = '';
        productsContainer.innerHTML = '';

        loadingSpinner.style.display = 'inline-block';
        calculateBtn.disabled = true;

        try {
            const response = await fetch('{{ route('pvgis.calculate') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ latitude, longitude, peakPower, systemLoss })
            });

            const data = await response.json();

            if (!response.ok) throw new Error(data.error || 'Nepoznata greška');

            if (data.outputs && data.outputs.monthly && Array.isArray(data.outputs.monthly.fixed)) {
                const monthlyData = data.outputs.monthly.fixed;
                let totalAnnualProduction = 0;

                monthlyData.forEach(monthData => {
                    const monthNames = ["Januar","Februar","Mart","April","Maj","Juni","Juli","August","Septembar","Oktobar","Novembar","Decembar"];
                    const monthName = monthNames[monthData.month - 1];
                    const E_d_value = monthData.E_d?.toFixed(2) || '0.00';
                    const E_m_value = monthData.E_m?.toFixed(2) || '0.00';
                    const H_d_value = monthData['H(i)_d']?.toFixed(2) || '0.00';

                    const row = document.createElement('tr');
                    row.innerHTML = `<td>${monthName}</td><td>${E_d_value} kWh</td><td>${E_m_value} kWh</td><td>${H_d_value} kWh/m²</td>`;
                    resultsTableBody.appendChild(row);
                    totalAnnualProduction += monthData.E_m || 0;
                });

                totalAnnualProductionDiv.innerHTML = `Ukupna godišnja proizvodnja: <strong>${totalAnnualProduction.toFixed(2)} kWh</strong>`;
                resultsDiv.style.display = 'block';

                // Products
                try {
                    const prodResp = await fetch(`/products/suggest?peakPower=${peakPower}`);
                    const prodData = await prodResp.json();

                    if (prodData.products.length > 0) {
                        prodData.products.forEach((p, index) => {
                            const itemDiv = document.createElement('div');
                            itemDiv.classList.add('carousel-item');
                            if(index === 0) itemDiv.classList.add('active');
                            itemDiv.innerHTML = `
                                <div class="card p-2 shadow-sm" style="width: 200px;">
                                <a href="/products/${p.id}">
                                    <img src="${p.image_path}" class="card-img-top" alt="${p.name}">
                                    </a>
                                    <div class="card-body p-2 text-center">
                                        <h6 class="card-title mb-1">${p.name}</h6>
                                        <p class="mb-1">${(p.power_w/1000).toFixed(2)} kWp</p>
                                        <p class="text-success fw-bold">${p.price} ${p.currency}</p>
                                    </div>
                                </div>
                            `;
                            productsContainer.appendChild(itemDiv);
                        });
                        productsDiv.style.display = 'block';
                    }
                } catch (err) {
                    console.error('Greška pri učitavanju proizvoda:', err);
                }

                if (data.messages?.length) {
                    messagesDiv.innerHTML = '<ul>' + data.messages.map(msg => `<li>${msg}</li>`).join('') + '</ul>';
                    messagesDiv.style.display = 'block';
                }

            } else {
                errorAlert.innerHTML = 'Nema podataka za prikaz. <pre>' + JSON.stringify(data, null, 2) + '</pre>';
                errorAlert.style.display = 'block';
            }

        } catch (err) {
            console.error(err);
            errorAlert.textContent = 'Došlo je do greške prilikom zahtjeva. Provjerite parametre ili server.';
            errorAlert.style.display = 'block';
        } finally {
            loadingSpinner.style.display = 'none';
            calculateBtn.disabled = false;
        }
    });
});
</script>

</body>
</html>
</x-app-layout>
