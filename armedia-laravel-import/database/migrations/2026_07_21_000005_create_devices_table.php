<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('nama');                 // biasanya "ONT"
            $table->string('model')->nullable();
            $table->string('kode_id')->unique()->nullable(); // ID internal perangkat
            $table->string('sn')->nullable();
            $table->date('tgl_ambil_dari_stok')->nullable();
            $table->string('status')->nullable();    // TERPASANG, STOK, dst
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->string('id_lama_referensi')->nullable();
            $table->string('kondisi')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
