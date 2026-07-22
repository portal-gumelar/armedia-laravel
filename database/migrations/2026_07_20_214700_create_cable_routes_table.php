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
        Schema::create('cable_routes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('polyline'); // array of lat/long points
            $table->enum('type', ['core', 'distribution', 'drop'])->default('distribution');
            $table->enum('status', ['active', 'cut', 'maintenance'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cable_routes');
    }
};
