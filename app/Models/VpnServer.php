<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VpnServer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'host',
        'port',
        'username',
        'password',
        'type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'password' => 'encrypted',
    ];
}
