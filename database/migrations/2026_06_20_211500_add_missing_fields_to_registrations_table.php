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
        Schema::table('registrations', function (Blueprint $table) {
            $table->string('nik')->nullable();
            $table->string('rw')->nullable();
            $table->string('rt')->nullable();
            $table->string('provider_saat_ini')->nullable();
            $table->string('sumber_info')->nullable();
            $table->text('link_google_maps')->nullable();
            $table->string('foto_ktp')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamp('tanggal_aktif')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn([
                'nik',
                'rw',
                'rt',
                'provider_saat_ini',
                'sumber_info',
                'link_google_maps',
                'foto_ktp',
                'catatan',
                'tanggal_aktif',
            ]);
        });
    }
};
