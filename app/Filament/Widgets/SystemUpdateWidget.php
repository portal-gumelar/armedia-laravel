<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class SystemUpdateWidget extends Widget
{
    protected static string $view = 'filament.widgets.system-update-widget';
    protected int | string | array $columnSpan = 'full';
    
    // Pastikan widget ini tampil paling atas (opsional)
    protected static ?int $sort = -1;
}
