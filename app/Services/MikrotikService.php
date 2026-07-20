<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\MikrotikServer;
use RouterOS\Client;
use RouterOS\Query;
use Illuminate\Support\Facades\Log;

class MikrotikService
{
    /**
     * Connect to the Mikrotik Server.
     */
    public function connect(MikrotikServer $server): ?Client
    {
        try {
            $client = new Client([
                'host' => $server->host,
                'user' => $server->username,
                'pass' => $server->password,
                'port' => (int) $server->port,
            ]);
            return $client;
        } catch (\Exception $e) {
            Log::error("Failed to connect to Mikrotik Server {$server->name}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Isolate a customer based on their PPPoE secret or IP Address.
     */
    public function isolateCustomer(Customer $customer): bool
    {
        if (!$customer->mikrotikServer) {
            Log::warning("Customer {$customer->id_arm} has no associated Mikrotik Server.");
            return false;
        }

        $client = $this->connect($customer->mikrotikServer);
        if (!$client) {
            return false;
        }

        try {
            // Option A: PPPoE Disable Method
            if ($customer->pppoe_username) {
                // Find secret
                $query = (new Query('/ppp/secret/print'))->where('name', $customer->pppoe_username);
                $secrets = $client->query($query)->read();
                
                if (!empty($secrets)) {
                    $secretId = $secrets[0]['.id'];
                    // Disable the secret
                    $client->query(
                        (new Query('/ppp/secret/set'))
                            ->equal('.id', $secretId)
                            ->equal('disabled', 'yes')
                    )->read();

                    // Disconnect active PPPoE session so they get disconnected immediately
                    $activeQuery = (new Query('/ppp/active/print'))->where('name', $customer->pppoe_username);
                    $actives = $client->query($activeQuery)->read();
                    if (!empty($actives)) {
                        $client->query(
                            (new Query('/ppp/active/remove'))
                                ->equal('.id', $actives[0]['.id'])
                        )->read();
                    }
                }
            }
            // Option B: Address List Method (for Static IP)
            elseif ($customer->ip_address) {
                $client->query(
                    (new Query('/ip/firewall/address-list/add'))
                        ->equal('list', 'ISOLIR')
                        ->equal('address', $customer->ip_address)
                        ->equal('comment', "Auto-Isolir: " . $customer->id_arm)
                )->read();
            }

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to isolate Customer {$customer->id_arm}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Un-isolate a customer based on their PPPoE secret or IP Address.
     */
    public function unisolateCustomer(Customer $customer): bool
    {
        if (!$customer->mikrotikServer) {
            return false;
        }

        $client = $this->connect($customer->mikrotikServer);
        if (!$client) {
            return false;
        }

        try {
            // Option A: PPPoE Enable Method
            if ($customer->pppoe_username) {
                $query = (new Query('/ppp/secret/print'))->where('name', $customer->pppoe_username);
                $secrets = $client->query($query)->read();
                
                if (!empty($secrets)) {
                    $secretId = $secrets[0]['.id'];
                    $client->query(
                        (new Query('/ppp/secret/set'))
                            ->equal('.id', $secretId)
                            ->equal('disabled', 'no')
                    )->read();
                }
            }
            // Option B: Address List Remove (for Static IP)
            elseif ($customer->ip_address) {
                $query = (new Query('/ip/firewall/address-list/print'))
                            ->where('list', 'ISOLIR')
                            ->where('address', $customer->ip_address);
                $lists = $client->query($query)->read();

                if (!empty($lists)) {
                    $client->query(
                        (new Query('/ip/firewall/address-list/remove'))
                            ->equal('.id', $lists[0]['.id'])
                    )->read();
                }
            }

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to un-isolate Customer {$customer->id_arm}: " . $e->getMessage());
            return false;
        }
    }
}
