<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('id_baru')->unique();         // ARM-0001
            $table->string('id_lama')->nullable();        // G-15226030001
            $table->string('nama');
            $table->string('nik_ktp')->nullable();
            $table->string('no_hp')->nullable();
            $table->string('kec')->nullable();
            $table->string('desa')->nullable();
            $table->string('rw')->nullable();
            $table->string('rt')->nullable();
            $table->string('kota_kab')->nullable();

            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->unsignedInteger('paket_mbps')->nullable(); // snapshot kapasitas saat pasang
            $table->unsignedBigInteger('harga')->nullable();   // snapshot harga saat pasang
            $table->string('ip')->nullable();

            $table->string('perangkat_kode')->nullable();  // reference ke devices.kode_id (dihubungkan setelah devices ada)
            $table->string('sn')->nullable();
            $table->string('sn_lama')->nullable();

            $table->foreignId('odp_id')->nullable()->constrained('odps')->nullOnDelete();

            $table->date('tgl_aktif')->nullable();
            $table->float('panjang_kabel')->nullable();
            $table->string('pon_olt')->nullable();          // 1/1/8:2
            $table->string('port_olt')->nullable();
            $table->string('index_olt')->nullable();
            $table->string('profile')->nullable();
            $table->string('vlan_olt')->nullable();
            $table->float('redaman_dbm')->nullable();

            $table->text('keterangan')->nullable();
            $table->string('status')->default('Aktif');     // Aktif, Nonaktif, FREE, dst

            $table->string('link_foto')->nullable();
            $table->string('link_maps')->nullable();

            $table->string('wa_konfirmasi_rtrw')->nullable();
            $table->string('wa_umum')->nullable();
            $table->string('wa_invoice_tagihan')->nullable();
            $table->string('wa_foto_lokasi')->nullable();
            $table->string('wa_ingatkan_h3')->nullable();
            $table->string('wa_jatuh_tempo_hari_ini')->nullable();
            $table->string('wa_lewat_tempo')->nullable();
            $table->string('wa_invoice_pelanggan_baru')->nullable();

            $table->string('ssid')->nullable();
            $table->string('password_wifi')->nullable();
            $table->string('vlan')->nullable();
            $table->string('tipe_onu')->nullable();

            $table->date('jatuh_tempo_bulan_ini')->nullable();
            $table->unsignedBigInteger('tagihan_bln1_prorata')->nullable();

            $table->string('teknisi_pasang')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
