<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Send a WhatsApp message using WAHA (WhatsApp HTTP API).
     *
     * @param string $phone
     * @param string $message
     * @param string|null $fileUrl (optional PDF URL)
     * @return bool
     */
    public static function sendMessage(string $phone, string $message, ?string $fileUrl = null): bool
    {
        $settings = app(\App\Settings\WhatsappSettings::class);
        $endpoint = $settings->waha_endpoint;
        $session  = $settings->waha_session;
        
        // Cek apakah WAHA diaktifkan
        if (!$settings->is_active || !$endpoint) {
            Log::warning('WhatsAppService: WAHA is not active or endpoint not set in settings.');
            return false;
        }

        // Format phone number to 62...
        $phone = self::formatPhone($phone);
        // Format chatId untuk WAHA (nomor@c.us)
        $chatId = $phone . '@c.us';

        try {
            if ($fileUrl) {
                // Endpoint untuk kirim file/dokumen
                $url = rtrim($endpoint, '/') . '/api/sendFile';
                $payload = [
                    'chatId'  => $chatId,
                    'file'    => ['url' => $fileUrl],
                    'caption' => $message,
                    'session' => $session,
                ];
            } else {
                // Endpoint untuk kirim teks biasa
                $url = rtrim($endpoint, '/') . '/api/sendText';
                $payload = [
                    'chatId'  => $chatId,
                    'text'    => $message,
                    'session' => $session,
                ];
            }

            // WAHA biasanya tidak memerlukan Bearer token, kecuali disetting khusus
            $response = Http::post($url, $payload);

            if ($response->successful()) {
                Log::info('WhatsApp message sent to ' . $phone, ['response' => $response->json()]);
                return true;
            }

            Log::error('WAHA API Error for ' . $phone, [
                'status' => $response->status(),
                'response' => $response->json(),
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('WhatsAppService Exception (WAHA): ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Format phone number to standard Indonesian format (62...)
     */
    public static function formatPhone(string $phone): string
    {
        // Remove spaces, dashes, etc
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($phone, '08')) {
            $phone = '628' . substr($phone, 2);
        }

        return $phone;
    }
}
