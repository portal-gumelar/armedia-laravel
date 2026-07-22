<?php

namespace App\Services;

use App\Models\OltServer;
use Illuminate\Support\Facades\Log;
use phpseclib3\Net\SSH2;
use Exception;

class ZteOltService
{
    protected OltServer $server;
    protected ?SSH2 $ssh = null;

    public function __construct(OltServer $server)
    {
        $this->server = $server;
    }

    /**
     * Connect to the OLT via SSH.
     */
    protected function connect(): SSH2
    {
        if ($this->ssh !== null && $this->ssh->isConnected()) {
            return $this->ssh;
        }

        $ssh = new SSH2($this->server->host, $this->server->port ?? 22);
        $ssh->setTimeout(10);
        
        if (!$ssh->login($this->server->username, $this->server->password)) {
            throw new Exception("Login SSH ke OLT {$this->server->name} gagal. Periksa kredensial.");
        }

        $this->ssh = $ssh;
        $this->ssh->exec('terminal length 0'); // Matikan paging output

        return $this->ssh;
    }

    /**
     * Dapatkan daftar ONU yang belum dikonfigurasi (UNCFG)
     */
    public function getUnconfiguredOnus(): array
    {
        $ssh = $this->connect();
        $output = $ssh->exec('show gpon onu uncfg');
        
        $unconfigured = [];
        if ($output) {
            $lines = explode("\n", $output);
            foreach ($lines as $line) {
                // regex match: gpon-olt_1/1/PORT  INDEX  SN  unknown
                if (preg_match('/gpon-olt_1\/1\/(\d+)\s+\d+\s+([A-Z0-9]{12})/i', $line, $matches)) {
                    $unconfigured[] = [
                        'port' => (int) $matches[1],
                        'sn'   => $matches[2],
                        'raw'  => trim($line)
                    ];
                }
            }
        }
        return $unconfigured;
    }

    /**
     * Cari index kosong pertama (1-128) pada port tertentu
     */
    public function findEmptyIndex(int $port): int
    {
        $ssh = $this->connect();
        $output = $ssh->exec("show gpon onu state gpon-olt_1/1/{$port}");
        
        $usedIndexes = [];
        if ($output) {
            $lines = explode("\n", $output);
            foreach ($lines as $line) {
                // regex match: gpon-onu_1/1/PORT:INDEX
                if (preg_match('/gpon-onu_1\/1\/\d+:(\d+)/i', $line, $matches)) {
                    $usedIndexes[] = (int) $matches[1];
                }
            }
        }
        
        for ($i = 1; $i <= 128; $i++) {
            if (!in_array($i, $usedIndexes)) {
                return $i;
            }
        }
        
        throw new Exception("Tidak ada index kosong di Port {$port}");
    }

