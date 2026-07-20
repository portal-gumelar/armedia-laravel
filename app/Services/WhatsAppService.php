<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Invoice;

class WhatsAppService
{
    protected string $token;
    protected string $endpoint;

    public function __construct()
    {
        // Akan diambil dari settings/database, sementara pakai env
        $this->token = env('FONNTE_TOKEN', '');
        $this->endpoint = 'https://api.fonnte.com/send';
    }

    /**
     * Send a plain text message.
     */
    public function sendMessage(string $target, string $message): bool
    {
        if (empty($this->token)) {
            Log::warning("Fonnte token is not set. Cannot send WhatsApp message to $target.");
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->token
            ])->post($this->endpoint, [
                'target' => $target,
                'message' => $message,
                'countryCode' => '62',
            ]);

            if ($response->successful() && isset($response['status']) && $response['status'] == true) {
                return true;
            }

            Log::error("Failed to send WhatsApp message to $target: " . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error("WhatsAppService Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send Invoice Notification (Lunas/Belum Lunas)
     */
    public function sendInvoiceNotification(Invoice $invoice): bool
    {
        $customer = $invoice->customer;
        if (!$customer || empty($customer->whatsapp)) {
            return false;
        }

        $status = $invoice->status;
        $totalFormatted = "Rp " . number_format($invoice->total_amount, 0, ',', '.');
        $period = \Carbon\Carbon::parse($invoice->due_date)->translatedFormat('F Y');

        if ($status->value === 'lunas') {
            $message = "Halo *{$customer->name}*,\n\n";
            $message .= "Terima kasih, pembayaran tagihan Internet Anda untuk periode *{$period}* sebesar *{$totalFormatted}* telah kami terima.\n\n";
            $message .= "Status: *LUNAS*\n";
            $message .= "Layanan internet Anda sudah aktif kembali. Terima kasih telah menggunakan layanan Armedia.\n\n";
            $message .= "_Pesan ini dikirim otomatis oleh sistem Armedia._";
        } else {
            $message = "Halo *{$customer->name}*,\n\n";
            $message .= "Ini adalah informasi tagihan Internet Anda untuk periode *{$period}*.\n\n";
            $message .= "Total Tagihan: *{$totalFormatted}*\n";
            $message .= "Jatuh Tempo: *" . \Carbon\Carbon::parse($invoice->due_date)->translatedFormat('d F Y') . "*\n\n";
            $message .= "Mohon lakukan pembayaran sebelum jatuh tempo untuk menghindari isolir otomatis.\n\n";
            $message .= "_Pesan ini dikirim otomatis oleh sistem Armedia._";
        }

        return $this->sendMessage($customer->whatsapp, $message);
    }
}
