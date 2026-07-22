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
        Schema::create('olt_servers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('host')->comment('IP Address / Domain OLT');
            $table->integer('port')->default(23)->comment('SSH/Telnet Port');
            $table->string('username');
            $table->text('password')->comment('Encrypted in Model');
            $table->text('snmp_community')->nullable()->comment('Encrypted in Model');
            $table->string('type')->default('zte')->comment('zte, huawei');
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
        Schema::dropIfExists('olt_servers');
    }
};
