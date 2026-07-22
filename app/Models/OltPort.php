<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OltPort extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'olt_server_id',
        'slot',
        'port',
        'max_capacity',
    ];

    public function server(): BelongsTo
    {
        return $this->belongsTo(OltServer::class, 'olt_server_id');
    }

    public function onus(): HasMany
    {
        return $this->hasMany(OnuDevice::class);
    }
}
