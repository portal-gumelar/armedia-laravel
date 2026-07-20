<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProrataRate extends Model
{
    protected $fillable = ['tanggal_pasang', 'product_id', 'jumlah'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
