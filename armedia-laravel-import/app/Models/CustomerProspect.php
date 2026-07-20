<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerProspect extends Model
{
    protected $fillable = [
        'no_laporan', 'nama_pelanggan', 'nomor_telepon', 'alamat_pemasangan',
        'jadwal_pasang', 'jenis_layanan', 'status', 'marketing',
    ];

    protected $casts = [
        'jadwal_pasang' => 'date',
    ];
}
