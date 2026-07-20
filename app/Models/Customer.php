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

use Illuminate\Foundation\Auth\User as Authenticatable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class Customer extends Authenticatable implements FilamentUser
{
    use HasFactory, LogsActivity, SoftDeletes;

    public function canAccessPanel(Panel $panel): bool
    {
        return $panel->getId() === 'member';
    }

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
        'mitra_id',
        'id_arm',
        'id_lama',
        'name',
        'whatsapp',
        'password',
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
        'is_active',
        'kec',
        'desa',
        'kota_kab',
        'paket_mbps',
        'harga',
        'perangkat_kode',
        'sn',
        'sn_lama',
        'port_olt',
        'index_olt',
        'profile',
        'vlan_olt',
        'redaman_dbm',
        'link_foto',
        'link_maps',
        'wa_konfirmasi_rtrw',
        'wa_umum',
        'wa_invoice_tagihan',
        'wa_foto_lokasi',
        'wa_ingatkan_h3',
        'wa_jatuh_tempo_hari_ini',
        'wa_lewat_tempo',
        'wa_invoice_pelanggan_baru',
        'ssid',
        'password_wifi',
        'vlan',
        'tipe_onu',
        'jatuh_tempo_bulan_ini',
        'tagihan_bln1_prorata',
        'teknisi_pasang',
        'mikrotik_server_id',
        'pppoe_username',
        'pppoe_password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'subscription_status'  => CustomerSubscriptionStatus::class,
            'monitoring_status'    => MonitoringStatus::class,
            'activated_at'         => 'date',
            'monitoring_checked_at' => 'datetime',
            'password'             => 'hashed',
        ];
    }

    // Gunakan 'whatsapp' sebagai field login (bukan 'email')
    public function getAuthIdentifierName(): string
    {
        return 'whatsapp';
    }

    // ── Relasi ──────────────────────────────────────────────────────────────

    public function mitra(): BelongsTo
    {
        return $this->belongsTo(Mitra::class);
    }

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

    public function mikrotikServer(): BelongsTo
    {
        return $this->belongsTo(MikrotikServer::class);
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
