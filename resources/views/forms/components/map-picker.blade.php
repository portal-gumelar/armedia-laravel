<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div
        x-data="mapPicker(
            @js($getState()),
            {{ $getDefaultLat() }},
            {{ $getDefaultLng() }},
            {{ $getZoom() }}
        )"
        x-init="init()"
        wire:ignore
        class="w-full"
    >
        {{-- Peta --}}
        <div x-ref="mapContainer"
             class="w-full rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden"
             style="height: 420px;"></div>

        {{-- Info koordinat --}}
        <div class="flex items-center justify-between mt-2 px-1">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                📍 <span class="font-mono font-medium"
                         x-text="lat !== '' ? lat + ', ' + lng : 'Klik peta atau drag marker untuk pilih lokasi'">
                </span>
            </p>
            <button
                type="button"
                x-on:click="useMyLocation()"
                class="inline-flex items-center gap-1 text-xs font-medium px-3 py-1.5 rounded-lg
                       bg-primary-50 text-primary-700 border border-primary-200
                       hover:bg-primary-100 transition dark:bg-primary-900 dark:text-primary-300
                       dark:border-primary-700"
            >
                🎯 Pakai lokasi GPS saya
            </button>
        </div>
    </div>

    {{-- Alpine Component --}}
    <script>
        function mapPicker(initialState, defaultLat, defaultLng, zoom) {
            return {
                lat: initialState?.lat ?? '',
                lng: initialState?.lng ?? '',
                map: null,
                marker: null,

                init() {
                    // Tentukan titik awal peta
                    const startLat = (this.lat !== '' ? parseFloat(this.lat) : defaultLat);
                    const startLng = (this.lng !== '' ? parseFloat(this.lng) : defaultLng);

                    // Inisiasi peta Leaflet
                    this.map = L.map(this.$refs.mapContainer, {
                        zoomControl: true,
                    }).setView([startLat, startLng], zoom);

                    // Layer peta OpenStreetMap
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                        maxZoom: 19,
                    }).addTo(this.map);

                    // Buat ikon kustom (merah untuk ODP)
                    const odpIcon = L.icon({
                        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                        iconSize: [25, 41],
                        iconAnchor: [12, 41],
                        popupAnchor: [1, -34],
                        shadowSize: [41, 41],
                    });

                    // Tambah marker jika koordinat sudah ada
                    if (this.lat !== '' && this.lng !== '') {
                        this.marker = L.marker([parseFloat(this.lat), parseFloat(this.lng)], {
                            draggable: true,
                            icon: odpIcon,
                        }).addTo(this.map);

                        this.marker.on('dragend', (e) => {
                            const pos = e.target.getLatLng();
                            this.updateCoords(pos.lat, pos.lng);
                        });
                    }

                    // Klik peta → pindahkan/buat marker
                    this.map.on('click', (e) => {
                        if (!this.marker) {
                            this.marker = L.marker(e.latlng, {
                                draggable: true,
                                icon: odpIcon,
                            }).addTo(this.map);

                            this.marker.on('dragend', (ev) => {
                                const pos = ev.target.getLatLng();
                                this.updateCoords(pos.lat, pos.lng);
                            });
                        } else {
                            this.marker.setLatLng(e.latlng);
                        }
                        this.updateCoords(e.latlng.lat, e.latlng.lng);
                    });
                },

                updateCoords(lat, lng) {
                    this.lat = parseFloat(lat).toFixed(7);
                    this.lng = parseFloat(lng).toFixed(7);
                    @this.set('{{ $getStatePath() }}', { lat: this.lat, lng: this.lng });
                },

                useMyLocation() {
                    if (!navigator.geolocation) {
                        alert('Browser Anda tidak mendukung GPS.');
                        return;
                    }
                    navigator.geolocation.getCurrentPosition(
                        (pos) => {
                            const lat = pos.coords.latitude;
                            const lng = pos.coords.longitude;

                            // Arahkan peta ke lokasi GPS
                            this.map.setView([lat, lng], 17);

                            // Pindahkan/buat marker
                            if (!this.marker) {
                                const odpIcon = L.icon({
                                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                                    iconSize: [25, 41],
                                    iconAnchor: [12, 41],
                                    popupAnchor: [1, -34],
                                    shadowSize: [41, 41],
                                });
                                this.marker = L.marker([lat, lng], { draggable: true, icon: odpIcon }).addTo(this.map);
                                this.marker.on('dragend', (e) => {
                                    const pos = e.target.getLatLng();
                                    this.updateCoords(pos.lat, pos.lng);
                                });
                            } else {
                                this.marker.setLatLng([lat, lng]);
                            }
                            this.updateCoords(lat, lng);
                        },
                        (err) => {
                            alert('Tidak bisa mendapatkan lokasi GPS: ' + err.message);
                        },
                        { enableHighAccuracy: true, timeout: 10000 }
                    );
                },
            };
        }
    </script>
</x-dynamic-component>
