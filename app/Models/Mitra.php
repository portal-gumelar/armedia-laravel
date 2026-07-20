<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mitra extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'kode_mitra',
        'nama_mitra',
        'pemilik',
        'whatsapp',
        'email',
        'alamat',
        'wilayah',
        'persentase_komisi',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'persentase_komisi' => 'decimal:2',
        ];
    }

    // ── Relasi ────────────────────────────────────────────────────────────
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'mitra_user');
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function odps(): HasMany
    {
        return $this->hasMany(Odp::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }
}
