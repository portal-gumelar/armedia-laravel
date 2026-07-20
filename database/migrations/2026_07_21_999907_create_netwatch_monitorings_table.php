<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('netwatch_monitorings', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address')->nullable();
            $table->string('status_koneksi')->nullable(); // UP / DOWN (paste dari Mikrotik Netwatch)
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->string('desa')->nullable();
            $table->string('rw_rt')->nullable();
            $table->unsignedInteger('paket_mbps')->nullable();
            $table->string('status_berlangganan')->nullable();
            $table->string('chat_wa')->nullable();
            $table->string('wa_follow_up_gangguan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('netwatch_monitorings');
    }
};
