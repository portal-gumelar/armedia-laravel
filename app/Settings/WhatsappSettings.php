<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class WhatsappSettings extends Settings
{
    public string $waha_endpoint;
    public string $waha_session;
    public bool $is_active;

    public static function group(): string
    {
        return 'whatsapp';
    }
}
