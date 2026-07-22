<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;

enum TicketPriority: string implements HasLabel, HasColor, HasIcon
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
    case URGENT = 'urgent';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::LOW => 'Rendah',
            self::MEDIUM => 'Sedang',
            self::HIGH => 'Tinggi',
            self::URGENT => 'Mendesak (VIP)',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::LOW => 'gray',
            self::MEDIUM => 'info',
            self::HIGH => 'warning',
            self::URGENT => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::LOW => 'heroicon-m-arrow-down',
            self::MEDIUM => 'heroicon-m-minus',
            self::HIGH => 'heroicon-m-arrow-up',
            self::URGENT => 'heroicon-m-exclamation-triangle',
        };
    }
}
