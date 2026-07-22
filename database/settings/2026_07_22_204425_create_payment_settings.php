<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('payment.midtrans_server_key', env('MIDTRANS_SERVER_KEY', ''));
        $this->migrator->add('payment.midtrans_client_key', env('MIDTRANS_CLIENT_KEY', ''));
        $this->migrator->add('payment.midtrans_is_production', env('MIDTRANS_IS_PRODUCTION', false));
    }
};
