<?php

namespace App\Models\Radius;

use Illuminate\Database\Eloquent\Model;

class RadCheck extends Model
{
    // Point to the radius database connection
    protected $connection = 'radius';
    
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
