<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class HrmPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('hrm')
            ->path('hrm')
            ->login()
            ->brandName('ARMEDIA HRM')
            ->brandLogo('/images/logo-armedia.png')
            ->brandLogoHeight('2.5rem')
            ->colors([
                'primary' => Color::Indigo,
            ])
            ->discoverResources(
                in: app_path('Filament/Hrm/Resources'),
                for: 'App\\Filament\\Hrm\\Resources'
            )
            ->discoverPages(
                in: app_path('Filament/Hrm/Pages'),
                for: 'App\\Filament\\Hrm\\Pages'
            )
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(
                in: app_path('Filament/Hrm/Widgets'),
                for: 'App\\Filament\\Hrm\\Widgets'
            )
            ->widgets([
                \App\Filament\Hrm\Widgets\HrmOverview::class,
                Widgets\AccountWidget::class,
            ])
            ->navigationGroups([
                NavigationGroup::make()->label('SDM & Karyawan'),
                NavigationGroup::make()->label('Kehadiran & Cuti'),
                NavigationGroup::make()->label('Penggajian'),
                NavigationGroup::make()->label('Pengaturan'),
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
