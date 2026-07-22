<?php

namespace App\Observers;

use App\Models\Customer;
use App\Services\RadiusService;

class CustomerObserver
{
    /**
     * Handle the Customer "created" event.
     */
    public function created(Customer $customer): void
    {
        $this->syncToRadius($customer);
        $this->syncToMikrotik($customer);
    }

    public function updated(Customer $customer): void
    {
        // Jika password atau username pppoe berubah, atau status berlangganan berubah
        if ($customer->isDirty('pppoe_username') || $customer->isDirty('pppoe_password') || $customer->isDirty('subscription_status') || $customer->isDirty('mikrotik_server_id')) {
            $this->syncToRadius($customer);
            $this->syncToMikrotik($customer);
        }
    }

    /**
     * Handle the Customer "deleted" event.
     */
    public function deleted(Customer $customer): void
    {
        if ($customer->pppoe_username) {
            $radius = new RadiusService();
            $radius->deleteUser($customer->pppoe_username);
            
            $mikrotik = new \App\Services\MikrotikService();
            $mikrotik->removePppoeSecret($customer);
        }
    }

    private function syncToRadius(Customer $customer): void
    {
        if (!$customer->pppoe_username || !$customer->pppoe_password) {
            return;
        }

        $radius = new RadiusService();
        $profile = $customer->internetPackage?->nama_paket ?? 'DEFAULT';

        // Jika status Isolir, ganti profil ke ISOLIR
        if ($customer->subscription_status?->value === 'ISOLIR' || $customer->subscription_status === 'ISOLIR') {
            $profile = 'ISOLIR';
        }

        $radius->createUser($customer->pppoe_username, $customer->pppoe_password, $profile);
    }

    private function syncToMikrotik(Customer $customer): void
    {
        if ($customer->mikrotik_server_id && $customer->pppoe_username) {
            $mikrotik = new \App\Services\MikrotikService();
            $mikrotik->createPppoeSecret($customer);
        }
    }
}
