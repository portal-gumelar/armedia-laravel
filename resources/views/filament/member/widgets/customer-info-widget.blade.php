<x-filament-widgets::widget>
    <x-filament::section style="background: linear-gradient(135deg, rgba(16,185,129,0.05) 0%, rgba(5,150,105,0.05) 100%); border-left: 4px solid #10b981; border-radius: 0.75rem; overflow: hidden; transition: all 0.3s ease;" class="hover:shadow-md">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6" style="padding: 0.5rem;">
            
            <!-- Info Utama -->
            <div class="flex items-center gap-4 w-full md:w-auto">
                <div class="h-16 w-16 rounded-full flex items-center justify-center text-white font-bold text-2xl flex-shrink-0" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); box-shadow: 0 4px 10px rgba(16,185,129,0.3);">
                    {{ substr($this->customer->name, 0, 1) }}
                </div>
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-gray-950 dark:text-white" style="margin-bottom: 0.25rem;">
                        Hai, {{ explode(' ', $this->customer->name)[0] }}! 👋
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">
                        ID Pelanggan: <span class="text-emerald-600 dark:text-emerald-400 font-bold bg-emerald-50 dark:bg-emerald-900/30 px-2 py-0.5 rounded">{{ $this->customer->id_arm ?? '-' }}</span>
                    </p>
                    <div class="mt-3 flex flex-wrap items-center gap-2">
                        @if($this->customer->subscription_status->value === 'aktif')
                            <x-filament::badge color="success" icon="heroicon-m-check-circle">Internet Aktif</x-filament::badge>
                        @elseif($this->customer->subscription_status->value === 'isolir')
                            <x-filament::badge color="danger" icon="heroicon-m-x-circle">Terisolir</x-filament::badge>
                        @else
                            <x-filament::badge color="warning" icon="heroicon-m-pause-circle">Berhenti</x-filament::badge>
                        @endif
                        <x-filament::badge color="info" icon="heroicon-m-wifi">
                            {{ $this->customer->internetPackage?->nama_paket ?? ($this->customer->paket_mbps ? $this->customer->paket_mbps . ' Mbps' : 'Paket Belum Diatur') }}
                        </x-filament::badge>
                        
                        @if($this->customer->acrMember)
                            <x-filament::badge color="warning" icon="heroicon-m-star" style="background: linear-gradient(135deg, #fbbf24 0%, #d97706 100%); color: white; border: none; box-shadow: 0 2px 4px rgba(245, 158, 11, 0.3);">
                                {{ number_format($this->customer->acrMember->total_poin, 0, ',', '.') }} Poin ACR ({{ $this->customer->acrMember->level_member }})
                            </x-filament::badge>
                        @else
                            <x-filament::button
                                color="warning"
                                size="xs"
                                tag="button"
                                wire:click="registerAcr"
                                icon="heroicon-m-sparkles"
                                style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; border: none; box-shadow: 0 2px 6px rgba(245, 158, 11, 0.3); transition: transform 0.2s;"
                                onmouseover="this.style.transform='scale(1.05)';" onmouseout="this.style.transform='scale(1)';"
                            >
                                Daftar ACR Rewards
                            </x-filament::button>
                        @endif
                    </div>
                    
                    @if($this->customer->subscription_status->value === 'aktif' && $this->customer->tagihan_bln1_prorata > 0)
                        <div class="mt-3 p-3 rounded-lg border border-indigo-100 bg-indigo-50/50 dark:bg-indigo-900/20 dark:border-indigo-800 flex items-start gap-3">
                            <x-filament::icon
                                icon="heroicon-o-information-circle"
                                class="w-5 h-5 text-indigo-600 dark:text-indigo-400 shrink-0 mt-0.5"
                            />
                            <div>
                                <h4 class="text-sm font-semibold text-indigo-900 dark:text-indigo-300">Info Tagihan Prorata (Bulan Pertama)</h4>
                                <p class="text-xs text-indigo-700 dark:text-indigo-400 mt-1">
                                    Total prorata Anda: <strong>Rp {{ number_format($this->customer->tagihan_bln1_prorata, 0, ',', '.') }}</strong>. Hubungi CS melalui tombol <span class="font-semibold text-warning-600">Info Tagihan</span> untuk rincian lebih lanjut.
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Tombol WA (Multiple Templates) -->
            <div class="w-full md:w-auto flex flex-col sm:flex-row flex-wrap md:justify-end gap-2 mt-4 md:mt-0">
                <!-- Lapor Gangguan -->
                <x-filament::button
                    color="danger"
                    size="sm"
                    icon="heroicon-o-exclamation-triangle"
                    href="https://wa.me/628211234011?text={{ urlencode('Halo CS ARMEDIA, saya ' . $this->customer->name . ' (' . ($this->customer->id_arm ?? '-') . '). Internet saya sedang mengalami kendala/mati. Mohon bantuannya untuk pengecekan.') }}"
                    tag="a" target="_blank"
                    style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; border: none; box-shadow: 0 4px 10px rgba(239, 68, 68, 0.25); transition: all 0.2s ease;"
                    onmouseover="this.style.transform='translateY(-2px)';" onmouseout="this.style.transform='none';"
                >
                    Lapor Gangguan
                </x-filament::button>

                <!-- Info Tagihan -->
                <x-filament::button
                    color="warning"
                    size="sm"
                    icon="heroicon-o-banknotes"
                    href="https://wa.me/628211234011?text={{ urlencode('Halo CS ARMEDIA, saya ' . $this->customer->name . ' (' . ($this->customer->id_arm ?? '-') . '). Saya ingin mengonfirmasi pembayaran / info tagihan saya.') }}"
                    tag="a" target="_blank"
                    style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; border: none; box-shadow: 0 4px 10px rgba(245, 158, 11, 0.25); transition: all 0.2s ease;"
                    onmouseover="this.style.transform='translateY(-2px)';" onmouseout="this.style.transform='none';"
                >
                    Info Tagihan
                </x-filament::button>

                <!-- CS Umum -->
                <x-filament::button
                    color="success"
                    size="sm"
                    icon="heroicon-o-chat-bubble-oval-left-ellipsis"
                    href="https://wa.me/628211234011?text={{ urlencode('Halo CS ARMEDIA, saya ' . $this->customer->name . ' (' . ($this->customer->id_arm ?? '-') . '). Saya memiliki pertanyaan terkait layanan internet.') }}"
                    tag="a" target="_blank"
                    style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none; box-shadow: 0 4px 10px rgba(16, 185, 129, 0.25); transition: all 0.2s ease;"
                    onmouseover="this.style.transform='translateY(-2px)';" onmouseout="this.style.transform='none';"
                >
                    CS Umum
                </x-filament::button>

                <!-- Upgrade Layanan Action -->
                {{ $this->upgradeAction }}
            </div>
            
            <x-filament-actions::modals />

        </div>
    </x-filament::section>
</x-filament-widgets::widget>
