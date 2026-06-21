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
        Schema::create('acr_point_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_member')->constrained('acr_members')->cascadeOnDelete();
            $table->string('jenis'); // MASUK or KELUAR
            $table->integer('jumlah_poin');
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acr_point_transactions');
    }
};
