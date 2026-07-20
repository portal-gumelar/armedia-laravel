<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CsrDistribution extends Model
{
    protected $fillable = [
        'no', 'nama', 'provinsi', 'kabupaten', 'kecamatan', 'desa', 'rw', 'rt',
        'total', 'dana_desa', 'dana_rt', 'status_pencairan', 'tgl_bayar',
    ];

    protected $casts = [
        'tgl_bayar' => 'date',
    ];

    public function months(): HasMany
    {
        return $this->hasMany(CsrDistributionMonth::class);
    }
}
