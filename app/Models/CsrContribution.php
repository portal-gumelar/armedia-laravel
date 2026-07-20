<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CsrContribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'village_id',
        'rw',
        'rt',
        'period',
        'customer_count',
        'csr_total',
        'desa_share',
        'rt_share',
    ];

    protected function casts(): array
    {
        return [
            'period' => 'date',
        ];
    }

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }
}
