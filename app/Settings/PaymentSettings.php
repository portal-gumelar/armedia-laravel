<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class PaymentSettings extends Settings
{
    public string $midtrans_server_key;
    public string $midtrans_client_key;
    public bool $midtrans_is_production;

    public static function group(): string
    {
        return 'payment';
    }
}
