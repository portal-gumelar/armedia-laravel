<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // customers - nullable agar data lama (ARMEDIA Pusat) tetap valid
        Schema::table('customers', function (Blueprint $table) {
            $table->foreignId('mitra_id')->nullable()->after('id')->constrained('mitras')->nullOnDelete();
        });

        // odps
        Schema::table('odps', function (Blueprint $table) {
            $table->foreignId('mitra_id')->nullable()->after('id')->constrained('mitras')->nullOnDelete();
        });

        // invoices
        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('mitra_id')->nullable()->after('id')->constrained('mitras')->nullOnDelete();
        });

        // tickets
        Schema::table('tickets', function (Blueprint $table) {
            $table->foreignId('mitra_id')->nullable()->after('id')->constrained('mitras')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('customers', fn (Blueprint $t) => $t->dropConstrainedForeignId('mitra_id'));
        Schema::table('odps', fn (Blueprint $t) => $t->dropConstrainedForeignId('mitra_id'));
        Schema::table('invoices', fn (Blueprint $t) => $t->dropConstrainedForeignId('mitra_id'));
        Schema::table('tickets', fn (Blueprint $t) => $t->dropConstrainedForeignId('mitra_id'));
    }
};
