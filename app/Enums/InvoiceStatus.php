<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum InvoiceStatus: string implements HasLabel, HasColor
{
    case BELUM            = 'belum';
    case LUNAS            = 'lunas';
    case GRATIS           = 'gratis';
    case TIDAK_TERTAGIH   = 'tidak_tertagih';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::BELUM          => 'Belum Bayar',
            self::LUNAS          => 'Lunas',
            self::GRATIS         => 'Gratis',
            self::TIDAK_TERTAGIH => 'Tidak Tertagih',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::BELUM          => 'warning',
            self::LUNAS          => 'success',
            self::GRATIS         => 'info',
            self::TIDAK_TERTAGIH => 'gray',
        };
    }
}
