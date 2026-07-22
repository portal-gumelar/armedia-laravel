<?php

namespace App\Providers\Filament;

use App\Models\Mitra;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
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

class MitraPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('mitra')
            ->path('mitra')
            ->tenant(Mitra::class, slugAttribute: 'kode_mitra')
            ->login()
            ->brandLogo('/images/logo-armedia.png')
            ->brandLogoHeight('2.5rem')
            ->colors([
                'primary' => Color::Sky,
            ])
            ->discoverResources(in: app_path('Filament/Mitra/Resources'), for: 'App\\Filament\\Mitra\\Resources')
            ->discoverPages(in: app_path('Filament/Mitra/Pages'), for: 'App\\Filament\\Mitra\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Mitra/Widgets'), for: 'App\\Filament\\Mitra\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
            ])
            ->navigationGroups([
                \Filament\Navigation\NavigationGroup::make()->label('Pelanggan & Tagihan'),
                \Filament\Navigation\NavigationGroup::make()->label('Jaringan'),
                \Filament\Navigation\NavigationGroup::make()->label('Layanan'),
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
            ])
            ->renderHook(
                'panels::body.start',
                fn () => request()->routeIs('filament.mitra.auth.login') ? new \Illuminate\Support\HtmlString('
                    <div class="custom-login-wrapper">
                        <!-- Sisi Kiri: Gambar Background & Teks Edukasi/Promo -->
                        <div class="promo-side" style="background-image: url(\'/images/bg-mitra.jpg\');">
                            <div class="promo-overlay"></div>
                            <div class="promo-content">
                                <img src="/images/logo-armedia.png" alt="Logo" class="promo-logo">
                                <h1 class="promo-title">Portal Kemitraan<br><span class="promo-highlight">ARMEDIA</span></h1>
                                <p class="promo-desc">
                                    Kelola pelanggan area Anda, pantau pembayaran tagihan, dan monitor kondisi jaringan internet secara real-time.
                                </p>
                                
                                <div class="promo-cards">
                                    <div class="promo-card">
                                        <div class="promo-icon promo-icon-sky">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                        </div>
                                        <h3>Manajemen Pelanggan</h3>
                                        <p>Data pelanggan Anda tersolasi secara aman. Fokus pada pertumbuhan bisnis di wilayah kemitraan Anda.</p>
                                    </div>
                                    <div class="promo-card">
                                        <div class="promo-icon promo-icon-blue">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                        </div>
                                        <h3>Integrasi Sistem</h3>
                                        <p>Status koneksi OLT & MikroTik terhubung langsung ke dasbor Anda, minimalkan komplain.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Sisi Kanan: Form Login -->
                        <div class="login-side"></div>
                    </div>
                    <style>
                        /* Custom Styles to avoid Tailwind compilation issues */
                        .custom-login-wrapper {
                            position: fixed; top: 0; right: 0; bottom: 0; left: 0; z-index: -1; display: flex;
                        }
                        .promo-side {
                            display: none; width: 60%; background-size: cover; background-position: center; position: relative;
                        }
                        .promo-overlay {
                            position: absolute; top: 0; right: 0; bottom: 0; left: 0;
                            background: linear-gradient(to right, rgba(12, 74, 110, 0.95), rgba(7, 89, 133, 0.8), transparent);
                        }
                        .promo-content {
                            position: relative; z-index: 10; padding: 4rem; display: flex; flex-direction: column; justify-content: center; height: 100%; color: white;
                        }
                        .promo-logo { height: 3rem; width: auto; margin-bottom: 2rem; opacity: 0.9; filter: brightness(0) invert(1); }
                        .promo-title { font-size: 3rem; font-weight: 700; margin-bottom: 1rem; line-height: 1.2; }
                        .promo-highlight { color: #7dd3fc; }
                        .promo-desc { font-size: 1.125rem; color: #f0f9ff; max-width: 36rem; line-height: 1.6; margin-bottom: 2rem; }
                        .promo-cards { display: flex; gap: 1rem; margin-bottom: 2rem; }
                        .promo-card { background: rgba(255,255,255,0.1); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); padding: 1.25rem; border-radius: 1rem; border: 1px solid rgba(255,255,255,0.2); flex: 1; }
                        .promo-icon { width: 2.5rem; height: 2.5rem; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; margin-bottom: 0.75rem; }
                        .promo-icon svg { width: 1.5rem; height: 1.5rem; }
                        .promo-icon-sky { background: rgba(14, 165, 233, 0.3); color: white; }
                        .promo-icon-blue { background: rgba(59, 130, 246, 0.3); color: #93c5fd; }
                        .promo-card h3 { font-weight: 700; font-size: 1.25rem; color: white; margin: 0 0 0.25rem 0; }
                        .promo-card p { font-size: 0.875rem; color: #e0f2fe; margin: 0; line-height: 1.5; }
                        .login-side { width: 100%; background: white; }
                        
                        @media(min-width: 1024px) {
                            .promo-side { display: flex; }
                            .login-side { width: 40%; }
                            .fi-layout { background: transparent !important; }
                            main.fi-main { margin: 0 !important; margin-left: auto !important; width: 40% !important; max-width: none !important; min-height: 100vh !important; display: flex !important; align-items: center !important; justify-content: center !important; background-color: white !important; }
                            .dark main.fi-main { background-color: #030712 !important; }
                            .fi-simple-main { width: 100% !important; max-width: 28rem !important; margin: 0 auto !important; box-shadow: none !important; background: transparent !important; }
                        }
                    </style>
                ') : null
            );
    }
}
