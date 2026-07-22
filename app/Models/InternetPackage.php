<?php

namespace App\Models;

use App\Enums\PackageBrand;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InternetPackage extends Model
{
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty();
    }

    protected $fillable = [
        // Kolom lama
        'nama_paket',
        'kecepatan',
        'harga',
        'keterangan_promo',
        'is_active',
        // Kolom baru ISP
        'code',
        'brand',
        'speed_mbps',
        'ip_allocation',
        'kode',
        'alokasi_ip',
    ];

    protected function casts(): array
    {
        return [
            'brand'     => PackageBrand::class,
            'is_active' => 'boolean',
        ];
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function prorataRates(): HasMany
    {
        return $this->hasMany(ProrataRate::class, 'product_id');
    }
}
