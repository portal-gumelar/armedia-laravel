<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum CustomerSubscriptionStatus: string implements HasLabel, HasColor
{
    case AKTIF = 'aktif';
    case BERHENTI = 'berhenti';
    case ISOLIR = 'isolir';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::AKTIF    => 'Aktif',
            self::BERHENTI => 'Berhenti',
            self::ISOLIR   => 'Isolir',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::AKTIF    => 'success',
            self::BERHENTI => 'gray',
            self::ISOLIR   => 'warning',
        };
    }
}
