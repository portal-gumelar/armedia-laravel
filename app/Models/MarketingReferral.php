<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketingReferral extends Model
{
    protected $fillable = [
        'nama_marketing', 'lokasi', 'nama_client', 'jumlah_fee', 'tgl_daftar', 'sumber_data',
    ];

    protected $casts = [
        'tgl_daftar' => 'date',
    ];
}
