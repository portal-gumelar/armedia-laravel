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
        Schema::create('acr_rewards_catalog', function (Blueprint $table) {
            $table->id();
            $table->string('nama_hadiah');
            $table->integer('poin_dibutuhkan');
            $table->integer('stok')->default(99);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acr_rewards_catalog');
    }
};
