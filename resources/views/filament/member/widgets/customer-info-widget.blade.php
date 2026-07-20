<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex flex-col md:flex-row items-center justify-between gap-6">
            
            <!-- Info Utama -->
            <div class="flex items-center gap-4 w-full md:w-auto">
                <div class="h-16 w-16 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 font-bold text-2xl flex-shrink-0">
                    {{ substr($this->customer->name, 0, 1) }}
                </div>
                <div>
                    <h2 class="text-xl font-bold tracking-tight text-gray-950 dark:text-white">
                        Hai, {{ explode(' ', $this->customer->name)[0] }}! 👋
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        ID Pelanggan: <span class="font-medium text-gray-950 dark:text-gray-300">{{ $this->customer->id_arm ?? '-' }}</span>
                    </p>
                    <div class="mt-2 flex items-center gap-2">
                        @if($this->customer->subscription_status->value === 'aktif')
                            <x-filament::badge color="success">Internet Aktif</x-filament::badge>
                        @else
                            <x-filament::badge color="danger">Terisolir / Nonaktif</x-filament::badge>
                        @endif
                        <x-filament::badge color="info">{{ $this->customer->internetPackage?->nama_paket ?? 'Paket Belum Diatur' }}</x-filament::badge>
                    </div>
                </div>
            </div>

            <!-- Tombol WA -->
            <div class="w-full md:w-auto flex justify-end">
                <x-filament::button
                    color="success"
                    icon="heroicon-o-chat-bubble-oval-left-ellipsis"
                    href="https://wa.me/6281234567890?text={{ urlencode('Halo CS ARMEDIA, saya ' . $this->customer->name . ' (' . $this->customer->id_arm . '), ingin bertanya mengenai layanan internet saya.') }}"
                    tag="a"
                    target="_blank"
                >
                    Hubungi CS (WhatsApp)
                </x-filament::button>
            </div>

        </div>
    </x-filament::section>
</x-filament-widgets::widget>
