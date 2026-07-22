<x-filament-panels::page>
    <div class="p-4 bg-white rounded-xl shadow dark:bg-gray-800">
        <h2 class="text-xl font-bold mb-4 dark:text-white">Pemetaan Geografis ODP & Pelanggan</h2>
        <div class="text-sm text-gray-500 mb-4">
            <span class="inline-block w-3 h-3 bg-blue-600 rounded-full mr-1"></span> ODP
            <span class="inline-block w-3 h-3 bg-green-500 rounded-full mr-1 ml-4"></span> Pelanggan Aktif
            <span class="inline-block w-3 h-3 bg-red-500 rounded-full mr-1 ml-4"></span> Pelanggan Isolir/Off
        </div>

        <div id="map" class="w-full h-[600px] rounded-lg border border-gray-300 dark:border-gray-700"></div>
    </div>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Default center point (Gumelar, Banyumas)
            const map = L.map('map').setView([-7.3695, 108.9959], 12);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            const locationsData = {!! $locations !!};
            let bounds = [];

            locationsData.forEach(loc => {
                if(loc.lat && loc.lng) {
                    let markerColor = 'blue';
                    let title = loc.name;
                    let popupContent = '';

                    if (loc.type === 'odp') {
                        markerColor = 'black'; // ODP
                        popupContent = `<strong>ODP: ${loc.name}</strong><br>Kode: ${loc.code}`;
                    } else {
                        markerColor = loc.status === 'aktif' ? 'green' : 'red';
                        popupContent = `<strong>Pelanggan: ${loc.name}</strong><br>Status: ${loc.status}<br>ODP Induk: ${loc.odp}`;
                    }

                    // Custom marker icon using svg
                    const iconSvg = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="${markerColor}" width="24px" height="24px"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>`;
                    const customIcon = L.divIcon({
                        className: 'custom-icon',
                        html: iconSvg,
                        iconSize: [24, 24],
                        iconAnchor: [12, 24],
                        popupAnchor: [0, -24]
                    });

                    L.marker([loc.lat, loc.lng], {icon: customIcon})
                        .addTo(map)
                        .bindPopup(popupContent);
                        
                    bounds.push([loc.lat, loc.lng]);
                }
            });

            if(bounds.length > 0) {
                map.fitBounds(bounds);
            }
        });
    </script>
</x-filament-panels::page>
