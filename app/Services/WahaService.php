<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WahaService
{
    protected string $url;
    protected string $session;
    
    public function __construct()
    {
        // Default internal url in coolify if missing
        $this->url = env('WAHA_URL', 'http://waha:3000');
        $this->session = env('WAHA_SESSION', 'default');
    }

    /**
     * Send Text Message
     * @param string $phone The phone number in international format, e.g., 628123456789
     * @param string $message The message text
     */
    public function sendMessage(string $phone, string $message): bool
    {
        try {
            // WAHA format requires @c.us at the end of the phone number
            $chatId = $this->formatPhone($phone) . '@c.us';

            $response = Http::post("{$this->url}/api/sendText", [
                'session' => $this->session,
                'chatId' => $chatId,
                'text' => $message,
            ]);

            if ($response->successful()) {
                return true;
            }

            Log::error('WAHA sendMessage Failed', [
                'phone' => $phone,
                'response' => $response->body()
            ]);
            
            return false;
        } catch (\Exception $e) {
            Log::error('WAHA sendMessage Exception', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Send PDF Document
     * @param string $phone The phone number
     * @param string $fileUrl The publicly accessible URL of the file
     * @param string $fileName The name of the file to send
     * @param string $caption Optional caption
     */
    public function sendDocument(string $phone, string $fileUrl, string $fileName, string $caption = ''): bool
    {
        try {
            $chatId = $this->formatPhone($phone) . '@c.us';

            $response = Http::post("{$this->url}/api/sendFile", [
                'session' => $this->session,
                'chatId' => $chatId,
                'file' => [
                    'url' => $fileUrl,
                ],
                'filename' => $fileName,
                'caption' => $caption,
            ]);

            if ($response->successful()) {
                return true;
            }

            Log::error('WAHA sendDocument Failed', [
                'phone' => $phone,
                'response' => $response->body()
            ]);
            
            return false;
        } catch (\Exception $e) {
            Log::error('WAHA sendDocument Exception', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Helper to format Indonesian phone numbers to standard format (62...)
     */
    protected function formatPhone(string $phone): string
    {
        // Remove any non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // If it starts with 0, change it to 62
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        return $phone;
    }
}
