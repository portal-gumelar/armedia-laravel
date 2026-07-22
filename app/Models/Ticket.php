<?php

namespace App\Models;

use App\Enums\TicketCategory;
use App\Enums\TicketStatus;
use App\Enums\TicketPriority;
use App\Services\TelegramService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class Ticket extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'mitra_id',
        'ticket_no',
        'customer_id',
        'category',
        'priority',
        'description',
        'status',
        'technician_notes',
        'resolved_at',
        'user_id',
        'assigned_to',
    ];

    protected function casts(): array
    {
        return [
            'category'    => TicketCategory::class,
            'priority'    => TicketPriority::class,
            'status'      => TicketStatus::class,
            'resolved_at' => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll()->logOnlyDirty();
    }

    // ── Relasi ──────────────────────────────────────────────────────────────

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function mitra(): BelongsTo
    {
        return $this->belongsTo(Mitra::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // ── Auto-Numbering & Notifikasi ──────────────────────────────────────────

    protected static function boot(): void
    {
        parent::boot();

        // Auto-generate ticket_no dan auto-assign priority sebelum create
        static::creating(function (Ticket $ticket) {
            $prefix = 'TKT-' . now()->format('ymd') . '-';
            $todayCount = static::withTrashed()
                ->where('ticket_no', 'like', $prefix . '%')
                ->count() + 1;
            $ticket->ticket_no = $prefix . str_pad($todayCount, 4, '0', STR_PAD_LEFT);

            // Auto-assign priority based on category if not explicitly provided
            if (empty($ticket->priority)) {
                $categoryValue = $ticket->category instanceof TicketCategory 
                    ? $ticket->category->value 
                    : $ticket->category;
                    
                $ticket->priority = match ($categoryValue) {
                    'internet_mati' => TicketPriority::URGENT,
                    'lambat' => TicketPriority::HIGH,
                    'wifi_masalah' => TicketPriority::MEDIUM,
                    default => TicketPriority::LOW,
                };
            }
        });

        // Telegram notif saat tiket baru dibuat
        static::created(function (Ticket $ticket) {
            $customer = $ticket->customer;
            $category = $ticket->category instanceof TicketCategory
                ? $ticket->category->getLabel()
                : $ticket->category;

            TelegramService::sendMessage(
                "🛠️ <b>LAPORAN GANGGUAN INTERNET BARU</b>\n\n" .
                "<b>No. Tiket:</b> {$ticket->ticket_no}\n" .
                "<b>ID Pelanggan:</b> {$customer?->id_arm}\n" .
                "<b>Pelanggan:</b> {$customer?->name} ({$customer?->village?->name})\n" .
                "<b>No. WhatsApp:</b> {$customer?->whatsapp}\n" .
                "<b>Kategori:</b> {$category}\n" .
                "<b>Detail Keluhan:</b>\n{$ticket->description}\n\n" .
                "⚡ Segera jadwalkan teknisi untuk pengecekan lokasi!"
            );
        });

        // Telegram notif saat tiket diselesaikan
        static::updated(function (Ticket $ticket) {
            if ($ticket->isDirty('status') &&
                $ticket->status === TicketStatus::RESOLVED) {

                $customer = $ticket->customer;
                TelegramService::sendMessage(
                    "✅ <b>TIKET GANGGUAN SELESAI</b>\n\n" .
                    "<b>No. Tiket:</b> {$ticket->ticket_no}\n" .
                    "<b>Pelanggan:</b> {$customer?->name}\n" .
                    "<b>Catatan Teknisi:</b>\n" .
                    ($ticket->technician_notes ?? '-')
                );
            }
        });
    }
}
