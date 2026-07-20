<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketing_fees', function (Blueprint $table) {
            $table->id();
            $table->string('marketing_name')->comment('Nama marketing/referral');
            $table->string('location')->nullable()->comment('Lokasi/daerah marketing');
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->string('client_name')->nullable()->comment('Nama pelanggan yang direferensikan');
            $table->integer('fee_amount')->default(0)->comment('Jumlah fee dalam rupiah');
            $table->string('status')->nullable()->comment('pending|dibayar|batal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketing_fees');
    }
};
