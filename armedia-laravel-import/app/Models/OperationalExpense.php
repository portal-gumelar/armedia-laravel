<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperationalExpense extends Model
{
    protected $fillable = ['nota', 'operasional', 'qty', 'harga_satuan', 'total_harga'];
}
