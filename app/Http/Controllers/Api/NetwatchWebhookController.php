<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\NetwatchLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NetwatchWebhookController extends Controller
{
    /**
     * Menerima PUSH notifikasi (webhook) dari RouterOS Netwatch
     * Payload expected (POST / GET):
     * - ip: IP Address pelanggan
     * - status: "up" atau "down"
     * - secret: Token rahasia dari config
     */
    public function handle(Request $request)
    {
        $secret = $request->input('secret');
        $validSecret = config('isp.mikrotik.webhook_secret');

        if (empty($secret) || $secret !== $validSecret) {
            Log::warning('Netwatch Webhook: Invalid secret token', ['ip' => $request->ip()]);
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $ipAddress = trim($request->input('ip'));
        $status    = strtolower(trim($request->input('status')));

        if (empty($ipAddress) || !in_array($status, ['up', 'down'])) {
            return response()->json(['error' => 'Invalid payload. Required: ip, status (up|down)'], 400);
        }

        // Cari pelanggan dengan IP ini
        $customer = Customer::where('ip_address', $ipAddress)->first();

        // Rekam ke logs (tetap rekam meskipun pelanggan belum ter-map)
        NetwatchLog::create([
            'customer_id' => $customer?->id,
            'ip_address'  => $ipAddress,
            'status'      => $status,
            'checked_at'  => now(),
        ]);

        if ($customer) {
            // Update status pelanggan terakhir
            $customer->update([
                'monitoring_status'     => $status,
                'monitoring_checked_at' => now(),
            ]);

            Log::info("Netwatch Webhook: Updated {$customer->name} ({$ipAddress}) to {$status}");
            
            return response()->json([
                'success' => true,
                'message' => "Customer {$customer->name} status updated to {$status}",
            ]);
        }

        Log::info("Netwatch Webhook: Logged IP {$ipAddress} to {$status} (Unmapped Customer)");

        return response()->json([
            'success' => true,
            'message' => "IP {$ipAddress} status logged as {$status}. (No customer mapped)",
        ]);
    }
}
