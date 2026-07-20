<?php

namespace App\Jobs;

use App\Models\Device;
use App\Services\Networking\TelnetClient;
use App\Services\Networking\MikrotikSshService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProvisionOltDeviceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $device;
    public array $data;

    /**
     * Create a new job instance.
     */
    public function __construct(Device $device, array $data)
    {
        $this->device = $device;
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("ProvisionOltDeviceJob: Started for Device ID {$this->device->id}");

        try {
            $this->provisionZteOlt();
            $this->provisionMikrotikNetwatch();
            
            // Update device status in DB if needed
            $this->device->update(['status' => 'dipasang']);

            Log::info("ProvisionOltDeviceJob: Successfully provisioned Device ID {$this->device->id}");
        } catch (Exception $e) {
            Log::error("ProvisionOltDeviceJob: Failed -> " . $e->getMessage());
            $this->device->update(['status' => 'rusak']);
            throw $e;
        }
    }

    protected function provisionZteOlt(): void
    {
        $d = $this->data;
        $telnet = new TelnetClient($d['olt_ip'], 23, 20); // 20s timeout
        $telnet->connect();

        // 1. Login
        $telnet->waitPrompt('Username:');
        $telnet->writeCommand($d['olt_user']);
        $telnet->waitPrompt('Password:');
        $telnet->writeCommand($d['olt_pass']);
        $telnet->waitPrompt('>'); // Default user prompt

        // 2. Enable & Config Terminal
        $telnet->writeCommand('enable');
        $telnet->waitPrompt('#');
        $telnet->writeCommand('configure terminal');
        $telnet->waitPrompt('(config)#');

        // 3. Masuk ke Interface OLT
        $telnet->writeCommand("interface gpon-olt_1/1/{$d['port']}");
        $telnet->waitPrompt("(config-if)#");

        // 4. Registrasi ONU (Jika replace, hapus dulu)
        if ($d['is_replace']) {
            $telnet->writeCommand("no onu {$d['index']}");
            // Wait a bit or wait for prompt
            $telnet->waitPrompt("(config-if)#");
        }
        
        $telnet->writeCommand("onu {$d['index']} type all sn {$d['sn']}");
        $telnet->waitPrompt("(config-if)#");
        $telnet->writeCommand("exit");
        $telnet->waitPrompt("(config)#");

        // 5. Konfigurasi ONU Interface
        $telnet->writeCommand("interface gpon-onu_1/1/{$d['port']}:{$d['index']}");
        $telnet->waitPrompt("(config-if)#");

        $telnet->writeCommand("name {$d['nama']}");
        $telnet->waitPrompt("(config-if)#");

        $desc = "{$d['ssid']}-{$d['ip_address']}-{$d['hp']}";
        $telnet->writeCommand("description {$desc}");
        $telnet->waitPrompt("(config-if)#");

        $telnet->writeCommand("sn-bind enable sn");
        $telnet->waitPrompt("(config-if)#");

        $telnet->writeCommand("tcont 1 profile {$d['profile']}");
        $telnet->waitPrompt("(config-if)#");

        $telnet->writeCommand("gemport 1 tcont 1");
        $telnet->waitPrompt("(config-if)#");

        $telnet->writeCommand("service-port 1 vport 1 user-vlan {$d['vlan']} vlan {$d['vlan']}");
        $telnet->waitPrompt("(config-if)#");

        $telnet->writeCommand("exit");
        $telnet->waitPrompt("(config)#");

        // 6. Flow Management (pon-onu-mng)
        $telnet->writeCommand("pon-onu-mng gpon-onu_1/1/{$d['port']}:{$d['index']}");
        $telnet->waitPrompt("(gpon-onu-mng)#");

        $telnet->writeCommand("flow mode 1 tag-filter vlan-filter untag-filter discard");
        $telnet->waitPrompt("(gpon-onu-mng)#");

        $telnet->writeCommand("flow 1 pri 0 vlan {$d['vlan']}");
        $telnet->waitPrompt("(gpon-onu-mng)#");

        $telnet->writeCommand("gemport 1 flow 1");
        $telnet->waitPrompt("(gpon-onu-mng)#");

        $telnet->writeCommand("exit");
        $telnet->waitPrompt("(config)#");

        // 7. Simpan konfigurasi
        $telnet->writeCommand("end");
        $telnet->waitPrompt("#");
        $telnet->writeCommand("write");
        // 'write' takes some time, wait for completion or timeout
        // Usually returns '#' after saving
        $telnet->waitPrompt("#");

        $telnet->disconnect();
    }

    protected function provisionMikrotikNetwatch(): void
    {
        $d = $this->data;
        $ssh = new MikrotikSshService($d['mikrotik_ip'], $d['mikrotik_user'], $d['mikrotik_pass'], $d['mikrotik_port']);
        $ssh->connect();

        $comment = "{$d['nama']} - {$d['sn']} - {$d['hp']} - {$d['ssid']} - {$d['rt_rw']} - {$d['desa']} - {$d['teknisi']}";
        
        if ($d['is_replace']) {
            // Update existing entry
            $cmd = "/tool netwatch set [find host=\"{$d['ip_address']}\"] comment=\"{$comment}\"";
        } else {
            // Add new entry
            $cmd = "/tool netwatch add host={$d['ip_address']} interval=3m comment=\"{$comment}\"";
        }
        
        $ssh->exec($cmd);
        $ssh->disconnect();
    }
}
