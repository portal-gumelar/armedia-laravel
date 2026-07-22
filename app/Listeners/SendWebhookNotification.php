<?php

namespace App\Listeners;

use App\Events\DeviceStatusChanged;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendWebhookNotification implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(DeviceStatusChanged $event): void
    {
        $webhookUrl = config('services.telegram.webhook_url');
        
        if (!$webhookUrl) {
            return;
        }

        $emoji = $event->status === 'online' || $event->status === 'up' ? '✅' : '❌';
        $statusText = strtoupper($event->status);

        $message = "{$emoji} *ALERT: {$event->deviceType} STATUS CHANGED*\n\n"
                 . "Device: {$event->deviceName}\n"
                 . "Target: {$event->ipOrSn}\n"
                 . "Customer: {$event->customerName}\n"
                 . "Status: *{$statusText}*\n"
                 . "Time: " . now()->format('Y-m-d H:i:s');

        try {
            Http::post($webhookUrl, [
                'text' => $message,
                'parse_mode' => 'Markdown'
            ]);
        } catch (\Exception $e) {
            Log::error("Gagal mengirim webhook Telegram: " . $e->getMessage());
        }
    }
}
