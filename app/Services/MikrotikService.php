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

    /**
     * Add a host to Netwatch for monitoring
     * 
     * @param MikrotikServer $server
     * @param string $ip
     * @param string $comment Format: NAMA - SN - HP - SSID - RT/RW - DESA
     * @return bool
     */
    public function addNetwatchHost(MikrotikServer $server, string $ip, string $comment): bool
    {
        $client = $this->connect($server);
        if (!$client) {
            return false;
        }

        try {
            // Cek apakah host sudah ada
            $query = (new Query('/tool/netwatch/print'))->where('host', $ip);
            $existing = $client->query($query)->read();

            if (!empty($existing)) {
                // Update jika sudah ada
                $client->query(
                    (new Query('/tool/netwatch/set'))
                        ->equal('.id', $existing[0]['.id'])
                        ->equal('comment', $comment)
                )->read();
            } else {
                // Tambah baru jika belum ada
                $client->query(
                    (new Query('/tool/netwatch/add'))
                        ->equal('host', $ip)
                        ->equal('interval', '3m')
                        ->equal('comment', $comment)
                )->read();
            }

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to add Netwatch host {$ip}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update the PPPoE profile for a customer (Bandwidth Sync).
     */
    public function updateSecretProfile(Customer $customer, string $newProfile): bool
    {
        if (!$customer->mikrotikServer || !$customer->pppoe_username) {
            return false;
        }

        $client = $this->connect($customer->mikrotikServer);
        if (!$client) {
            return false;
        }

        try {
            $query = (new Query('/ppp/secret/print'))->where('name', $customer->pppoe_username);
            $secrets = $client->query($query)->read();
            
            if (!empty($secrets)) {
                $client->query(
                    (new Query('/ppp/secret/set'))
                        ->equal('.id', $secrets[0]['.id'])
                        ->equal('profile', $newProfile)
                )->read();

                // Reconnect active session so new profile applies
                $activeQuery = (new Query('/ppp/active/print'))->where('name', $customer->pppoe_username);
                $actives = $client->query($activeQuery)->read();
                if (!empty($actives)) {
                    $client->query(
                        (new Query('/ppp/active/remove'))
                            ->equal('.id', $actives[0]['.id'])
                    )->read();
                }

                return true;
            }
            return false;
        } catch (\Exception $e) {
            Log::error("Failed to update PPPoE profile for Customer {$customer->id_arm}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Deploy Security Rules to Mikrotik (Anti Brute Force & Drops).
     */
    public function deploySecurityRules(MikrotikServer $server): bool
    {
        $client = $this->connect($server);
        if (!$client) {
            return false;
        }

        try {
            // Drop invalid packets
            $client->query(
                (new Query('/ip/firewall/filter/add'))
                    ->equal('chain', 'input')
                    ->equal('connection-state', 'invalid')
                    ->equal('action', 'drop')
                    ->equal('comment', 'ARM-SEC: Drop Invalid Packets')
            )->read();

            // FTP Brute force mitigation
            $client->query(
                (new Query('/ip/firewall/filter/add'))
                    ->equal('chain', 'input')
                    ->equal('protocol', 'tcp')
                    ->equal('dst-port', '21')
                    ->equal('src-address-list', 'ftp_blacklist')
                    ->equal('action', 'drop')
                    ->equal('comment', 'ARM-SEC: Drop FTP Brute Force')
            )->read();

            // SSH Brute force mitigation
            $client->query(
                (new Query('/ip/firewall/filter/add'))
                    ->equal('chain', 'input')
                    ->equal('protocol', 'tcp')
                    ->equal('dst-port', '22')
                    ->equal('src-address-list', 'ssh_blacklist')
                    ->equal('action', 'drop')
                    ->equal('comment', 'ARM-SEC: Drop SSH Brute Force')
            )->read();

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to deploy security rules to Server {$server->name}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Auto-provision: Create PPPoE Secret for new active customer.
     */
    public function createPppoeSecret(Customer $customer): bool
    {
        if (!$customer->mikrotikServer || !$customer->pppoe_username) {
            return false;
        }

        $client = $this->connect($customer->mikrotikServer);
        if (!$client) {
            return false;
        }

        try {
            // Check if exist
            $query = (new Query('/ppp/secret/print'))->where('name', $customer->pppoe_username);
            $existing = $client->query($query)->read();

            if (empty($existing)) {
                $client->query(
                    (new Query('/ppp/secret/add'))
                        ->equal('name', $customer->pppoe_username)
                        ->equal('password', $customer->pppoe_password ?? '123456')
                        ->equal('profile', $customer->profile ?? 'default')
                        ->equal('service', 'pppoe')
                        ->equal('comment', "Auto-provision: {$customer->id_arm} - {$customer->name}")
                )->read();
            } else {
                // Update existing
                $client->query(
                    (new Query('/ppp/secret/set'))
                        ->equal('.id', $existing[0]['.id'])
                        ->equal('password', $customer->pppoe_password ?? '123456')
                        ->equal('profile', $customer->profile ?? 'default')
                        ->equal('comment', "Auto-provision: {$customer->id_arm} - {$customer->name}")
                )->read();
            }

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to create PPPoE Secret for Customer {$customer->id_arm}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Remove PPPoE Secret from Mikrotik.
     */
    public function removePppoeSecret(Customer $customer): bool
    {
        if (!$customer->mikrotikServer || !$customer->pppoe_username) {
            return false;
        }

        $client = $this->connect($customer->mikrotikServer);
        if (!$client) {
            return false;
        }

        try {
            $query = (new Query('/ppp/secret/print'))->where('name', $customer->pppoe_username);
            $existing = $client->query($query)->read();

            if (!empty($existing)) {
                $client->query(
                    (new Query('/ppp/secret/remove'))
                        ->equal('.id', $existing[0]['.id'])
                )->read();
            }

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to remove PPPoE Secret for Customer {$customer->id_arm}: " . $e->getMessage());
            return false;
        }
    }
}
