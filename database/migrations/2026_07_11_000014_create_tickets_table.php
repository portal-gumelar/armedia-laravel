<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_no')->unique()->comment('Format: TKT-YYMMDD-XXXX');
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->string('category')->default('internet_mati')
                  ->comment('internet_mati|lambat|wifi_masalah|lainnya');
            $table->text('description')->comment('Kronologi keluhan pelanggan');
            $table->string('status')->default('open')
                  ->comment('open|process|resolved|closed');
            $table->text('technician_notes')->nullable()
                  ->comment('Catatan teknisi perbaikan');
            $table->timestamp('resolved_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['customer_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
