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
        Schema::create('vpn_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vpn_server_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('mikrotik_server_id')->nullable()->constrained()->nullOnDelete()->comment('Jika dipakai untuk remote NOC OLT');
            $table->string('username')->unique();
            $table->string('password');
            $table->string('ip_lokal')->nullable()->comment('IP Address VPN yang didapat');
            $table->integer('port_forwarding')->nullable()->comment('Port API/Winbox yang diforward');
            $table->string('vpn_type')->default('l2tp')->comment('l2tp, sstp, wireguard');
            $table->boolean('is_active')->default(true);
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vpn_accounts');
    }
};
