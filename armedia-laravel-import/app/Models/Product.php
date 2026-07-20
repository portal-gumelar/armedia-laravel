<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = ['kode', 'nama', 'kapasitas_mbps', 'harga', 'alokasi_ip'];

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function prorataRates(): HasMany
    {
        return $this->hasMany(ProrataRate::class);
    }
}
