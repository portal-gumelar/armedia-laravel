<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center gap-4 mb-4">
            <div class="h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600">
                <x-heroicon-o-rocket-launch class="h-7 w-7" />
            </div>
            <div>
                <h2 class="text-xl font-bold tracking-tight text-gray-950 dark:text-white">
                    Pembaruan Sistem (Juli 2026) 🚀
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Halo Pak Koko & Pak Yuseph! Berikut adalah rangkuman fitur baru yang berhasil kita bangun:
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
            
            <!-- Fitur 1 -->
            <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-2 mb-2">
                    <x-heroicon-o-building-office-2 class="w-5 h-5 text-primary-500" />
                    <h3 class="font-bold text-gray-900 dark:text-white">Sistem Kemitraan (Reseller/ISP)</h3>
                </div>
                <ul class="list-disc list-inside text-sm text-gray-600 dark:text-gray-400 space-y-1 ml-1">
                    <li>Dasbor terpisah untuk Mitra/Cabang (<a href="/mitra" class="text-primary-600 font-medium hover:underline" target="_blank">portal.armedia.id/mitra</a>)</li>
                    <li>Isolasi data pelanggan, tagihan, dan tiket per masing-masing Mitra</li>
                    <li>Otomatisasi filter data tanpa tercampur dengan pusat</li>
                </ul>
            </div>

            <!-- Fitur 2 -->
            <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-2 mb-2">
                    <x-heroicon-o-users class="w-5 h-5 text-emerald-500" />
                    <h3 class="font-bold text-gray-900 dark:text-white">Portal Member (Pelanggan)</h3>
                </div>
                <ul class="list-disc list-inside text-sm text-gray-600 dark:text-gray-400 space-y-1 ml-1">
                    <li>Login mudah menggunakan Nomor WhatsApp (<a href="/member" class="text-emerald-600 font-medium hover:underline" target="_blank">portal.armedia.id/member</a>)</li>
                    <li>Dasbor Ringkasan (Status Internet, Jatuh Tempo Tagihan)</li>
                    <li>Tombol Pintas "Hubungi CS via WhatsApp" langsung dari aplikasi</li>
                </ul>
            </div>

            <!-- Fitur 3 -->
            <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-2 mb-2">
                    <x-heroicon-o-table-cells class="w-5 h-5 text-amber-500" />
                    <h3 class="font-bold text-gray-900 dark:text-white">Import Data Massal</h3>
                </div>
                <ul class="list-disc list-inside text-sm text-gray-600 dark:text-gray-400 space-y-1 ml-1">
                    <li>Tombol "Import dari Excel" di menu Pelanggan</li>
                    <li>Sistem deteksi otomatis jika ada data yang ganda (duplikat)</li>
                    <li>Mendukung file format <code>.xlsx</code> dan <code>.csv</code></li>
                </ul>
            </div>

            <!-- Fitur 4 -->
            <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-2 mb-2">
                    <x-heroicon-o-gift class="w-5 h-5 text-rose-500" />
                    <h3 class="font-bold text-gray-900 dark:text-white">Sistem Poin ACR</h3>
                </div>
                <ul class="list-disc list-inside text-sm text-gray-600 dark:text-gray-400 space-y-1 ml-1">
                    <li>Skema membership terpadu (Platinum, Gold, Silver)</li>
                    <li>Informasi jumlah poin langsung terhubung di dasbor Pelanggan</li>
                    <li>Persiapan untuk katalog penukaran hadiah</li>
                </ul>
            </div>
            
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
