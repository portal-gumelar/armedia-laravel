<?php

namespace App\Models\Radius;

use Illuminate\Database\Eloquent\Model;

class RadCheck extends Model
{
    // The table name in freeradius
    protected $table = 'radcheck';

    // FreeRADIUS tables typically don't use Laravel's timestamps
    public $timestamps = false;

    protected $fillable = [
        'username',
        'attribute',
        'op',
        'value',
    ];
}
