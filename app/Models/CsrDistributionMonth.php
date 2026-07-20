<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CsrDistributionMonth extends Model
{
    protected $fillable = ['csr_distribution_id', 'bulan', 'jumlah_pelanggan', 'jumlah_csr'];

    public function csrDistribution(): BelongsTo
    {
        return $this->belongsTo(CsrDistribution::class);
    }
}
