<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payroll extends Model
{
    protected $fillable = [
        // ── Identitas ─────────────────────────────────────────────────────
        'employee_id',
        'period',
        'status',
        'paid_at',
        'notes',

        // ── Gaji Pokok ───────────────────────────────────────────────────
        'basic_salary',

        // ── Komponen IKR ─────────────────────────────────────────────────
        'fee_ikr_per_pelanggan',
        'jumlah_teknisi_pasang',
        'jumlah_ikr',

        // ── Transport ────────────────────────────────────────────────────
        'hari_hadir',

        // ── Marketing (Referral) ──────────────────────────────────────────
        'jumlah_referral',

        // ── Potongan ─────────────────────────────────────────────────────
        'kasbon',
        'lain_lain_potong',
        'ket_lain_lain',

        // ── Subtotal lama (kompatibilitas) ────────────────────────────────
        'allowance',
        'overtime',
        'deduction',
    ];

    // ── Constants Tarif ───────────────────────────────────────────────────────
    public const TARIF_TRANSPORT       = 5000;   // Rp 5.000 / hari hadir
    public const TARIF_REFERRAL        = 20000;  // Rp 20.000 / pelanggan referral
    public const DEFAULT_FEE_IKR       = 40000;  // Rp 40.000 / pemasangan
    public const DEFAULT_GAJI_POKOK    = 1300000;

    protected function casts(): array
    {
        return [
            'period'  => 'date',
            'paid_at' => 'date',
        ];
    }

    // ── Computed Accessors ────────────────────────────────────────────────────

    /**
     * Tarif IKR per unit pemasangan = fee_IKR / jumlah_teknisi_pasang
     * Guard: jika teknisi = 0, tarif = 0 (hindari bagi nol)
     */
    public function getTarifIkrAttribute(): int
    {
        $teknisi = (int) $this->jumlah_teknisi_pasang;
        if ($teknisi <= 0) return 0;
        return (int) round((int) $this->fee_ikr_per_pelanggan / $teknisi);
    }

    /** Tunjangan IKR = jumlah_ikr × tarif_ikr */
    public function getTunjanganIkrAttribute(): int
    {
        return (int) $this->jumlah_ikr * $this->tarif_ikr;
    }

    /** Tunjangan Transport = hari_hadir × Rp5.000 */
    public function getTunjanganTransportAttribute(): int
    {
        return (int) $this->hari_hadir * self::TARIF_TRANSPORT;
    }

    /** Fee Marketing = jumlah_referral × Rp20.000 */
    public function getFeeMarketingAttribute(): int
    {
        return (int) $this->jumlah_referral * self::TARIF_REFERRAL;
    }

    /** Total Pendapatan = Gaji Pokok + IKR + Transport + Fee Marketing */
    public function getTotalPendapatanAttribute(): int
    {
        return (int) $this->basic_salary
             + $this->tunjangan_ikr
             + $this->tunjangan_transport
             + $this->fee_marketing;
    }

    /** Total Potongan = Kasbon + Lain-lain (bernilai positif, dikurangi saat hitung total) */
    public function getTotalPotonganAttribute(): int
    {
        return (int) $this->kasbon + (int) $this->lain_lain_potong;
    }

    /** Total Gaji Diterima = Total Pendapatan - Total Potongan */
    public function getNetSalaryAttribute(): int
    {
        return $this->total_pendapatan - $this->total_potongan;
    }

    /** Masa kerja karyawan dihitung dari join_date ke periode */
    public function getMasaKerjaAttribute(): string
    {
        if (!$this->employee || !$this->employee->join_date || !$this->period) {
            return '—';
        }
        $diff = $this->employee->join_date->diff($this->period);
        $parts = [];
        if ($diff->y > 0) $parts[] = $diff->y . ' tahun';
        if ($diff->m > 0) $parts[] = $diff->m . ' bulan';
        return empty($parts) ? 'Baru bergabung' : implode(' ', $parts);
    }

    /** Apakah potongan melebihi pendapatan? */
    public function hasNegativeWarning(): bool
    {
        return $this->total_potongan > $this->total_pendapatan;
    }

    // ── Relasi ────────────────────────────────────────────────────────────────

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
