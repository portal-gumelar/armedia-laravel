<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthlyBill extends Model
{
    protected $fillable = ['customer_id', 'tahun', 'bulan', 'jumlah', 'harga_acuan_snapshot'];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
