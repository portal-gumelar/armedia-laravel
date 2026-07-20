<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operational_expenses', function (Blueprint $table) {
            $table->id();
            $table->string('nota')->nullable();
            $table->string('operasional'); // nama barang/jasa
            $table->string('qty')->nullable(); // "5,00 ROL" dsb, disimpan sebagai teks apa adanya
            $table->unsignedBigInteger('harga_satuan')->default(0);
            $table->unsignedBigInteger('total_harga')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operational_expenses');
    }
};
