<?php

namespace App\Models;

use App\Enums\CustomerSubscriptionStatus;
use App\Enums\MonitoringStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Customer extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    /**
     * Auto-generate id_arm saat pelanggan baru dibuat.
     * Format: ARM-XXXX (4 digit, auto-increment dari yang terbesar)
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Customer $customer) {
            if (empty($customer->id_arm)) {
                $last = static::withTrashed()->whereNotNull('id_arm')
                    ->where('id_arm', 'like', 'ARM-%')
                    ->orderByDesc('id_arm')
                    ->value('id_arm');

                $nextNumber = $last ? ((int) substr($last, 4)) + 1 : 1;
                $customer->id_arm = 'ARM-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
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
        'id_arm',
        'id_lama',
        'name',
        'whatsapp',
        'nik',
        'alamat',
        'kecamatan',
        'rw',
        'rt',
        'internet_package_id',
        'village_id',
        'odp_id',
        'device_id',
        'ip_address',
        'pon_olt',
        'cable_length_m',
        'activated_at',
        'subscription_status',
        'monitoring_status',
        'monitoring_checked_at',
        'photo_url',
        'maps_url',
        'drive_folder_url',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'subscription_status'  => CustomerSubscriptionStatus::class,
            'monitoring_status'    => MonitoringStatus::class,
            'activated_at'         => 'date',
            'monitoring_checked_at' => 'datetime',
        ];
    }

    // ── Relasi ──────────────────────────────────────────────────────────────

    public function internetPackage(): BelongsTo
    {
        return $this->belongsTo(InternetPackage::class);
    }

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    public function odp(): BelongsTo
    {
        return $this->belongsTo(Odp::class);
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function netwatchLogs(): HasMany
    {
        return $this->hasMany(NetwatchLog::class);
    }

    public function marketingFees(): HasMany
    {
        return $this->hasMany(MarketingFee::class);
    }
}
