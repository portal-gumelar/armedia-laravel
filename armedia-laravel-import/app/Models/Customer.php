<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'id_baru', 'id_lama', 'nama', 'nik_ktp', 'no_hp', 'kec', 'desa', 'rw', 'rt', 'kota_kab',
        'product_id', 'paket_mbps', 'harga', 'ip', 'perangkat_kode', 'sn', 'sn_lama', 'odp_id',
        'tgl_aktif', 'panjang_kabel', 'pon_olt', 'port_olt', 'index_olt', 'profile', 'vlan_olt',
        'redaman_dbm', 'keterangan', 'status', 'link_foto', 'link_maps',
        'wa_konfirmasi_rtrw', 'wa_umum', 'wa_invoice_tagihan', 'wa_foto_lokasi',
        'wa_ingatkan_h3', 'wa_jatuh_tempo_hari_ini', 'wa_lewat_tempo', 'wa_invoice_pelanggan_baru',
        'ssid', 'password_wifi', 'vlan', 'tipe_onu', 'jatuh_tempo_bulan_ini',
        'tagihan_bln1_prorata', 'teknisi_pasang',
    ];

    protected $casts = [
        'tgl_aktif' => 'date',
        'jatuh_tempo_bulan_ini' => 'date',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function odp(): BelongsTo
    {
        return $this->belongsTo(Odp::class);
    }

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }

    public function ontInventories(): HasMany
    {
        return $this->hasMany(OntInventory::class);
    }

    public function netwatchMonitorings(): HasMany
    {
        return $this->hasMany(NetwatchMonitoring::class);
    }

    public function monthlyBills(): HasMany
    {
        return $this->hasMany(MonthlyBill::class);
    }
}
