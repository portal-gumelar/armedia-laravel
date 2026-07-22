<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class VpnAccount extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'vpn_server_id',
        'customer_id',
        'mikrotik_server_id',
        'username',
        'password',
        'ip_lokal',
        'port_forwarding',
        'vpn_type',
        'is_active',
        'expired_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'expired_at' => 'datetime',
    ];

    public function vpnServer()
    {
        return $this->belongsTo(VpnServer::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function mikrotikServer()
    {
        return $this->belongsTo(MikrotikServer::class);
    }
}
