<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ont_inventories', function (Blueprint $table) {
            $table->id();
            $table->enum('tipe', ['new', 'error'])->default('new'); // dari sheet 12 vs 13
            $table->string('nama_barang')->default('ONT');
            $table->string('merek')->nullable();
            $table->string('sn')->nullable();
            $table->string('mac_address')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('ssid_2g')->nullable();
            $table->string('ssid_5g')->nullable();
            $table->unsignedInteger('jumlah')->default(1);
            $table->string('keterangan')->nullable(); // NEW / EROR
            $table->string('status')->nullable();     // TERPASANG dsb (hanya ada di sheet NEW)
            $table->date('tgl_keluar')->nullable();
            $table->string('teknisi')->nullable();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ont_inventories');
    }
};
