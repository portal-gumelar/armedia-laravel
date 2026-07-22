<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OnuDevice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'olt_port_id',
        'customer_id',
        'sn',
        'onu_id',
        'status',
        'rx_power',
        'last_online_at',
        'last_offline_at',
    ];

    protected $casts = [
        'last_online_at' => 'datetime',
        'last_offline_at' => 'datetime',
    ];

    public function port(): BelongsTo
    {
        return $this->belongsTo(OltPort::class, 'olt_port_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
