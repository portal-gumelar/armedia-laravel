<?php

namespace App\Services;

use App\Models\Payroll;

class PayrollSlipService
{
    // ── Konstanta Tarif ───────────────────────────────────────────────────────
    public const TARIF_TRANSPORT    = 5000;   // per hari hadir
    public const TARIF_REFERRAL     = 20000;  // per pelanggan referral

    /**
     * Hitung semua komponen gaji dari data input form.
     * Mengembalikan array siap simpan ke tabel `payrolls`.
     *
     * @param  array $inputs  Data mentah dari form Filament
     * @return array          Data kalkulasi lengkap
     */
    public function buildFromInputs(array $inputs): array
    {
        $gajiPokok          = (int) ($inputs['basic_salary']           ?? 0);
        $feeIkrPerPelanggan = (int) ($inputs['fee_ikr_per_pelanggan']  ?? 40000);
        $jumlahTeknisi      = (int) ($inputs['jumlah_teknisi_pasang']  ?? 1);
        $jumlahIkr          = (int) ($inputs['jumlah_ikr']             ?? 0);
        $hariHadir          = (int) ($inputs['hari_hadir']             ?? 0);
        $jumlahReferral     = (int) ($inputs['jumlah_referral']        ?? 0);
        $kasbon             = (int) ($inputs['kasbon']                 ?? 0);
        $lainLain           = (int) ($inputs['lain_lain_potong']       ?? 0);

        // ── Hitung Tarif IKR (guard bagi nol) ────────────────────────────
        $tarifIkr = $jumlahTeknisi > 0
            ? (int) round($feeIkrPerPelanggan / $jumlahTeknisi)
            : 0;

        // ── Hitung Komponen Pendapatan ────────────────────────────────────
        $tunjanganIkr       = $jumlahIkr      * $tarifIkr;
        $tunjanganTransport = $hariHadir      * self::TARIF_TRANSPORT;
        $feeMarketing       = $jumlahReferral * self::TARIF_REFERRAL;
        $totalPendapatan    = $gajiPokok + $tunjanganIkr + $tunjanganTransport + $feeMarketing;

        // ── Hitung Potongan ───────────────────────────────────────────────
        $totalPotongan = $kasbon + $lainLain;

        // ── Total Akhir ───────────────────────────────────────────────────
        $totalDiterima = $totalPendapatan - $totalPotongan;

        return [
            // Input yang disimpan verbatim
            'basic_salary'          => $gajiPokok,
            'fee_ikr_per_pelanggan' => $feeIkrPerPelanggan,
            'jumlah_teknisi_pasang' => $jumlahTeknisi,
            'jumlah_ikr'            => $jumlahIkr,
            'hari_hadir'            => $hariHadir,
            'jumlah_referral'       => $jumlahReferral,
            'kasbon'                => $kasbon,
            'lain_lain_potong'      => $lainLain,

            // Computed (untuk referensi di field lain)
            '_tarif_ikr'            => $tarifIkr,
            '_tunjangan_ikr'        => $tunjanganIkr,
            '_tunjangan_transport'  => $tunjanganTransport,
            '_fee_marketing'        => $feeMarketing,
            '_total_pendapatan'     => $totalPendapatan,
            '_total_potongan'       => $totalPotongan,
            '_total_diterima'       => $totalDiterima,
            '_has_warning'          => $totalPotongan > $totalPendapatan,
        ];
    }

    /**
     * Hitung ringkasan untuk ditampilkan di live preview form.
     * Sama dengan buildFromInputs tapi mengembalikan hanya field display.
     */
    public function previewFromState(array $state): array
    {
        $calc = $this->buildFromInputs($state);
        return [
            'tarif_ikr'           => $calc['_tarif_ikr'],
            'tunjangan_ikr'       => $calc['_tunjangan_ikr'],
            'tunjangan_transport' => $calc['_tunjangan_transport'],
            'fee_marketing'       => $calc['_fee_marketing'],
            'total_pendapatan'    => $calc['_total_pendapatan'],
            'total_potongan'      => $calc['_total_potongan'],
            'total_diterima'      => $calc['_total_diterima'],
            'has_warning'         => $calc['_has_warning'],
        ];
    }

    /**
     * Validasi khusus PRD edge cases.
     * Mengembalikan array pesan error, atau array kosong jika valid.
     */
    public function validate(array $inputs): array
    {
        $errors = [];

        if ((int) ($inputs['jumlah_teknisi_pasang'] ?? 0) === 0) {
            $errors[] = 'Jumlah teknisi = 0, tarif IKR akan dihitung Rp0.';
        }

        $preview = $this->previewFromState($inputs);
        if ($preview['has_warning']) {
            $errors[] = 'Peringatan: Total potongan (Rp ' . number_format($preview['total_potongan']) . ') melebihi total pendapatan (Rp ' . number_format($preview['total_pendapatan']) . '). Total diterima akan NEGATIF!';
        }

        return $errors;
    }
}
