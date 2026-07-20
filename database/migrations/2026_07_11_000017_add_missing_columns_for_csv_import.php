<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Only customers table needs the extra columns, the other tables already have them from previous migrations.
        Schema::table('customers', function (Blueprint $table) {
            $table->string('kota_kab')->nullable()->after('rt');
            $table->string('photo_url')->nullable()->after('monitoring_checked_at');
            $table->string('maps_url')->nullable()->after('photo_url');
            $table->string('drive_folder_url')->nullable()->after('maps_url');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['kota_kab', 'photo_url', 'maps_url', 'drive_folder_url']);
        });
    }
};
