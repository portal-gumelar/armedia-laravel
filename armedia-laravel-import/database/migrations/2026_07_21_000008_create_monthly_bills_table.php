<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monthly_bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->unsignedSmallInteger('tahun');   // 2026
            $table->unsignedTinyInteger('bulan');    // 1-12
            $table->unsignedBigInteger('jumlah')->default(0); // 0 = "-" (belum bayar / belum aktif)
            $table->unsignedBigInteger('harga_acuan_snapshot')->nullable();
            $table->timestamps();

            $table->unique(['customer_id', 'tahun', 'bulan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_bills');
    }
};
