<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_prospects', function (Blueprint $table) {
            $table->id();
            $table->string('no_laporan')->unique()->nullable(); // PSB15226062302
            $table->string('nama_pelanggan');
            $table->string('nomor_telepon')->nullable();
            $table->string('alamat_pemasangan')->nullable();
            $table->date('jadwal_pasang')->nullable();
            $table->string('jenis_layanan')->nullable();
            $table->string('status')->default('Belum'); // Belum / Terpasang
            $table->string('marketing')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_prospects');
    }
};
