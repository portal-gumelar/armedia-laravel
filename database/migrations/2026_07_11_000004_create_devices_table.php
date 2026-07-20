<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('device_code')->unique()->comment('Contoh: PG-1522602001');
            $table->string('name')->comment('Contoh: XPON ONT');
            $table->string('model')->nullable()->comment('Contoh: F680C');
            $table->string('serial_number')->unique()->nullable()->comment('Contoh: HWTCXXXXXXXX');
            $table->string('batch_month_year')->nullable()->comment('Contoh: 2023-06');
            $table->string('status')->default('stok')->comment('terpasang|stok|rusak');
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
