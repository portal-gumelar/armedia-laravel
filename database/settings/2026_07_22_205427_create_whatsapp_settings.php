<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('whatsapp.waha_endpoint', env('WAHA_ENDPOINT', 'http://localhost:3000'));
        $this->migrator->add('whatsapp.waha_session', env('WAHA_SESSION', 'default'));
        $this->migrator->add('whatsapp.is_active', false);
    }
};
