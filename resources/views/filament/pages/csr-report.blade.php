<x-filament-panels::page>

    @php
        $byVillage = $this->getLiveDataByVillage();
        $liveData  = $this->getLiveData();
        $totalCustomer = $liveData->sum('customer_count');
        $totalCsr      = $liveData->sum('csr_total');
        $totalDesa     = $liveData->sum('desa_share');
        $totalRt       = $liveData->sum('rt_share');
    @endphp

    {{-- ── SUMMARY CARDS ─────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 gap-4 md:grid-cols-4 print:grid-cols-4 mb-6">
        <div class="rounded-xl bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-700 p-4">
            <p class="text-xs font-medium text-primary-600 dark:text-primary-400 uppercase tracking-wider">Total Pelanggan Aktif</p>
            <p class="mt-1 text-3xl font-bold text-primary-700 dark:text-primary-300">{{ number_format($totalCustomer) }}</p>
            <p class="text-xs text-gray-500 mt-1">Pelanggan</p>
        </div>
        <div class="rounded-xl bg-success-50 dark:bg-success-900/20 border border-success-200 dark:border-success-700 p-4">
            <p class="text-xs font-medium text-success-600 dark:text-success-400 uppercase tracking-wider">Total CSR Bulan Ini</p>
            <p class="mt-1 text-2xl font-bold text-success-700 dark:text-success-300">Rp {{ number_format($totalCsr) }}</p>
            <p class="text-xs text-gray-500 mt-1">@ Rp 3.000/pelanggan</p>
        </div>
        <div class="rounded-xl bg-info-50 dark:bg-info-900/20 border border-info-200 dark:border-info-700 p-4">
            <p class="text-xs font-medium text-info-600 dark:text-info-400 uppercase tracking-wider">Bagian Desa</p>
            <p class="mt-1 text-2xl font-bold text-info-700 dark:text-info-300">Rp {{ number_format($totalDesa) }}</p>
            <p class="text-xs text-gray-500 mt-1">@ Rp 1.000/pelanggan</p>
        </div>
        <div class="rounded-xl bg-warning-50 dark:bg-warning-900/20 border border-warning-200 dark:border-warning-700 p-4">
            <p class="text-xs font-medium text-warning-600 dark:text-warning-400 uppercase tracking-wider">Bagian RT</p>
            <p class="mt-1 text-2xl font-bold text-warning-700 dark:text-warning-300">Rp {{ number_format($totalRt) }}</p>
            <p class="text-xs text-gray-500 mt-1">@ Rp 2.000/pelanggan</p>
        </div>
    </div>

    {{-- ── PER-DESA ACCORDION ─────────────────────────────────────────── --}}
    @if($byVillage->isEmpty())
        <x-filament::section>
            <div class="flex flex-col items-center justify-center py-12 text-gray-400 dark:text-gray-600">
                <x-heroicon-o-building-office-2 class="h-12 w-12 mb-3"/>
                <p class="font-semibold">Belum ada data pelanggan aktif</p>
                <p class="text-sm">Pastikan sudah ada pelanggan dengan status <strong>Aktif</strong> dan memiliki Desa yang terisi.</p>
            </div>
        </x-filament::section>
    @else
        {{-- ── REKAP PER DESA (Summary) ─────────────────────────────────── --}}
        <x-filament::section>
            <x-slot name="heading">Rekap per Desa – {{ $this->getPeriodLabel() }}</x-slot>
            <x-slot name="description">Data dihitung secara <em>real-time</em> dari pelanggan aktif saat ini.</x-slot>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b-2 border-gray-200 dark:border-gray-700 text-xs uppercase tracking-wider text-gray-500">
                            <th class="text-left py-3 px-4 font-semibold">Desa</th>
                            <th class="text-center py-3 px-4 font-semibold">Jumlah RT</th>
                            <th class="text-right py-3 px-4 font-semibold">Pelanggan</th>
                            <th class="text-right py-3 px-4 font-semibold">Total CSR</th>
                            <th class="text-right py-3 px-4 font-semibold">Bagian Desa</th>
                            <th class="text-right py-3 px-4 font-semibold">Bagian RT</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($byVillage as $village)
                        <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/40 transition-colors">
                            <td class="py-3 px-4">
                                <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $village->village_name }}</span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="inline-flex items-center justify-center rounded-full bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 text-xs font-medium px-2 py-0.5">
                                    {{ $village->rt_count }} RT
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right font-medium">{{ number_format($village->customer_count) }}</td>
                            <td class="py-3 px-4 text-right font-semibold text-success-600 dark:text-success-400">Rp {{ number_format($village->csr_total) }}</td>
                            <td class="py-3 px-4 text-right text-info-600 dark:text-info-400">Rp {{ number_format($village->desa_share) }}</td>
                            <td class="py-3 px-4 text-right text-warning-600 dark:text-warning-400">Rp {{ number_format($village->rt_share) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="border-t-2 border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800/60">
                            <td colspan="2" class="py-3 px-4 font-bold text-gray-700 dark:text-gray-300">GRAND TOTAL</td>
                            <td class="py-3 px-4 text-right font-bold">{{ number_format($totalCustomer) }}</td>
                            <td class="py-3 px-4 text-right font-bold text-success-700 dark:text-success-300">Rp {{ number_format($totalCsr) }}</td>
                            <td class="py-3 px-4 text-right font-bold text-info-700 dark:text-info-300">Rp {{ number_format($totalDesa) }}</td>
                            <td class="py-3 px-4 text-right font-bold text-warning-700 dark:text-warning-300">Rp {{ number_format($totalRt) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </x-filament::section>

        {{-- ── DETAIL PER RT (per Desa) ─────────────────────────────────── --}}
        @foreach($byVillage as $village)
        <x-filament::section class="mt-4">
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-heroicon-s-map-pin class="h-4 w-4 text-primary-500"/>
                    Detail RT – Desa {{ $village->village_name }}
                </div>
            </x-slot>
            <x-slot name="description">
                {{ $village->rt_count }} RT aktif &bull; {{ number_format($village->customer_count) }} pelanggan &bull;
                Total CSR: <strong>Rp {{ number_format($village->csr_total) }}</strong>
            </x-slot>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700 text-xs uppercase tracking-wider text-gray-400">
                            <th class="text-left py-2 px-3">RW</th>
                            <th class="text-left py-2 px-3">RT</th>
                            <th class="text-right py-2 px-3">Pelanggan</th>
                            <th class="text-right py-2 px-3">CSR (Total)</th>
                            <th class="text-right py-2 px-3">Bagian Desa</th>
                            <th class="text-right py-2 px-3">Bagian RT</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($village->rows->sortBy([['rw', 'asc'], ['rt', 'asc']]) as $row)
                        <tr class="border-b border-gray-100 dark:border-gray-800/60 hover:bg-gray-50 dark:hover:bg-gray-800/30">
                            <td class="py-2 px-3 text-gray-600 dark:text-gray-400">{{ $row->rw ?? '—' }}</td>
                            <td class="py-2 px-3 font-medium">RT {{ $row->rt ?? '—' }}</td>
                            <td class="py-2 px-3 text-right">{{ number_format($row->customer_count) }}</td>
                            <td class="py-2 px-3 text-right font-semibold text-success-600">Rp {{ number_format($row->csr_total) }}</td>
                            <td class="py-2 px-3 text-right text-info-600">Rp {{ number_format($row->desa_share) }}</td>
                            <td class="py-2 px-3 text-right text-warning-600">Rp {{ number_format($row->rt_share) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="border-t border-gray-200 dark:border-gray-700 font-semibold bg-gray-50 dark:bg-gray-800/40">
                            <td colspan="2" class="py-2 px-3 text-gray-600">Subtotal</td>
                            <td class="py-2 px-3 text-right">{{ number_format($village->customer_count) }}</td>
                            <td class="py-2 px-3 text-right text-success-700">Rp {{ number_format($village->csr_total) }}</td>
                            <td class="py-2 px-3 text-right text-info-700">Rp {{ number_format($village->desa_share) }}</td>
                            <td class="py-2 px-3 text-right text-warning-700">Rp {{ number_format($village->rt_share) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </x-filament::section>
        @endforeach
    @endif

    {{-- Print styles --}}
    <style>
        @media print {
            nav, aside, header, [data-filament-header], .fi-sidebar, .fi-topbar { display: none !important; }
            main { padding: 0 !important; }
            .fi-section { break-inside: avoid; }
        }
    </style>

</x-filament-panels::page>
