<?php

namespace App\Filament\Member\Widgets;

use Filament\Widgets\Widget;

class CustomerInfoWidget extends Widget
{
    protected static string $view = 'filament.member.widgets.customer-info-widget';
    protected int | string | array $columnSpan = 'full';
    
    public function getCustomerProperty()
    {
        return auth('customer')->user();
    }
}
