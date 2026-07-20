<x-filament-panels::page>

    {{-- Stats Bar --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center text-2xl">📡</div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Total ODP</p>
                <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $this->getTotalOdp() }}</p>
                <p class="text-xs text-gray-400">{{ $this->getOdpWithCoords() }} sudah ada koordinat</p>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center text-2xl">👥</div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Pelanggan Aktif</p>
                <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $this->getTotalCustomer() }}</p>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900 rounded-lg flex items-center justify-center text-2xl">🗺️</div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Coverage Map</p>
                <p class="text-sm font-semibold text-amber-600 dark:text-amber-400">OpenStreetMap + Leaflet</p>
                <p class="text-xs text-gray-400">Gratis, tanpa API Key</p>
            </div>
        </div>
    </div>

    {{-- Map Container --}}
    <div
        x-data="petaCoverage(@js($this->getOdps()), @js($this->getCustomers()))"
        x-init="init()"
        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden"
    >
        {{-- Map Toolbar --}}
        <div class="flex items-center justify-between p-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1 text-xs font-medium px-2 py-1 bg-red-100 text-red-700 rounded-full">
                    <span class="w-2 h-2 bg-red-500 rounded-full inline-block"></span> ODP Penuh
                </span>
                <span class="inline-flex items-center gap-1 text-xs font-medium px-2 py-1 bg-green-100 text-green-700 rounded-full">
                    <span class="w-2 h-2 bg-green-500 rounded-full inline-block"></span> ODP Tersedia
                </span>
                <span class="inline-flex items-center gap-1 text-xs font-medium px-2 py-1 bg-gray-100 text-gray-700 rounded-full">
                    <span class="w-2 h-2 bg-gray-400 rounded-full inline-block"></span> Belum ada koordinat
                </span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs text-gray-500" x-text="odps.length + ' ODP dipetakan'"></span>
                <button
                    type="button"
                    x-on:click="centerMap()"
                    class="text-xs font-medium px-3 py-1.5 rounded-lg bg-primary-50 text-primary-700 border border-primary-200 hover:bg-primary-100 dark:bg-primary-900 dark:text-primary-300 dark:border-primary-700 transition"
                >
                    🎯 Reset View
                </button>
            </div>
        </div>

        {{-- Peta --}}
        <div x-ref="mapEl" style="height: 580px; width: 100%;"></div>

        {{-- Info panel (muncul saat klik marker) --}}
        <div
            x-show="selectedOdp !== null"
            x-transition
            class="p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900"
        >
            <template x-if="selectedOdp">
                <div class="flex flex-wrap items-start gap-4">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide font-semibold">Kode ODP</p>
                        <p class="text-lg font-bold text-gray-800 dark:text-white" x-text="selectedOdp.code"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide font-semibold">Lokasi</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300" x-text="selectedOdp.alamat"></p>
                    </div>
                    <div class="flex gap-4">
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide font-semibold">Kapasitas Port</p>
                            <p class="text-lg font-bold" x-text="selectedOdp.terpakai + ' / ' + selectedOdp.kapasitas"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide font-semibold">Sisa Slot</p>
                            <p class="text-lg font-bold"
                               :class="selectedOdp.sisa > 0 ? 'text-green-600' : 'text-red-600'"
                               x-text="selectedOdp.sisa + ' port'">
                            </p>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide font-semibold">Status</p>
                        <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium"
                              :class="selectedOdp.sisa > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
                              x-text="selectedOdp.status">
                        </span>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide font-semibold">Koordinat</p>
                        <p class="text-xs font-mono text-gray-500" x-text="selectedOdp.latitude + ', ' + selectedOdp.longitude"></p>
                    </div>
                    <button
                        type="button"
                        x-on:click="selectedOdp = null"
                        class="ml-auto text-xs text-gray-400 hover:text-gray-600"
                    >✕ Tutup</button>
                </div>
            </template>
        </div>
    </div>

    {{-- List ODP tanpa koordinat --}}
    @php
        $odpTanpaKoord = \App\Models\Odp::whereNull('latitude')->orWhereNull('longitude')->get();
    @endphp
    @if($odpTanpaKoord->count() > 0)
    <div class="mt-4 bg-amber-50 dark:bg-amber-950 border border-amber-200 dark:border-amber-800 rounded-xl p-4">
        <p class="text-sm font-semibold text-amber-800 dark:text-amber-200 mb-2">
            ⚠️ {{ $odpTanpaKoord->count() }} ODP belum memiliki koordinat — tidak tampil di peta
        </p>
        <div class="flex flex-wrap gap-2">
            @foreach($odpTanpaKoord as $odp)
                <a href="{{ route('filament.admin.resources.odps.edit', $odp) }}"
                   class="text-xs bg-amber-100 dark:bg-amber-900 text-amber-700 dark:text-amber-300 px-2 py-1 rounded-lg hover:bg-amber-200 transition font-mono">
                    {{ $odp->code }}
                </a>
            @endforeach
        </div>
        <p class="text-xs text-amber-600 dark:text-amber-400 mt-2">
            Klik nama ODP di atas untuk membuka form edit dan tambah koordinat (ada peta picker untuk klik langsung).
        </p>
    </div>
    @endif

    <script>
    function petaCoverage(odps, customers) {
        return {
            odps: odps,
            customers: customers,
            map: null,
            markers: [],
            selectedOdp: null,

            init() {
                // Tentukan center dari rata-rata ODP yang ada
                let centerLat = -7.5083, centerLng = 108.7871;
                if (odps.length > 0) {
                    centerLat = odps.reduce((a, b) => a + b.latitude, 0) / odps.length;
                    centerLng = odps.reduce((a, b) => a + b.longitude, 0) / odps.length;
                }

                this.map = L.map(this.$refs.mapEl, {
                    zoomControl: true,
                    attributionControl: true,
                }).setView([centerLat, centerLng], 13);

                // Base layer OpenStreetMap
                const osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                    maxZoom: 19,
                });

                // Satellite layer (ESRI)
                const satellite = L.tileLayer(
                    'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
                    { attribution: 'Tiles &copy; Esri', maxZoom: 19 }
                );

                osm.addTo(this.map);
                L.control.layers({ 'Peta Jalan': osm, 'Satelit': satellite }).addTo(this.map);

                // Plot semua ODP
                this.odps.forEach(odp => {
                    const color = odp.sisa > 0 ? 'green' : 'red';
                    const icon = L.icon({
                        iconUrl: `https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-${color}.png`,
                        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                        iconSize: [25, 41],
                        iconAnchor: [12, 41],
                        popupAnchor: [1, -34],
                        shadowSize: [41, 41],
                    });

                    const marker = L.marker([odp.latitude, odp.longitude], { icon })
                        .addTo(this.map)
                        .bindTooltip(`<b>${odp.code}</b><br>${odp.terpakai}/${odp.kapasitas} port`, {
                            permanent: false,
                            direction: 'top',
                        });

                    marker.on('click', () => {
                        this.selectedOdp = odp;
                    });

                    this.markers.push(marker);
                });

                // Fit bounds ke semua ODP
                if (this.markers.length > 0) {
                    const group = new L.featureGroup(this.markers);
                    this.map.fitBounds(group.getBounds().pad(0.15));
                }
            },

            centerMap() {
                if (this.markers.length > 0) {
                    const group = new L.featureGroup(this.markers);
                    this.map.fitBounds(group.getBounds().pad(0.15));
                }
            }
        };
    }
    </script>

</x-filament-panels::page>
