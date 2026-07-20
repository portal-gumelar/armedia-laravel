<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum TicketStatus: string implements HasLabel, HasColor, HasIcon
{
    case OPEN     = 'open';
    case PROCESS  = 'process';
    case RESOLVED = 'resolved';
    case CLOSED   = 'closed';

    public function getLabel(): string
    {
        return match($this) {
            self::OPEN     => 'Baru / Open',
            self::PROCESS  => 'Sedang Diproses',
            self::RESOLVED => 'Selesai',
            self::CLOSED   => 'Ditutup',
        };
    }

    public function getColor(): string|array|null
    {
        return match($this) {
            self::OPEN     => 'danger',
            self::PROCESS  => 'warning',
            self::RESOLVED => 'success',
            self::CLOSED   => 'gray',
        };
    }

    public function getIcon(): ?string
    {
        return match($this) {
            self::OPEN     => 'heroicon-o-exclamation-circle',
            self::PROCESS  => 'heroicon-o-wrench-screwdriver',
            self::RESOLVED => 'heroicon-o-check-circle',
            self::CLOSED   => 'heroicon-o-archive-box',
        };
    }
}