    /**
     * Daftarkan ONU ke OLT.
     */
    public function registerOnu(string $port, string $index, string $sn, string $profile, string $name, string $ssid, string $ip, string $phone, string $vlan = '1521'): array
    {
        try {
            $ssh = $this->connect();
            $name = str_replace(' ', '_', $name);
            
            $commands = [
                "configure terminal",
                "interface gpon-olt_1/1/{$port}",
                "onu {$index} type all sn {$sn}",
                "exit",
                "interface gpon-onu_1/1/{$port}:{$index}",
                "name {$name}",
                "description {$ssid}-{$ip}-{$phone}",
                "sn-bind enable sn",
                "tcont 1 profile {$profile}",
                "gemport 1 tcont 1",
                "service-port 1 vport 1 user-vlan {$vlan} vlan {$vlan}",
                "exit",
                "pon-onu-mng gpon-onu_1/1/{$port}:{$index}",
                "flow mode 1 tag-filter vlan-filter untag-filter discard",
                "flow 1 pri 0 vlan {$vlan}",
                "gemport 1 flow 1",
                "exit",
                "end",
                "write"
            ];

            $script = implode("\n", $commands) . "\n";
            Log::info("Mengeksekusi registrasi OLT untuk SN {$sn} di Port {$port} Index {$index}");
            
            $output = $ssh->exec($script);

            return [
                'success' => true,
                'onu_id' => $index,
                'message' => 'ONU registered successfully on OLT'
            ];
        } catch (\Exception $e) {
            Log::error("OLT Registration Error: " . $e->getMessage());
            return [
                'success' => false,
                'onu_id' => null,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Mengambil status RX Power dari ONU via SNMP
     * Menggunakan exec('snmpget') 
     */
    public function getOnuRxPower(string $onuIndex): ?float
    {
        if (!$this->server->snmp_community) {
            return null;
        }

        // OID ZTE untuk ONU RX Power biasanya .1.3.6.1.4.1.3902.1012.3.50.12.1.1.10
        // (Nilai biasanya di-return dalam bentuk integer, misalnya -25000 -> -25.00 dBm)
        $oid = ".1.3.6.1.4.1.3902.1012.3.50.12.1.1.10.{$onuIndex}";
        
        $command = "snmpget -v2c -c '{$this->server->snmp_community}' -t 2 -r 1 '{$this->server->host}' {$oid}";
        $output = shell_exec($command);

        if ($output && preg_match('/INTEGER:\s*(-?\d+)/', $output, $matches)) {
            $val = (int)$matches[1];
            if ($val == 65535 || $val == -65535) return null; // Biasanya indikasi LOS / Tidak Terbaca
            
            // Konversi nilai (-25000 / 1000 = -25.0 dBm) atau konvensi ZTE (dibagi 200 atau 1000)
            // Umumnya ZTE C320 RX OID membagi 1000 atau 256. 
            // Kita asumsikan format standar ZTE = nilai / 1000 jika besar, atau nilai / 10000 
            // Untuk amannya, kita asumsikan / 1000.
            return round($val / 1000, 2);
        }

        return null;
    }

    /**
     * Mengambil Status ONU (Online/Offline) via SNMP
     */
    public function getOnuStatus(string $onuIndex): string
    {
        if (!$this->server->snmp_community) {
            return 'offline';
        }

        // OID ZTE untuk ONU Status: .1.3.6.1.4.1.3902.1012.3.28.1.1.5 (1=working, 2=los, 3=dying gasp, 4=offline)
        $oid = ".1.3.6.1.4.1.3902.1012.3.28.1.1.5.{$onuIndex}";
        
        $command = "snmpget -v2c -c '{$this->server->snmp_community}' -t 2 -r 1 '{$this->server->host}' {$oid}";
        $output = shell_exec($command);

        if ($output && preg_match('/INTEGER:\s*(\d+)/', $output, $matches)) {
            $statusInt = (int)$matches[1];
            return match($statusInt) {
                1 => 'online',
                2 => 'los',
                3 => 'offline', // dying gasp
                4 => 'offline',
                default => 'offline'
            };
        }

        return 'offline';
    }

    /**
     * Reboot ONU via SSH Command.
     */
    public function rebootOnu(string $port, string $index): bool
    {
        try {
            $ssh = $this->connect();
            $commands = [
                "configure terminal",
                "pon-onu-mng gpon-onu_1/1/{$port}:{$index}",
                "reboot",
                "yes"
            ];
            $script = implode("\n", $commands) . "\n";
            Log::info("Mengeksekusi reboot untuk OLT Port {$port} Index {$index}");
            
            $ssh->exec($script);
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to reboot ONU Port {$port}:{$index} : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check Attenuation (Rx Power) via SSH directly.
     */
    public function checkAttenuation(string $port, string $index): ?string
    {
        try {
            $ssh = $this->connect();
            $command = "show pon power attenuation gpon-onu_1/1/{$port}:{$index}";
            Log::info("Cek Redaman: {$command}");
            
            $output = $ssh->exec($command);
            
            // Regex to match typical ZTE output: Rx : -23.45(dbm)
            if (preg_match('/Rx\s*:\s*(-?\d+(\.\d+)?)\(dbm\)/i', $output, $matches)) {
                return $matches[1]; // Returns "-23.45"
            }
            // Fallback for different firmware formats: rx power: -23.45
            if (preg_match('/rx power\s*:\s*(-?\d+(\.\d+)?)/i', $output, $matches)) {
                return $matches[1];
            }

            return null;
        } catch (\Exception $e) {
            Log::error("Failed to check attenuation for ONU Port {$port}:{$index} : " . $e->getMessage());
            return null;
        }
    }
}
