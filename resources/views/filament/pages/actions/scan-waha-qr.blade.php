<div x-data="wahaScanner()" x-init="initScanner()" class="flex flex-col items-center justify-center p-4 min-h-[300px]">
    
    <div x-show="status === 'loading'" class="flex flex-col items-center">
        <x-filament::loading-indicator class="h-8 w-8 text-primary-500 mb-4" />
        <p class="text-sm text-gray-500">Memeriksa status WhatsApp...</p>
    </div>

    <div x-show="status === 'qr'" class="flex flex-col items-center">
        <img :src="qrUrl" alt="QR Code WhatsApp" class="w-64 h-64 border rounded-xl shadow-sm mb-4" />
        <p class="text-sm font-medium text-gray-600">Scan QR Code di atas dengan aplikasi WhatsApp.</p>
    </div>

    <div x-show="status === 'connected'" class="flex flex-col items-center text-center">
        <div class="h-16 w-16 bg-success-100 text-success-600 rounded-full flex items-center justify-center mb-4">
            <x-heroicon-o-check-circle class="w-10 h-10" />
        </div>
        <h3 class="text-lg font-bold text-gray-900">WhatsApp Terhubung!</h3>
        <p class="text-sm text-gray-500">Gateway WhatsApp (WAHA) siap mengirim notifikasi.</p>
    </div>

    <div x-show="status === 'error'" class="flex flex-col items-center text-center">
        <div class="h-16 w-16 bg-danger-100 text-danger-600 rounded-full flex items-center justify-center mb-4">
            <x-heroicon-o-x-circle class="w-10 h-10" />
        </div>
        <h3 class="text-lg font-bold text-gray-900">Gagal Terhubung</h3>
        <p class="text-sm text-gray-500" x-text="errorMessage"></p>
        <x-filament::button size="sm" class="mt-4" @click="initScanner()">Coba Lagi</x-filament::button>
    </div>

    <script>
        function wahaScanner() {
            return {
                endpoint: '{{ rtrim($endpoint, '/') }}',
                sessionName: '{{ $session }}',
                status: 'loading', // loading, qr, connected, error
                qrUrl: '',
                errorMessage: '',
                checkInterval: null,

                async initScanner() {
                    this.status = 'loading';
                    try {
                        // 1. Coba check status dulu
                        let res = await fetch(`${this.endpoint}/api/sessions/${this.sessionName}`);
                        
                        if (res.status === 404) {
                            // Sesi belum ada, mari start
                            let startRes = await fetch(`${this.endpoint}/api/sessions/start`, {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify({ name: this.sessionName })
                            });
                            if (!startRes.ok) throw new Error('Gagal memulai sesi WAHA');
                            // Tunggu bentar buat WAHA generate QR
                            await new Promise(r => setTimeout(r, 2000));
                        }

                        // Cek status lagi
                        this.pollStatus();

                    } catch (e) {
                        this.status = 'error';
                        this.errorMessage = e.message || 'Tidak dapat menghubungi server WAHA. Pastikan Endpoint benar.';
                    }
                },

                async pollStatus() {
                    if (this.checkInterval) clearInterval(this.checkInterval);
                    
                    const check = async () => {
                        try {
                            let res = await fetch(`${this.endpoint}/api/sessions/${this.sessionName}`);
                            if (!res.ok) return;
                            let data = await res.json();
                            
                            if (data.status === 'WORKING') {
                                this.status = 'connected';
                                clearInterval(this.checkInterval);
                            } else if (data.status === 'SCAN_QR_CODE') {
                                this.status = 'qr';
                                // tambahkan timestamp agar image refresh
                                this.qrUrl = `${this.endpoint}/api/sessions/${this.sessionName}/auth/qr?t=${new Date().getTime()}`;
                            } else if (data.status === 'STARTING') {
                                this.status = 'loading';
                            }
                        } catch (e) {
                            console.error(e);
                        }
                    };

                    await check();
                    // Poll tiap 3 detik
                    this.checkInterval = setInterval(check, 3000);
                }
            }
        }
    </script>
</div>
