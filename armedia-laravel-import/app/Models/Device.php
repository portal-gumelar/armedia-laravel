<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Device extends Model
{
    protected $fillable = [
        'nama', 'model', 'kode_id', 'sn', 'tgl_ambil_dari_stok', 'status',
        'customer_id', 'id_lama_referensi', 'kondisi', 'catatan',
    ];

    protected $casts = [
        'tgl_ambil_dari_stok' => 'date',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
