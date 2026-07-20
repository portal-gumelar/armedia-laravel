<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('csr_contributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('village_id')->nullable()->constrained('villages')->nullOnDelete();
            $table->string('rw')->nullable();
            $table->string('rt')->nullable();
            $table->date('period')->comment('Bulan snapshot, contoh: 2026-01-01');
            $table->integer('customer_count')->default(0);
            $table->integer('csr_total')->default(0)->comment('Total CSR = customer_count * 3000');
            $table->integer('desa_share')->default(0)->comment('Bagian desa = customer_count * 1000');
            $table->integer('rt_share')->default(0)->comment('Bagian RT = customer_count * 2000');
            $table->timestamps();

            $table->index(['village_id', 'period']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('csr_contributions');
    }
};
