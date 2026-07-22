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
        Schema::create('vpn_servers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('host')->comment('IP Publik MikroTik CHR');
            $table->integer('port')->default(8728)->comment('RouterOS API Port');
            $table->string('username');
            $table->text('password')->comment('Encrypted in Model');
            $table->string('type')->default('mikrotik_chr');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vpn_servers');
    }
};
