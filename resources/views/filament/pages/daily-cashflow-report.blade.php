<x-filament-panels::page>
    <form wire:submit.prevent="submit">
        {{ $this->form }}
    </form>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
        <!-- Saldo Akhir -->
        <div class="fi-wi-stats-overview-stat relative flex flex-col bg-white ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 rounded-xl p-6 shadow-sm">
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Saldo Bersih Hari Ini</h3>
            <p class="text-3xl font-semibold mt-2 {{ $saldoAkhir >= 0 ? 'text-success-600 dark:text-success-400' : 'text-danger-600 dark:text-danger-400' }}">
                Rp {{ number_format($saldoAkhir, 0, ',', '.') }}
            </p>
        </div>

        <!-- Uang Masuk -->
        <div class="fi-wi-stats-overview-stat relative flex flex-col bg-white ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 rounded-xl p-6 shadow-sm">
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Pemasukan (In)</h3>
            <p class="text-3xl font-semibold mt-2 text-success-600 dark:text-success-400">
                Rp {{ number_format($totalIn, 0, ',', '.') }}
            </p>
            <div class="mt-4 text-xs text-gray-500">
                <ul>
                    @foreach($byMethod as $method => $amount)
                        <li>{{ ucfirst($method ?: 'Lainnya') }}: Rp {{ number_format($amount, 0, ',', '.') }}</li>
                    @endforeach
                </ul>
            </div>
        </div>

        <!-- Uang Keluar -->
        <div class="fi-wi-stats-overview-stat relative flex flex-col bg-white ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 rounded-xl p-6 shadow-sm">
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Pengeluaran (Out)</h3>
            <p class="text-3xl font-semibold mt-2 text-danger-600 dark:text-danger-400">
                Rp {{ number_format($totalOut, 0, ',', '.') }}
            </p>
        </div>
    </div>

    <!-- Tabel Rincian -->
    <div class="mt-8">
        <h2 class="text-lg font-bold mb-4">Rincian Transaksi</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Pemasukan -->
            <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 overflow-hidden">
                <div class="bg-success-50 dark:bg-success-900/30 p-4 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="font-bold text-success-600 dark:text-success-400 flex items-center gap-2">
                        <x-heroicon-o-arrow-down-tray class="w-5 h-5"/> Uang Masuk (Tagihan Lunas)
                    </h3>
                </div>
                <div class="p-0 overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 dark:bg-gray-800/50 text-gray-500 dark:text-gray-400">
                            <tr>
                                <th class="px-4 py-3 font-medium">Pelanggan</th>
                                <th class="px-4 py-3 font-medium">Metode</th>
                                <th class="px-4 py-3 font-medium text-right">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @forelse($invoices as $inv)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                                    <td class="px-4 py-3">{{ $inv->customer->name ?? '-' }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-primary-50 text-primary-600 ring-1 ring-inset ring-primary-500/10">
                                            {{ ucfirst($inv->payment_method ?: '-') }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right font-medium text-success-600">Rp {{ number_format($inv->amount, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-8 text-center text-gray-400">Tidak ada pemasukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pengeluaran -->
            <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 overflow-hidden">
                <div class="bg-danger-50 dark:bg-danger-900/30 p-4 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="font-bold text-danger-600 dark:text-danger-400 flex items-center gap-2">
                        <x-heroicon-o-arrow-up-tray class="w-5 h-5"/> Pengeluaran (Operasional)
                    </h3>
                </div>
                <div class="p-0 overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 dark:bg-gray-800/50 text-gray-500 dark:text-gray-400">
                            <tr>
                                <th class="px-4 py-3 font-medium">Keterangan</th>
                                <th class="px-4 py-3 font-medium text-right">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @forelse($expenses as $exp)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                                    <td class="px-4 py-3">{{ $exp->operasional }}</td>
                                    <td class="px-4 py-3 text-right font-medium text-danger-600">Rp {{ number_format($exp->total_harga, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-4 py-8 text-center text-gray-400">Tidak ada pengeluaran.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
