<?php

namespace App\Console\Commands;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendBillingReminders extends Command
{
    protected $signature   = 'isp:send-reminders {--force : Paksa kirim tanpa cek tanggal}';
    protected $description = 'Kirim WA tagihan bulanan ke pelanggan (format ISP profesional)';

    public function handle(): void
    {
        $this->info('🚀 Mulai pengiriman WA Tagihan...');

        $today      = now();
        $targetDay  = (int) env('BILLING_REMINDER_DAY', 7); // Default kirim tiap tgl 7 (H-3 dari tgl 10)
        $forced     = $this->option('force');

        if (!$forced && (int)$today->format('d') !== $targetDay) {
            $this->info("Hari ini tgl {$today->format('d')}, bukan hari pengiriman tagihan (tgl {$targetDay}). Lewati.");
            return;
        }

        // Cari semua invoice bulan ini yang statusnya BELUM LUNAS
        $invoices = Invoice::with(['customer.village', 'customer.internetPackage'])
            ->where('status', InvoiceStatus::BELUM->value)
            ->whereYear('period', $today->year)
            ->whereMonth('period', $today->month)
            ->get();

        $this->info("Ditemukan {$invoices->count()} tagihan yang perlu dikirim...");

        $sent   = 0;
        $failed = 0;

        foreach ($invoices as $invoice) {
            $customer = $invoice->customer;
            if (!$customer || !$customer->whatsapp) {
                $this->warn("Invoice #{$invoice->id}: No. WA tidak ada, dilewati.");
                continue;
            }

            $message = $this->buildWhatsAppMessage($invoice);
            $success = $this->sendViaWebhook($invoice, $message);

            if ($success) {
                $sent++;
                $this->info("✅ Terkirim ke {$customer->name} ({$customer->whatsapp})");
            } else {
                $failed++;
                $this->error("❌ Gagal kirim ke {$customer->name}");
            }
        }

        $this->info("Selesai. ✅ Terkirim: {$sent} | ❌ Gagal: {$failed}");
    }

    /**
     * Buat teks WA tagihan bergaya ISP profesional.
     */
    private function buildWhatsAppMessage(Invoice $invoice): string
    {
        $customer   = $invoice->customer;
        $village    = $customer->village?->name ?? '-';
        $paket      = $customer->internetPackage?->nama_paket ?? '-';
        $idArm      = $customer->id_arm ?? '-';
        $invoiceNo  = $invoice->invoice_no ?? '-';
        $amount     = 'Rp ' . number_format($invoice->amount, 0, ',', '.');
        $dueDate    = $invoice->due_date
            ? Carbon::parse($invoice->due_date)->translatedFormat('d-M-Y')
            : '10-' . Carbon::parse($invoice->period)->translatedFormat('M-Y');

        // Link bayar Midtrans (jika tersedia) & link portal pelanggan
        $payUrl     = url("/pelanggan/bayar/{$invoice->id}");
        $portalUrl  = url("/pelanggan?id={$idArm}");
        $csWa       = env('ARMEDIA_CS_WA', '628xxxxxxxxxx');
        $csPhone    = env('ARMEDIA_CS_PHONE', '0812-XXXX-XXXX');

        return <<<MSG
Yth. Bapak/Ibu *{$customer->name} ({$village})* 👋✨

Invoice Tagihan internet Anda sudah siap! Yuk cek detail lengkapnya:

🆔 *ID Pelanggan*: {$idArm}
📄 *Nomor Invoice*: {$invoiceNo}
📶 *Layanan*: {$paket}
💳 *Harga Paket*: {$amount}
🗓 *Periode*: 1 Bulan
⏰ *Jatuh Tempo*: {$dueDate}

💵 *Total Tagihan*: {$amount}

🔗 Bayar lebih cepat dan mudah:
👉 {$payUrl}

Atau login ke Portal Pelanggan Anda:
🌐 {$portalUrl}

- Pembayaran Tunai bisa ke Kantor PT. Akses Artha Media
- Keterlambatan pembayaran: internet diputus sementara, tagihan tetap berjalan

Terima kasih 🙏 — by Billing
*CS/Admin ARMEDIA*
WA: {$csWa} (Hanya CHAT) 📵
Panggilan: {$csPhone} 📞
MSG;
    }

    /**
     * Kirim pesan via n8n / Typebot Webhook.
     */
    private function sendViaWebhook(Invoice $invoice, string $message): bool
    {
        $webhookUrl = env('TYPEBOT_REMINDER_WEBHOOK_URL');

        if (!$webhookUrl) {
            Log::warning("TYPEBOT_REMINDER_WEBHOOK_URL belum diset di .env");
            return false;
        }

        try {
            $response = Http::timeout(15)->post($webhookUrl, [
                'phone'       => $invoice->customer->whatsapp,
                'message'     => $message,
                // Data tambahan untuk n8n/Typebot bisa proses lebih lanjut
                'invoice_id'  => $invoice->id,
                'invoice_no'  => $invoice->invoice_no,
                'customer_id' => $invoice->customer->id,
                'amount'      => $invoice->amount,
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error("Gagal kirim WA tagihan #{$invoice->id}: " . $e->getMessage());
            return false;
        }
    }
}
