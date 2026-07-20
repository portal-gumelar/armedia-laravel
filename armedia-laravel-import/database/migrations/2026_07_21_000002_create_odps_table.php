<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('odps', function (Blueprint $table) {
            $table->id();
            $table->string('kode_odp')->unique();       // 1/1/1, 1/1/2, ...
            $table->unsignedInteger('port_terpakai')->default(0);
            $table->unsignedInteger('kapasitas_maks')->nullable();
            $table->integer('sisa_slot')->nullable();
            $table->string('status')->nullable();        // Tersedia, Penuh, dst
            $table->string('desa_lokasi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('odps');
    }
};
