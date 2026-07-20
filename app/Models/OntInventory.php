<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OntInventory extends Model
{
    protected $fillable = [
        'tipe', 'nama_barang', 'merek', 'sn', 'mac_address', 'ip_address',
        'ssid_2g', 'ssid_5g', 'jumlah', 'keterangan', 'status', 'tgl_keluar',
        'teknisi', 'customer_id',
    ];

    protected $casts = [
        'tgl_keluar' => 'date',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
