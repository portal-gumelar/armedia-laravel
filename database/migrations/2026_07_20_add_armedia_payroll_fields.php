<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            // ── Komponen IKR (Insentif Kinerja) ─────────────────────────────
            $table->integer('fee_ikr_per_pelanggan')->default(40000)
                  ->comment('Fee IKR per pemasangan (default Rp40.000)')->after('notes');
            $table->integer('jumlah_teknisi_pasang')->default(1)
                  ->comment('Jumlah teknisi yang ikut memasang (pembagi tarif IKR)')->after('fee_ikr_per_pelanggan');
            $table->integer('jumlah_ikr')->default(0)
                  ->comment('Jumlah pemasangan yang dilakukan karyawan ini')->after('jumlah_teknisi_pasang');

            // ── Komponen Transport ───────────────────────────────────────────
            $table->integer('hari_hadir')->default(0)
                  ->comment('Jumlah hari hadir (dari laporan harian)')->after('jumlah_ikr');

            // ── Komponen Marketing ───────────────────────────────────────────
            $table->integer('jumlah_referral')->default(0)
                  ->comment('Jumlah pelanggan baru hasil referral')->after('hari_hadir');

            // ── Potongan ─────────────────────────────────────────────────────
            $table->integer('kasbon')->default(0)
                  ->comment('Kasbon / pinjaman yang dipotong')->after('jumlah_referral');
            $table->integer('lain_lain_potong')->default(0)
                  ->comment('Potongan lain-lain (mis. ganti kabel)')->after('kasbon');
            $table->text('ket_lain_lain')->nullable()
                  ->comment('Keterangan potongan lain-lain')->after('lain_lain_potong');
        });
    }

    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn([
                'fee_ikr_per_pelanggan',
                'jumlah_teknisi_pasang',
                'jumlah_ikr',
                'hari_hadir',
                'jumlah_referral',
                'kasbon',
                'lain_lain_potong',
                'ket_lain_lain',
            ]);
        });
    }
};
