<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('odps', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->comment('Contoh: 1/1/3');
            $table->integer('max_capacity')->nullable()->default(8);
            $table->foreignId('village_id')->nullable()->constrained('villages')->nullOnDelete();
            $table->string('status')->nullable()->comment('aktif|nonaktif');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('odps');
    }
};
