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
        Schema::create('olt_ports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('olt_server_id')->constrained()->cascadeOnDelete();
            $table->string('slot')->comment('Example: 1/1');
            $table->string('port')->comment('Example: 1');
            $table->integer('max_capacity')->default(128)->comment('Max ONUs per port');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('olt_ports');
    }
};
