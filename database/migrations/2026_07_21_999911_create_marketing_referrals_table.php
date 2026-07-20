<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketing_referrals', function (Blueprint $table) {
            $table->id();
            $table->string('nama_marketing');
            $table->string('lokasi')->nullable();
            $table->string('nama_client');
            $table->unsignedBigInteger('jumlah_fee')->default(0);
            $table->date('tgl_daftar')->nullable();
            $table->string('sumber_data')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketing_referrals');
    }
};
