<?php

namespace App\Jobs;

use App\Models\OnuDevice;
use App\Services\ZteOltService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProvisionOnuToOltJob implements ShouldQueue
{
    use Queueable;

    public $tries = 3; // Retry up to 3 times
    public $backoff = [10, 30, 60]; // Exponential backoff in seconds

    protected $onuDeviceId;
    protected $profile;

    /**
     * Create a new job instance.
     */
    public function __construct($onuDeviceId, $profile = 'default')
    {
        $this->onuDeviceId = $onuDeviceId;
        $this->profile = $profile;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $onu = OnuDevice::with('port.server')->find($this->onuDeviceId);
        if (!$onu) {
            Log::error("ProvisionOnuToOltJob: OnuDevice ID {$this->onuDeviceId} not found.");
            return;
        }

        $server = $onu->port->server;
        
        $oltService = new ZteOltService($server);
        $result = $oltService->registerOnu($onu->port->slot, $onu->port->port, $onu->sn, $this->profile);

        if ($result['success']) {
            $onu->update([
                'onu_id' => $result['onu_id'],
                'status' => 'online', // Or whatever initial status makes sense
            ]);
            Log::info("ProvisionOnuToOltJob: Success for ONU SN {$onu->sn}. assigned ID: {$result['onu_id']}");
        } else {
            Log::error("ProvisionOnuToOltJob: Failed to register ONU SN {$onu->sn}. Reason: {$result['message']}");
            // Throwing exception to trigger a retry
            throw new \Exception("Failed to register ONU: " . $result['message']);
        }
    }
}
