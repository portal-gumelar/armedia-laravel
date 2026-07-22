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
    }

    /**
     * Handle the Customer "updated" event.
     */
    public function updated(Customer $customer): void
    {
        // Jika password atau username pppoe berubah, atau status berlangganan berubah
        if ($customer->isDirty('username_pppoe') || $customer->isDirty('password_pppoe') || $customer->isDirty('subscription_status')) {
            $this->syncToRadius($customer);
        }
    }

    /**
     * Handle the Customer "deleted" event.
     */
    public function deleted(Customer $customer): void
    {
        if ($customer->username_pppoe) {
            $radius = new RadiusService();
            $radius->deleteUser($customer->username_pppoe);
        }
    }

    private function syncToRadius(Customer $customer): void
    {
        if (!$customer->username_pppoe || !$customer->password_pppoe) {
            return;
        }

        $radius = new RadiusService();
        $profile = $customer->internetPackage?->nama_paket ?? 'DEFAULT';

        // Jika status Isolir, ganti profil ke ISOLIR
        if ($customer->subscription_status?->value === 'ISOLIR' || $customer->subscription_status === 'ISOLIR') {
            $profile = 'ISOLIR';
        }

        $radius->createUser($customer->username_pppoe, $customer->password_pppoe, $profile);
    }
}
