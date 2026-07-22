<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MidtransWebhookLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'transaction_status',
        'fraud_status',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
    ];
}
