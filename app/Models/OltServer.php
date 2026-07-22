<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OltServer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'host',
        'port',
        'username',
        'password',
        'snmp_community',
        'type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'password' => 'encrypted',
        'snmp_community' => 'encrypted',
    ];

    public function ports(): HasMany
    {
        return $this->hasMany(OltPort::class);
    }
}
