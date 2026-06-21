<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->string('paket');
            $table->string('langganan_sebelumnya')->nullable();
            $table->string('nama');
            $table->string('whatsapp');
            $table->string('kecamatan')->default('GUMELAR');
            $table->string('desa');
            $table->text('alamat');
            $table->string('tanggal_pemasangan')->default('Secepatnya');
            $table->string('waktu_survei')->default('Pagi (08:00 - 11:00)');
            $table->string('status')->default('baru');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
