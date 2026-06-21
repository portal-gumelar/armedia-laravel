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
        Schema::create('acr_redemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_member')->constrained('acr_members');
            $table->foreignId('id_hadiah')->constrained('acr_rewards_catalog');
            $table->string('status')->default('Menunggu Proses');
            $table->timestamp('tanggal_tukar')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acr_redemptions');
    }
};
