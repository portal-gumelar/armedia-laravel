<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->date('period')->comment('Tanggal awal bulan, contoh: 2026-01-01');
            $table->integer('amount')->comment('Snapshot harga paket saat invoice dibuat');
            $table->string('status')->default('belum')->comment('belum|lunas|gratis|tidak_tertagih');
            $table->date('paid_at')->nullable();
            $table->string('payment_method')->nullable()->comment('transfer|tunai|qris|dsb');
            $table->text('notes')->nullable();
            $table->timestamps();

            // Satu customer hanya boleh punya satu invoice per bulan
            $table->unique(['customer_id', 'period']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
