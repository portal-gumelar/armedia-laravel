<?php

namespace App\Jobs;

use App\Models\Device;
use App\Services\Networking\TelnetClient;
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
    public $oltIp;
    public $oltUser;
    public $oltPass;

    /**
     * Create a new job instance.
     */
    public function __construct(Device $device, string $oltIp, string $oltUser, string $oltPass)
    {
        $this->device = $device;
        $this->oltIp = $oltIp;
        $this->oltUser = $oltUser;
        $this->oltPass = $oltPass;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("ProvisionOltDeviceJob: Started for Device ID {$this->device->id}");

        try {
            $telnet = new TelnetClient($this->oltIp, 23, 15);
            $telnet->connect();

            // Wait for Login prompt (adjust 'Username:' based on OLT brand)
            $telnet->waitPrompt('Username:');
            $telnet->writeCommand($this->oltUser);

            // Wait for Password prompt
            $telnet->waitPrompt('Password:');
            $telnet->writeCommand($this->oltPass);

            // Wait for terminal prompt (usually '>' or '#')
            $telnet->waitPrompt('>');

            // TODO: Add specific OLT provisioning commands here based on the brand (ZTE, Huawei, etc.)
            // Example:
            // $telnet->writeCommand('enable');
            // $telnet->waitPrompt('#');
            // $telnet->writeCommand('configure terminal');
            // ...

            $telnet->disconnect();

            // Update device status in DB if needed
            $this->device->update(['status' => 'active']);

            Log::info("ProvisionOltDeviceJob: Successfully provisioned Device ID {$this->device->id}");
        } catch (Exception $e) {
            Log::error("ProvisionOltDeviceJob: Failed -> " . $e->getMessage());
            
            // Mark device as failed provisioning
            $this->device->update(['status' => 'error_provisioning']);
            
            // Re-throw so Laravel marks the job as failed
            throw $e;
        }
    }
}
