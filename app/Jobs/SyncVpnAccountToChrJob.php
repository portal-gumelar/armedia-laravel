<?php

namespace App\Jobs;

use App\Models\VpnAccount;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SyncVpnAccountToChrJob implements ShouldQueue
{
    use Queueable;

    public $vpnAccount;

    /**
     * Create a new job instance.
     */
    public function __construct(VpnAccount $vpnAccount)
    {
        $this->vpnAccount = $vpnAccount;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $server = $this->vpnAccount->vpnServer;
        
        if (!$server || !$server->is_active) {
            return;
        }

        try {
            $client = new \RouterOS\Client([
                'host' => $server->host,
                'user' => $server->username,
                'pass' => $server->password,
                'port' => (int) $server->port,
            ]);

            // Create PPP Secret
            $query = new \RouterOS\Query('/ppp/secret/add');
            $query->equal('name', $this->vpnAccount->username);
            $query->equal('password', $this->vpnAccount->password);
            
            if ($this->vpnAccount->vpn_type === 'wireguard') {
                // Ignore for now, basic implementation is PPP
            } else {
                $query->equal('service', $this->vpnAccount->vpn_type);
            }

            if ($this->vpnAccount->ip_lokal) {
                $query->equal('remote-address', $this->vpnAccount->ip_lokal);
            }

            if (!$this->vpnAccount->is_active) {
                $query->equal('disabled', 'yes');
            }

            $client->query($query)->read();

        } catch (\Exception $e) {
            \Log::error('Failed to sync VPN to CHR: ' . $e->getMessage());
        }
    }
}
