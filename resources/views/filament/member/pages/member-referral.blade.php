<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Banner Info -->
        <div class="p-6 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl shadow-lg text-white">
            <h2 class="text-2xl font-bold mb-2">Ajak Teman, Dapatkan Untung! 🎁</h2>
            <p class="mb-4">Bagikan link referral Anda ke tetangga atau teman. Dapatkan <strong>50.000 Poin</strong> setiap ada teman yang berhasil berlangganan ARMEDIA melalui link Anda!</p>
            <div class="flex items-center space-x-4 bg-white/20 p-4 rounded-xl backdrop-blur-sm">
                <div class="flex-1">
                    <p class="text-sm uppercase tracking-wider font-semibold opacity-80">Link Referral Anda</p>
                    <p class="text-lg font-mono font-bold mt-1 select-all">{{ $referralLink }}</p>
                </div>
                <button onclick="navigator.clipboard.writeText('{{ $referralLink }}'); alert('Link berhasil disalin!')" class="px-4 py-2 bg-white text-blue-600 rounded-lg font-bold shadow hover:bg-gray-100 transition">
                    Copy Link
                </button>
            </div>
        </div>

        <!-- Poin Status -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="p-6 bg-white rounded-2xl shadow dark:bg-gray-800 flex items-center space-x-6">
                <div class="p-4 bg-yellow-100 dark:bg-yellow-900/50 rounded-full text-yellow-600">
                    <x-heroicon-o-star class="w-10 h-10" />
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 font-semibold">Total Poin Anda</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($totalPoints, 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-400 mt-1">1 Poin = Rp 1. Bisa ditukar untuk bayar tagihan.</p>
                </div>
            </div>
            
            <div class="p-6 bg-white rounded-2xl shadow dark:bg-gray-800">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">Riwayat Poin Terakhir</h3>
                @if(count($transactions) > 0)
                    <div class="space-y-4">
                        @foreach($transactions as $trx)
                            <div class="flex justify-between items-center border-b border-gray-100 dark:border-gray-700 pb-2 last:border-0 last:pb-0">
                                <div>
                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $trx->description }}</p>
                                    <p class="text-xs text-gray-500">{{ $trx->created_at->format('d M Y H:i') }}</p>
                                </div>
                                <div class="font-bold {{ $trx->amount > 0 ? 'text-green-500' : 'text-red-500' }}">
                                    {{ $trx->amount > 0 ? '+' : '' }}{{ number_format($trx->amount, 0, ',', '.') }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-sm italic">Belum ada riwayat poin.</p>
                @endif
            </div>
        </div>
    </div>
</x-filament-panels::page>
