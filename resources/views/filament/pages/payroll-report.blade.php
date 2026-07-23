<x-filament-panels::page>
    <div class="max-w-md mb-6">
        <form wire:submit.prevent="submit">
            {{ $this->form }}
        </form>
    </div>

    <!-- Tabel Rincian -->
    <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 overflow-hidden">
        <div class="bg-primary-50 dark:bg-primary-900/30 p-4 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center">
            <h3 class="font-bold text-primary-600 dark:text-primary-400 flex items-center gap-2">
                <x-heroicon-o-users class="w-5 h-5"/> Rekapitulasi Gaji & Komisi Teknisi/Marketing
            </h3>
        </div>
        <div class="p-0 overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 dark:bg-gray-800/50 text-gray-500 dark:text-gray-400">
                    <tr>
                        <th class="px-4 py-3 font-medium">Nama Marketing/Teknisi</th>
                        <th class="px-4 py-3 font-medium text-center">Total PSB Selesai</th>
                        <th class="px-4 py-3 font-medium text-right">Total Komisi / Fee</th>
                        <th class="px-4 py-3 font-medium text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($payrolls as $pay)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                            <td class="px-4 py-3 font-semibold">{{ $pay->marketing_name }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-info-100 text-info-800 dark:bg-info-900 dark:text-info-300">
                                    {{ $pay->total_psb }} Pelanggan
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right font-bold text-success-600">Rp {{ number_format($pay->total_fee, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-center">
                                <button onclick="alert('Cetak slip gaji PDF belum dikonfigurasi route-nya.')" class="inline-flex items-center justify-center gap-1 font-medium rounded-lg border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset min-h-[2.25rem] px-3 text-sm text-gray-800 bg-white border-gray-300 hover:bg-gray-50 focus:ring-primary-600 dark:text-white dark:bg-gray-800 dark:border-gray-600 dark:hover:bg-gray-700">
                                    <x-heroicon-o-printer class="w-4 h-4"/> Cetak Slip
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-400">Tidak ada data pembayaran komisi di bulan ini.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-50 dark:bg-gray-800/50">
                    <tr>
                        <th class="px-4 py-3 text-right font-bold" colspan="2">TOTAL KESELURUHAN:</th>
                        <th class="px-4 py-3 text-right font-bold text-success-600 text-lg">Rp {{ number_format($payrolls->sum('total_fee'), 0, ',', '.') }}</th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</x-filament-panels::page>
