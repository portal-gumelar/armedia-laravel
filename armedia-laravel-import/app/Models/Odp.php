<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Odp extends Model
{
    protected $fillable = ['kode_odp', 'port_terpakai', 'kapasitas_maks', 'sisa_slot', 'status', 'desa_lokasi'];

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }
}
