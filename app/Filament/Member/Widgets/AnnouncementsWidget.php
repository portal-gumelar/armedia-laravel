<?php

namespace App\Filament\Member\Widgets;

use App\Models\Announcement;
use Filament\Widgets\Widget;

class AnnouncementsWidget extends Widget
{
    protected static string $view = 'filament.member.widgets.announcements-widget';
    protected int | string | array $columnSpan = 'full';
    
    // Position below CustomerInfoWidget (which has default sorting, so let's give this a high sort number)
    protected static ?int $sort = 2;

    public function getAnnouncementsProperty()
    {
        return Announcement::where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })
            ->latest()
            ->take(5)
            ->get();
    }
}
