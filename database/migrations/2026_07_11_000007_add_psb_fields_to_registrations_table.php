<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            // Semua nullable — additive only, tidak menyentuh kolom lama
            $table->string('report_no')->nullable()->after('catatan')
                  ->comment('Nomor laporan PSB, contoh: PSB15226062701');
            $table->date('jadwal_pasang')->nullable()->after('report_no');
            $table->string('marketing')->nullable()->after('jadwal_pasang')
                  ->comment('Nama marketing yang mereferensikan');
            $table->foreignId('target_odp_id')->nullable()->after('marketing')
                  ->constrained('odps')->nullOnDelete();
            $table->string('pipeline_status')->nullable()->after('target_odp_id')
                  ->default('belum')
                  ->comment('belum|survey|terjadwal|terpasang|batal');
            $table->foreignId('converted_customer_id')->nullable()->after('pipeline_status')
                  ->constrained('customers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropForeign(['target_odp_id']);
            $table->dropForeign(['converted_customer_id']);
            $table->dropColumn([
                'report_no',
                'jadwal_pasang',
                'marketing',
                'target_odp_id',
                'pipeline_status',
                'converted_customer_id',
            ]);
        });
    }
};
