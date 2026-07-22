<?php

namespace App\Jobs;

use App\Models\MikrotikServer;
use App\Models\Customer;
use App\Services\Networking\MikrotikSshService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class FetchMikrotikNetwatchJob implements ShouldQueue
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
        Log::info("FetchMikrotikNetwatchJob: Memulai penarikan Netwatch...");

        $servers = MikrotikServer::where('is_active', true)->get();

        foreach ($servers as $server) {
            try {
                $ssh = new MikrotikSshService($server->host, $server->username, $server->password, $server->port ?? 22022);
                $ssh->connect();
                $statuses = $ssh->getNetwatchStatus();
                $ssh->disconnect();

                $now = now();
                $insertData = [];
                
                // Map IP to Customer ID and fetch previous status for comparison
                $ips = collect($statuses)->pluck('host')->toArray();
                $customers = Customer::whereIn('ip_address', $ips)->get()->keyBy('ip_address');
                
                // Get the latest status for each IP
                $latestLogs = DB::table('netwatch_logs')
                    ->whereIn('ip_address', $ips)
                    ->orderBy('checked_at', 'desc')
                    ->get()
                    ->unique('ip_address')
                    ->keyBy('ip_address');

                foreach ($statuses as $stat) {
                    $ip = $stat['host'];
                    $status = $stat['status'];
                    $customer = $customers[$ip] ?? null;
                    $prevLog = $latestLogs[$ip] ?? null;

                    $insertData[] = [
                        'customer_id' => $customer?->id,
                        'ip_address' => $ip,
                        'status' => $status,
                        'checked_at' => $now,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                    
                    // Dispatch event if status changed
                    $prevStatus = $prevLog ? $prevLog->status : null;
                    if ($prevStatus !== null && $prevStatus !== $status) {
                        event(new \App\Events\DeviceStatusChanged('MikroTik_Netwatch', $server->name, $customer?->name ?? 'Unknown', $status, $ip));
                    }
                }

                if (!empty($insertData)) {
                    DB::table('netwatch_logs')->insert($insertData);
                }

            } catch (\Exception $e) {
                Log::warning("FetchMikrotikNetwatchJob gagal untuk Mikrotik {$server->name}: " . $e->getMessage());
            }
        }
        
        Log::info("FetchMikrotikNetwatchJob: Selesai.");
    }
}
