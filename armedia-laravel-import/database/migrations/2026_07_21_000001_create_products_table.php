<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();          // AR-1, AR-2, ...
            $table->string('nama');                     // ARMED 10, ARMED 20, ...
            $table->unsignedInteger('kapasitas_mbps');
            $table->unsignedBigInteger('harga');
            $table->string('alokasi_ip')->nullable();   // 10.152.10, dst
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
