<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mitras', function (Blueprint $table) {
            $table->id();
            $table->string('kode_mitra', 20)->unique(); // Misal: ARM-MTR-001
            $table->string('nama_mitra');
            $table->string('pemilik')->nullable();       // Nama pemilik mitra
            $table->string('whatsapp', 20)->nullable();
            $table->string('email')->nullable();
            $table->text('alamat')->nullable();
            $table->string('wilayah')->nullable();       // Kota/Kabupaten cakupan mitra
            $table->decimal('persentase_komisi', 5, 2)->default(0); // Misal: 10.00%
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabel pivot User <-> Mitra (satu user bisa pegang beberapa mitra)
        Schema::create('mitra_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mitra_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['mitra_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mitra_user');
        Schema::dropIfExists('mitras');
    }
};
