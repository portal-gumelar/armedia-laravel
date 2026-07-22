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
        Schema::create('onu_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('olt_port_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('sn')->unique()->comment('ONU Serial Number (ZTE/Huawei format)');
            $table->string('onu_id')->nullable()->comment('ONU Index on OLT port');
            $table->string('status')->default('offline')->comment('online, offline, los');
            $table->decimal('rx_power', 8, 2)->nullable()->comment('Last known RX Power (dBm)');
            $table->timestamp('last_online_at')->nullable();
            $table->timestamp('last_offline_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('onu_devices');
    }
};
