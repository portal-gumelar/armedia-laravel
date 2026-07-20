<?php

namespace App\Models;

use App\Enums\InvoiceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    /**
     * Auto-generate invoice_no dan due_date saat invoice baru dibuat.
     * Format invoice_no: {id_arm}-{YYMMDD}.{HHMMSS}.{id}
     * Contoh: ARM-0001-260711.070000.1370
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Invoice $invoice) {
            // Set due_date: tanggal 10 bulan yang sama dengan periode
            if (empty($invoice->due_date) && $invoice->period) {
                $invoice->due_date = \Carbon\Carbon::parse($invoice->period)
                    ->setDay(10)
                    ->toDateString();
            }
        });

        static::created(function (Invoice $invoice) {
            // Generate invoice_no setelah ID tersedia
            if (empty($invoice->invoice_no)) {
                $customer = $invoice->customer;
                $idArm    = $customer?->id_arm ?? 'ARM-0000';
                $dateStr  = now()->format('ymd');
                $timeStr  = now()->format('His');
                $invoiceNo = "{$idArm}-{$dateStr}.{$timeStr}.{$invoice->id}";
                $invoice->updateQuietly(['invoice_no' => $invoiceNo]);
            }
        });

        static::updated(function (Invoice $invoice) {
            // Jika tagihan berubah jadi lunas
            if ($invoice->isDirty('status') && $invoice->status === InvoiceStatus::LUNAS) {
                // Un-isolir Mikrotik
                if ($invoice->customer) {
                    \App\Jobs\ProcessMikrotikIsolation::dispatch($invoice->customer, 'unisolate');
                }
                
                // Kirim WA Kuitansi
                try {
                    $waService = app(\App\Services\WhatsAppService::class);
                    $waService->sendInvoiceNotification($invoice);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Gagal kirim WA kuitansi otomatis: " . $e->getMessage());
                }
            }
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty();
    }

    protected $fillable = [
        'mitra_id',
        'customer_id',
        'invoice_no',
        'period',
        'due_date',
        'amount',
        'status',
        'paid_at',
        'payment_method',
        'payment_token',
        'payment_url',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status'  => InvoiceStatus::class,
            'period'  => 'date',
            'paid_at' => 'date',
            'due_date' => 'date',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function mitra(): BelongsTo
    {
        return $this->belongsTo(Mitra::class);
    }
}
