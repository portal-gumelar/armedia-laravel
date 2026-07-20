<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum MonitoringStatus: string implements HasLabel, HasColor
{
    case UP      = 'up';
    case DOWN    = 'down';
    case UNKNOWN = 'unknown';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::UP      => 'Online',
            self::DOWN    => 'Offline',
            self::UNKNOWN => 'Belum Dicek',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::UP      => 'success',
            self::DOWN    => 'danger',
            self::UNKNOWN => 'gray',
        };
    }
}
