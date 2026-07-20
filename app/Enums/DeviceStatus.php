<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum DeviceStatus: string implements HasLabel, HasColor
{
    case TERPASANG = 'terpasang';
    case STOK      = 'stok';
    case RUSAK     = 'rusak';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::TERPASANG => 'Terpasang',
            self::STOK      => 'Stok',
            self::RUSAK     => 'Rusak',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::TERPASANG => 'success',
            self::STOK      => 'info',
            self::RUSAK     => 'danger',
        };
    }
}
