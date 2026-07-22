<?php

namespace App\Jobs;

use App\Models\OnuDevice;
use App\Services\ZteOltService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class FetchOltMetricsJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("FetchOltMetricsJob: Memulai penarikan metrik SNMP...");
        
        $onus = OnuDevice::with('port.server')->whereHas('port.server', function($q) {
            $q->where('is_active', true)->whereNotNull('snmp_community');
        })->get();

        foreach ($onus as $onu) {
            try {
                $server = $onu->port->server;
                $oltService = new ZteOltService($server);
                
                // Format ONU Index bergantung pada slot, port, onu_id
                // Misal: gpon-onu_1/1/2:1 biasanya index SNMP dikalkulasi, tapi kita pakai index dummy/onu_id sementara.
                $index = "{$onu->port->slot}.{$onu->port->port}.{$onu->onu_id}"; 

                $rxPower = $oltService->getOnuRxPower($index);
                $status = $oltService->getOnuStatus($index);

                $updateData = [
                    'rx_power' => $rxPower,
                    'status' => $status,
                ];

                if ($status === 'online' && $onu->status !== 'online') {
                    $updateData['last_online_at'] = now();
                    event(new \App\Events\DeviceStatusChanged('OLT_ONU', $server->name, $onu->customer?->name ?? 'Unknown', $status, $onu->sn));
                } elseif ($status !== 'online' && $onu->status === 'online') {
                    $updateData['last_offline_at'] = now();
                    event(new \App\Events\DeviceStatusChanged('OLT_ONU', $server->name, $onu->customer?->name ?? 'Unknown', $status, $onu->sn));
                }

                $onu->update($updateData);

            } catch (\Exception $e) {
                Log::warning("FetchOltMetricsJob gagal untuk ONU SN {$onu->sn}: " . $e->getMessage());
            }
        }
        
        Log::info("FetchOltMetricsJob: Selesai menarik metrik untuk {$onus->count()} ONU.");
    }
}
