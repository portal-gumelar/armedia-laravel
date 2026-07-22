<?php

namespace App\Services;

use App\Models\Customer;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GenieAcsService
{
    protected string $url;
    protected string $username;
    protected string $password;
    protected int $timeout;

    public function __construct()
    {
        $this->url = rtrim(config('genieacs.url', 'http://127.0.0.1:3005'), '/');
        $this->username = config('genieacs.username', '');
        $this->password = config('genieacs.password', '');
        $this->timeout = config('genieacs.timeout', 10);
    }

    /**
     * Build HTTP Client with Authentication
     */
    protected function client()
    {
        $client = Http::timeout($this->timeout);
        
        if (!empty($this->username) || !empty($this->password)) {
            $client->withBasicAuth($this->username, $this->password);
        }

        return $client;
    }

    /**
     * Get Device Status / Info from ACS by SN
     */
    public function getDeviceStatus(string $serialNumber): array
    {
        // Mock Implementation for now
        // In real TR-069: query `/devices/?query={"_id":"$serialNumber"}`
        return [
            'success' => true,
            'sn' => $serialNumber,
            'online' => (bool) rand(0, 1),
            'rx_power' => rand(-25, -15) . ' dBm',
            'last_inform' => now()->subMinutes(rand(1, 60))->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Reboot Device via TR-069
     */
    public function rebootDevice(string $serialNumber): array
    {
        // Mock Implementation
        // In real TR-069: send task `{"name":"reboot", "device":"$serialNumber"}` to `/tasks`
        Log::info("GenieACS: Reboot triggered for SN {$serialNumber}");
        
        return [
            'success' => true,
            'message' => 'Perintah reboot berhasil dikirim ke perangkat.'
        ];
    }

    /**
     * Update WiFi Configuration (SSID and Password)
     */
    public function setWifiConfig(string $serialNumber, string $ssid, string $password): array
    {
        // Mock Implementation
        // In real TR-069: queue SetParameterValues tasks for InternetGatewayDevice.LANDevice.1.WLANConfiguration.1.SSID etc.
        Log::info("GenieACS: WiFi Config Updated for SN {$serialNumber} | SSID: {$ssid}");
        
        return [
            'success' => true,
            'message' => 'Konfigurasi WiFi berhasil dikirim dan akan segera diterapkan pada modem.'
        ];
    }
}
