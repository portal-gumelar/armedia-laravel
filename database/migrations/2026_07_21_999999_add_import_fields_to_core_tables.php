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
            if (!Schema::hasColumn('customers', 'kec')) $table->string('kec')->nullable();
            if (!Schema::hasColumn('customers', 'desa')) $table->string('desa')->nullable();
            if (!Schema::hasColumn('customers', 'kota_kab')) $table->string('kota_kab')->nullable();
            if (!Schema::hasColumn('customers', 'paket_mbps')) $table->unsignedInteger('paket_mbps')->nullable();
            if (!Schema::hasColumn('customers', 'harga')) $table->unsignedBigInteger('harga')->nullable();
            if (!Schema::hasColumn('customers', 'perangkat_kode')) $table->string('perangkat_kode')->nullable();
            if (!Schema::hasColumn('customers', 'sn')) $table->string('sn')->nullable();
            if (!Schema::hasColumn('customers', 'sn_lama')) $table->string('sn_lama')->nullable();
            if (!Schema::hasColumn('customers', 'port_olt')) $table->string('port_olt')->nullable();
            if (!Schema::hasColumn('customers', 'index_olt')) $table->string('index_olt')->nullable();
            if (!Schema::hasColumn('customers', 'profile')) $table->string('profile')->nullable();
            if (!Schema::hasColumn('customers', 'vlan_olt')) $table->string('vlan_olt')->nullable();
            if (!Schema::hasColumn('customers', 'redaman_dbm')) $table->float('redaman_dbm')->nullable();
            if (!Schema::hasColumn('customers', 'link_foto')) $table->string('link_foto')->nullable();
            if (!Schema::hasColumn('customers', 'link_maps')) $table->string('link_maps')->nullable();
            if (!Schema::hasColumn('customers', 'wa_konfirmasi_rtrw')) $table->string('wa_konfirmasi_rtrw')->nullable();
            if (!Schema::hasColumn('customers', 'wa_umum')) $table->string('wa_umum')->nullable();
            if (!Schema::hasColumn('customers', 'wa_invoice_tagihan')) $table->string('wa_invoice_tagihan')->nullable();
            if (!Schema::hasColumn('customers', 'wa_foto_lokasi')) $table->string('wa_foto_lokasi')->nullable();
            if (!Schema::hasColumn('customers', 'wa_ingatkan_h3')) $table->string('wa_ingatkan_h3')->nullable();
            if (!Schema::hasColumn('customers', 'wa_jatuh_tempo_hari_ini')) $table->string('wa_jatuh_tempo_hari_ini')->nullable();
            if (!Schema::hasColumn('customers', 'wa_lewat_tempo')) $table->string('wa_lewat_tempo')->nullable();
            if (!Schema::hasColumn('customers', 'wa_invoice_pelanggan_baru')) $table->string('wa_invoice_pelanggan_baru')->nullable();
            if (!Schema::hasColumn('customers', 'ssid')) $table->string('ssid')->nullable();
            if (!Schema::hasColumn('customers', 'password_wifi')) $table->string('password_wifi')->nullable();
            if (!Schema::hasColumn('customers', 'vlan')) $table->string('vlan')->nullable();
            if (!Schema::hasColumn('customers', 'tipe_onu')) $table->string('tipe_onu')->nullable();
            if (!Schema::hasColumn('customers', 'jatuh_tempo_bulan_ini')) $table->date('jatuh_tempo_bulan_ini')->nullable();
            if (!Schema::hasColumn('customers', 'tagihan_bln1_prorata')) $table->unsignedBigInteger('tagihan_bln1_prorata')->nullable();
            if (!Schema::hasColumn('customers', 'teknisi_pasang')) $table->string('teknisi_pasang')->nullable();
        });

        // 2. Tambah ke products
        Schema::table('internet_packages', function (Blueprint $table) {
            if (!Schema::hasColumn('internet_packages', 'kode')) $table->string('kode')->nullable();
            if (!Schema::hasColumn('internet_packages', 'alokasi_ip')) $table->string('alokasi_ip')->nullable();
        });

        // 3. Tambah ke odps
        Schema::table('odps', function (Blueprint $table) {
            if (!Schema::hasColumn('odps', 'kapasitas_maks')) $table->integer('kapasitas_maks')->nullable();
            if (!Schema::hasColumn('odps', 'sisa_slot')) $table->integer('sisa_slot')->nullable();
            if (!Schema::hasColumn('odps', 'status_odp')) $table->string('status_odp')->nullable();
            if (!Schema::hasColumn('odps', 'desa_lokasi')) $table->string('desa_lokasi')->nullable();
        });

        // 4. Tambah ke devices
        Schema::table('devices', function (Blueprint $table) {
            if (!Schema::hasColumn('devices', 'kode_id')) $table->string('kode_id')->nullable();
            if (!Schema::hasColumn('devices', 'tgl_ambil_dari_stok')) $table->date('tgl_ambil_dari_stok')->nullable();
            if (!Schema::hasColumn('devices', 'id_lama_referensi')) $table->string('id_lama_referensi')->nullable();
            if (!Schema::hasColumn('devices', 'kondisi')) $table->string('kondisi')->nullable();
            if (!Schema::hasColumn('devices', 'catatan')) $table->text('catatan')->nullable();
        });
    }

    public function down(): void
    {
    }
};
