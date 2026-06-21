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
        Schema::create('acr_members', function (Blueprint $table) {
            $table->id();
            $table->string('id_pelanggan')->unique();
            $table->string('nama');
            $table->string('whatsapp');
            $table->string('password')->nullable(); // Ditambahkan untuk login member
            $table->integer('total_poin')->default(0);
            $table->string('level_member')->default('Reguler');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acr_members');
    }
};
