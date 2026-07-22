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
        Schema::table('customer_prospects', function (Blueprint $table) {
            $table->text('link_map')->nullable();
            $table->string('foto_ktp')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_prospects', function (Blueprint $table) {
            $table->dropColumn(['link_map', 'foto_ktp']);
        });
    }
};
