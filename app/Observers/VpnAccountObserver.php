<?php

namespace App\Observers;

use App\Models\VpnAccount;

use App\Jobs\SyncVpnAccountToChrJob;

class VpnAccountObserver
{
    /**
     * Handle the VpnAccount "created" event.
     */
    public function created(VpnAccount $vpnAccount): void
    {
        SyncVpnAccountToChrJob::dispatch($vpnAccount);
    }

    /**
     * Handle the VpnAccount "updated" event.
     */
    public function updated(VpnAccount $vpnAccount): void
    {
        SyncVpnAccountToChrJob::dispatch($vpnAccount);
    }

    /**
     * Handle the VpnAccount "deleted" event.
     */
    public function deleted(VpnAccount $vpnAccount): void
    {
        //
    }

    /**
     * Handle the VpnAccount "restored" event.
     */
    public function restored(VpnAccount $vpnAccount): void
    {
        //
    }

    /**
     * Handle the VpnAccount "force deleted" event.
     */
    public function forceDeleted(VpnAccount $vpnAccount): void
    {
        //
    }
}
