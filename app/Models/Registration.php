<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Registration extends Model
{
    /** @use HasFactory<\Database\Factories\RegistrationFactory> */
    use HasFactory, LogsActivity;

    protected $fillable = [
        // Kolom lama
        'paket',
        'langganan_sebelumnya',
        'nama',
        'whatsapp',
        'kecamatan',
        'desa',
        'alamat',
        'tanggal_pemasangan',
        'waktu_survei',
        'status',
        'nik',
        'rw',
        'rt',
        'provider_saat_ini',
        'sumber_info',
        'link_google_maps',
        'foto_ktp',
        'catatan',
        'tanggal_aktif',
        // Kolom PSB baru
        'report_no',
        'jadwal_pasang',
        'marketing',
        'target_odp_id',
        'pipeline_status',
        'converted_customer_id',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty();
    }

    public function internetPackage()
    {
        return $this->belongsTo(InternetPackage::class);
    }

    public function convertedCustomer()
    {
        return $this->belongsTo(Customer::class, 'converted_customer_id');
    }

    public function targetOdp()
    {
        return $this->belongsTo(Odp::class, 'target_odp_id');
    }

    protected static function booted()
    {
        static::created(function ($registration) {
            $packageName = $registration->paket ?: 'Tidak diketahui';
            \App\Services\TelegramService::sendMessage(
                "🚀 <b>PENDAFTARAN INTERNET BARU</b>\n\n" .
                "<b>Nama:</b> {$registration->nama}\n" .
                "<b>No. Telp:</b> {$registration->whatsapp}\n" .
                "<b>Paket:</b> {$packageName}\n" .
                "<b>Alamat:</b> {$registration->alamat}\n\n" .
                "Segera hubungi pendaftar untuk proses survei!"
            );
        });
    }
}
