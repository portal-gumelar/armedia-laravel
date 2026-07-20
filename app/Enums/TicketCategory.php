<?php

namespace App\Enums;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum TicketCategory: string implements HasLabel, HasIcon
{
    case INTERNET_MATI  = 'internet_mati';
    case LAMBAT         = 'lambat';
    case WIFI_MASALAH   = 'wifi_masalah';
    case LAINNYA        = 'lainnya';

    public function getLabel(): string
    {
        return match($this) {
            self::INTERNET_MATI => 'Internet Mati / Putus Total',
            self::LAMBAT        => 'Internet Sangat Lambat',
            self::WIFI_MASALAH  => 'Wi-Fi Tidak Terdeteksi',
            self::LAINNYA       => 'Keluhan Lainnya',
        };
    }

    public function getIcon(): ?string
    {
        return match($this) {
            self::INTERNET_MATI => 'heroicon-o-signal-slash',
            self::LAMBAT        => 'heroicon-o-arrow-trending-down',
            self::WIFI_MASALAH  => 'heroicon-o-wifi',
            self::LAINNYA       => 'heroicon-o-chat-bubble-left-ellipsis',
        };
    }
}
