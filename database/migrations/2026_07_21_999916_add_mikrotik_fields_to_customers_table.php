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
        Schema::table('customers', function (Blueprint $table) {
            $table->foreignId('mikrotik_server_id')->nullable()->constrained('mikrotik_servers')->nullOnDelete();
            $table->string('pppoe_username')->nullable()->unique();
            $table->string('pppoe_password')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['mikrotik_server_id']);
            $table->dropColumn(['mikrotik_server_id', 'pppoe_username', 'pppoe_password']);
        });
    }
};
