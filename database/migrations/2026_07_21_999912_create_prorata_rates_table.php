<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prorata_rates', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('tanggal_pasang'); // 1-30
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->unsignedBigInteger('jumlah');
            $table->timestamps();

            $table->unique(['tanggal_pasang', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prorata_rates');
    }
};
