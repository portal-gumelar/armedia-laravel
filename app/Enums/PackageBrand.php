<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum PackageBrand: string implements HasLabel, HasColor
{
    case ARMED  = 'ARMED';
    case HEROIK = 'HEROIK';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ARMED  => 'ARMED',
            self::HEROIK => 'HEROIK',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::ARMED  => 'danger',
            self::HEROIK => 'primary',
        };
    }
}
