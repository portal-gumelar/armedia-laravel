<?php

namespace App\Jobs;

use App\Models\MikrotikServer;
use App\Services\Networking\MikrotikSshService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class MikrotikSecurityScannerJob implements ShouldQueue
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
        Log::info("MikrotikSecurityScannerJob: Memulai scanning brute force...");

        $servers = MikrotikServer::where('is_active', true)->get();
        $threshold = 5; // Blacklist jika gagal login 5 kali atau lebih

        foreach ($servers as $server) {
            try {
                $ssh = new MikrotikSshService($server->host, $server->username, $server->password, $server->port ?? 22022);
                $ssh->connect();
                
                $failures = $ssh->getLoginFailures();
                
                foreach ($failures as $ip => $count) {
                    if ($count >= $threshold) {
                        Log::warning("MikrotikSecurityScannerJob: IP {$ip} terdeteksi brute force ({$count}x) pada server {$server->name}. Memasukkan ke blacklist.");
                        $ssh->blacklistIp($ip);
                        
                        // Kirim notifikasi Telegram via Event
                        event(new \App\Events\DeviceStatusChanged('SECURITY_ALERT', $server->name, 'SYSTEM', "BRUTE FORCE DETECTED ($count failures). IP $ip BLACKLISTED", $ip));
                    }
                }

                $ssh->disconnect();

            } catch (\Exception $e) {
                Log::error("MikrotikSecurityScannerJob gagal untuk server {$server->name}: " . $e->getMessage());
            }
        }

        Log::info("MikrotikSecurityScannerJob: Scanning selesai.");
    }
}
