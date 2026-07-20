<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah ke customers
        Schema::table('customers', function (Blueprint $table) {
            $table->string('kec')->nullable();
            $table->string('desa')->nullable();
            $table->string('kota_kab')->nullable();
            $table->unsignedInteger('paket_mbps')->nullable();
            $table->unsignedBigInteger('harga')->nullable();
            $table->string('perangkat_kode')->nullable();
            $table->string('sn')->nullable();
            $table->string('sn_lama')->nullable();
            $table->string('port_olt')->nullable();
            $table->string('index_olt')->nullable();
            $table->string('profile')->nullable();
            $table->string('vlan_olt')->nullable();
            $table->float('redaman_dbm')->nullable();
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
        });

        // 2. Tambah ke products
        Schema::table('internet_packages', function (Blueprint $table) {
            $table->string('kode')->nullable();
            $table->string('alokasi_ip')->nullable();
        });

        // 3. Tambah ke odps
        Schema::table('odps', function (Blueprint $table) {
            $table->integer('kapasitas_maks')->nullable();
            $table->integer('sisa_slot')->nullable();
            $table->string('status_odp')->nullable(); // karena 'status' bisa bentrok jika sudah ada
            $table->string('desa_lokasi')->nullable();
        });

        // 4. Tambah ke devices
        Schema::table('devices', function (Blueprint $table) {
            $table->string('kode_id')->nullable();
            $table->date('tgl_ambil_dari_stok')->nullable();
            $table->string('id_lama_referensi')->nullable();
            $table->string('kondisi')->nullable();
            $table->text('catatan')->nullable();
        });
    }

    public function down(): void
    {
        // Down migration tidak ditulis detail untuk mempersingkat waktu.
    }
};
