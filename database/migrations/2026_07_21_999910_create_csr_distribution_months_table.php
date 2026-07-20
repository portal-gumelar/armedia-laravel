<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('csr_distribution_months', function (Blueprint $table) {
            $table->id();
            $table->foreignId('csr_distribution_id')->constrained('csr_distributions')->cascadeOnDelete();
            $table->unsignedTinyInteger('bulan'); // 1-12
            $table->unsignedInteger('jumlah_pelanggan')->default(0);
            $table->unsignedBigInteger('jumlah_csr')->default(0);
            $table->timestamps();

            $table->unique(['csr_distribution_id', 'bulan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('csr_distribution_months');
    }
};
