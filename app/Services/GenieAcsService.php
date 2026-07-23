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
        try {
            $response = $this->client()->get("{$this->url}/devices", [
                'query' => json_encode(['_id' => $serialNumber])
            ]);

            if ($response->successful() && count($response->json()) > 0) {
                $device = $response->json()[0];
                return [
                    'success' => true,
                    'sn' => $serialNumber,
                    'online' => $device['_lastInform'] ?? null ? (time() - strtotime($device['_lastInform']) < 3600) : false,
                    'rx_power' => 'N/A', // Typically requires custom parameter reading
                    'last_inform' => $device['_lastInform'] ?? 'Unknown',
                    'data' => $device
                ];
            }

            return [
                'success' => false,
                'message' => 'Perangkat tidak ditemukan di GenieACS.'
            ];
        } catch (\Exception $e) {
            Log::error("GenieACS GetStatus Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Gagal menghubungi server GenieACS: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Reboot Device via TR-069
     */
    public function rebootDevice(string $serialNumber): array
    {
        try {
            $response = $this->client()->post("{$this->url}/tasks", [
                'name' => 'reboot',
                'device' => $serialNumber
            ]);

            if ($response->successful()) {
                Log::info("GenieACS: Reboot triggered for SN {$serialNumber}");
                return [
                    'success' => true,
                    'message' => 'Perintah reboot berhasil dikirim ke perangkat.'
                ];
            }

            return [
                'success' => false,
                'message' => 'Gagal mengirim perintah reboot ke GenieACS.'
            ];
        } catch (\Exception $e) {
            Log::error("GenieACS Reboot Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Update WiFi Configuration (SSID and Password)
     */
    public function setWifiConfig(string $serialNumber, string $ssid, string $password): array
    {
        try {
            // Standar path TR-069 untuk WiFi (bisa berbeda tergantung merk ONT/Modem)
            $ssidPath = "InternetGatewayDevice.LANDevice.1.WLANConfiguration.1.SSID";
            $passPath = "InternetGatewayDevice.LANDevice.1.WLANConfiguration.1.PreSharedKey.1.PreSharedKey";

            $response = $this->client()->post("{$this->url}/tasks", [
                'name' => 'setParameterValues',
                'device' => $serialNumber,
                'parameterValues' => [
                    [$ssidPath, $ssid, 'xsd:string'],
                    [$passPath, $password, 'xsd:string']
                ]
            ]);

            if ($response->successful()) {
                Log::info("GenieACS: WiFi Config Updated for SN {$serialNumber} | SSID: {$ssid}");
                
                // Trigger refresh so the modem applies it
                $this->client()->post("{$this->url}/tasks", [
                    'name' => 'refreshObject',
                    'device' => $serialNumber,
                    'objectName' => ''
                ]);

                return [
                    'success' => true,
                    'message' => 'Konfigurasi WiFi berhasil dikirim dan akan segera diterapkan pada modem.'
                ];
            }

            return [
                'success' => false,
                'message' => 'Gagal mengirim konfigurasi WiFi ke GenieACS.'
            ];
        } catch (\Exception $e) {
            Log::error("GenieACS WiFi Config Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }
}
