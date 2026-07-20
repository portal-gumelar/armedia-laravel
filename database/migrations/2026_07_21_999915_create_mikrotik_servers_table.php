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
        Schema::create('mikrotik_servers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('host')->comment('IP Address / Domain Mikrotik');
            $table->integer('port')->default(8728)->comment('API Port');
            $table->string('username');
            $table->string('password');
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mikrotik_servers');
    }
};
