<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── EXPENSES (Pengeluaran Operasional) ────────────────────────────────
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('expense_no')->unique()->comment('Format: EXP-YYMMDD-XXXX');
            $table->date('expense_date');
            $table->string('category')->comment('operasional|infrastruktur|sdm|pemasaran|lainnya');
            $table->string('description');
            $table->decimal('amount', 15, 2);
            $table->string('payment_method')->default('transfer')->comment('transfer|tunai|kartu');
            $table->string('receipt_file')->nullable()->comment('Path file bukti pembayaran');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });

        // ── EMPLOYEES (Data Karyawan) ─────────────────────────────────────────
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_no')->unique()->comment('Format: EMP-XXXX');
            $table->string('name');
            $table->string('nik')->unique()->nullable()->comment('Nomor Induk Kependudukan');
            $table->string('position')->comment('Jabatan');
            $table->string('division')->nullable()->comment('Divisi/Departemen');
            $table->string('employment_type')->default('tetap')->comment('tetap|kontrak|magang');
            $table->date('join_date');
            $table->date('end_date')->nullable()->comment('Jika kontrak/magang');
            $table->string('status')->default('aktif')->comment('aktif|cuti|nonaktif|resign');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('photo')->nullable();
            $table->decimal('basic_salary', 15, 2)->default(0);
            $table->string('bpjs_kes_no')->nullable();
            $table->string('bpjs_tk_no')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_account_no')->nullable();
            $table->date('birth_date')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // ── ATTENDANCES (Presensi Harian) ─────────────────────────────────────
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->date('attendance_date');
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->string('status')->default('hadir')
                  ->comment('hadir|terlambat|izin|sakit|alpha|libur|cuti');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'attendance_date']);
        });

        // ── PAYROLLS (Penggajian Bulanan) ─────────────────────────────────────
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->date('period')->comment('Bulan/tahun gaji (set ke tgl 1)');
            $table->decimal('basic_salary', 15, 2)->default(0);
            $table->decimal('allowance', 15, 2)->default(0)->comment('Tunjangan (transport, makan, dll)');
            $table->decimal('overtime', 15, 2)->default(0)->comment('Lembur');
            $table->decimal('deduction', 15, 2)->default(0)->comment('Potongan (BPJS, keterlambatan, dll)');
            $table->decimal('net_salary', 15, 2)->storedAs('basic_salary + allowance + overtime - deduction');
            $table->string('status')->default('draft')->comment('draft|approved|paid');
            $table->date('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'period']);
        });

        // ── LEAVES (Cuti & Izin) ──────────────────────────────────────────────
        Schema::create('leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('type')->comment('tahunan|sakit|melahirkan|penting|izin');
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedTinyInteger('days_count')->default(1);
            $table->text('reason');
            $table->string('status')->default('pending')->comment('pending|approved|rejected');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leaves');
        Schema::dropIfExists('payrolls');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('expenses');
    }
};
