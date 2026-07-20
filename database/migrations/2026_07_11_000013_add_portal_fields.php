<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah invoice_no ke tabel invoices
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('invoice_no')->nullable()->unique()->after('id')
                  ->comment('Format: ARM-0001-YYMMDD.HHMMSS.{id}');
            $table->date('due_date')->nullable()->after('period')
                  ->comment('Tanggal jatuh tempo (default: tgl 10 bulan periode)');
        });

        // Tambah customer_id ke acr_members (hubungkan ke tabel customers)
        Schema::table('acr_members', function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable()->after('id')
                  ->constrained('customers')->nullOnDelete()
                  ->comment('Relasi ke tabel customers');
            $table->string('pin')->nullable()->after('password')
                  ->comment('PIN 6 digit untuk login portal pelanggan');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['invoice_no', 'due_date']);
        });

        Schema::table('acr_members', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropColumn(['customer_id', 'pin']);
        });
    }
};
