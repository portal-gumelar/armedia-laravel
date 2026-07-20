<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('csr_distributions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('no')->nullable();
            $table->string('nama')->nullable();       // nama penerima / catatan historis
            $table->string('provinsi')->nullable();
            $table->string('kabupaten')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('desa')->nullable();
            $table->string('rw')->nullable();
            $table->string('rt')->nullable();
            $table->unsignedBigInteger('total')->default(0);
            $table->unsignedBigInteger('dana_desa')->default(0);   // Rp1.000/pelanggan
            $table->unsignedBigInteger('dana_rt')->default(0);     // Rp2.000/pelanggan
            $table->string('status_pencairan')->default('Belum Dibayar');
            $table->date('tgl_bayar')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('csr_distributions');
    }
};
