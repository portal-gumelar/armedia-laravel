<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('internet_packages', function (Blueprint $table) {
            // Cek additive — hanya tambah kolom baru, tidak mengubah yang lama
            $table->string('code')->unique()->nullable()->after('is_active')
                  ->comment('Kode singkat, contoh: AR-2, HR-11');
            $table->string('brand')->nullable()->after('code')
                  ->comment('ARMED | HEROIK');
            $table->integer('speed_mbps')->nullable()->after('brand')
                  ->comment('Kecepatan dalam Mbps');
            $table->string('ip_allocation')->nullable()->after('speed_mbps')
                  ->comment('Rentang IP, contoh: 10.152.6-10.152.7');
        });
    }

    public function down(): void
    {
        Schema::table('internet_packages', function (Blueprint $table) {
            $table->dropColumn(['code', 'brand', 'speed_mbps', 'ip_allocation']);
        });
    }
};
