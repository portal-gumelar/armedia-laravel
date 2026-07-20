<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();

            // Identifikasi
            $table->string('id_arm')->unique()->nullable()->comment('Contoh: ARM-0001');
            $table->string('id_lama')->nullable()->comment('ID sistem lama, misal: G-152260');
            $table->string('name');
            $table->string('whatsapp')->nullable();
            $table->string('nik')->nullable();
            $table->text('alamat')->nullable();
            $table->string('kecamatan')->nullable()->default('GUMELAR');
            $table->string('rw')->nullable();
            $table->string('rt')->nullable();

            // Relasi ke master
            $table->foreignId('internet_package_id')->nullable()->constrained('internet_packages')->nullOnDelete();
            $table->foreignId('village_id')->nullable()->constrained('villages')->nullOnDelete();
            $table->foreignId('odp_id')->nullable()->constrained('odps')->nullOnDelete();
            // device_id ditambahkan via migrasi terpisah (circular reference)

            // Data teknis ISP
            $table->string('ip_address')->nullable()->comment('Contoh: 10.152.6.30');
            $table->string('pon_olt')->nullable()->comment('Contoh: 1/1/3:2');
            $table->integer('cable_length_m')->nullable();
            $table->date('activated_at')->nullable();

            // Status
            $table->string('subscription_status')->nullable()->default('aktif')
                  ->comment('aktif|berhenti|isolir');
            $table->string('monitoring_status')->nullable()->default('unknown')
                  ->comment('up|down|unknown');
            $table->timestamp('monitoring_checked_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
