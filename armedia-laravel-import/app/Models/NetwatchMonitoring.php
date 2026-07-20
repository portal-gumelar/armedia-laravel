<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NetwatchMonitoring extends Model
{
    protected $fillable = [
        'ip_address', 'status_koneksi', 'customer_id', 'desa', 'rw_rt',
        'paket_mbps', 'status_berlangganan', 'chat_wa', 'wa_follow_up_gangguan',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
