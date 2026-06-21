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
        Schema::table('articles', function (Blueprint $table) {
            // Konten penuh artikel (HTML dari rich text editor)
            $table->longText('content')->nullable()->after('excerpt');
            // Kolom gambar utama — akan disimpan sebagai path di storage Laravel
            $table->string('cover_image')->nullable()->after('image_url');
            // Kolom galeri foto tambahan — disimpan sebagai JSON array path
            $table->json('gallery')->nullable()->after('cover_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['content', 'cover_image', 'gallery']);
        });
    }
};
