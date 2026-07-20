<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum PipelineStatus: string implements HasLabel, HasColor
{
    case BELUM      = 'belum';
    case SURVEY     = 'survey';
    case TERJADWAL  = 'terjadwal';
    case TERPASANG  = 'terpasang';
    case BATAL      = 'batal';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::BELUM     => 'Belum Diproses',
            self::SURVEY    => 'Survei',
            self::TERJADWAL => 'Terjadwal',
            self::TERPASANG => 'Terpasang',
            self::BATAL     => 'Batal',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::BELUM     => 'gray',
            self::SURVEY    => 'info',
            self::TERJADWAL => 'warning',
            self::TERPASANG => 'success',
            self::BATAL     => 'danger',
        };
    }
}
