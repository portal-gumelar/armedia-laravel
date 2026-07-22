<?php

namespace App\Providers\Filament;

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

class MemberPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('member')
            ->path('member')
            ->authGuard('customer')
            ->login(\App\Filament\Member\Pages\Auth\Login::class)
            ->darkMode(false)
            ->profile(\App\Filament\Member\Pages\EditProfile::class)
            ->brandLogo('/images/logo-armedia.png')
            ->brandLogoHeight('2.5rem')
            ->colors([
                'primary' => Color::Blue,
            ])
            ->discoverResources(in: app_path('Filament/Member/Resources'), for: 'App\\Filament\\Member\\Resources')
            ->discoverPages(in: app_path('Filament/Member/Pages'), for: 'App\\Filament\\Member\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Member/Widgets'), for: 'App\\Filament\\Member\\Widgets')
            ->widgets([
                \App\Filament\Member\Widgets\CustomerInfoWidget::class,
                \App\Filament\Member\Widgets\AnnouncementsWidget::class,
                \App\Filament\Member\Widgets\UptimeChartWidget::class,
                \App\Filament\Member\Widgets\BillingSummaryWidget::class,
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
                'panels::body.end',
                fn () => !request()->routeIs('filament.member.auth.login') ? new \Illuminate\Support\HtmlString('
                    <style>
                        /* Animasi Fade In Halaman */
                        body { animation: fadeIn 0.6s ease-in-out; }
                        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

                        /* Animasi Hover pada Widget Statistik & Card */
                        .fi-wi-stats-overview-stat, .fi-wi-widget > .fi-card { 
                            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); 
                            border-radius: 12px; 
                        }
                        .fi-wi-stats-overview-stat:hover, .fi-wi-widget > .fi-card:hover { 
                            transform: translateY(-5px) scale(1.02); 
                            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); 
                            border-color: rgba(var(--primary-500), 0.4); 
                        }

                        /* Efek Hover Navigasi Sidebar & Active Indicator */
                        .fi-sidebar-item > a { transition: all 0.3s ease-in-out; position: relative; }
                        .fi-sidebar-item > a:hover { padding-left: 1.5rem; background: linear-gradient(90deg, rgba(var(--primary-500), 0.1) 0%, transparent 100%); }
                        .fi-sidebar-item.fi-active > a::before {
                            content: ""; position: absolute; left: 0; top: 20%; height: 60%; width: 4px;
                            background-color: rgba(var(--primary-600), 1); border-radius: 0 4px 4px 0;
                            animation: slideInLeft 0.4s ease-out forwards;
                        }
                        @keyframes slideInLeft { from { opacity: 0; transform: scaleY(0); } to { opacity: 1; transform: scaleY(1); } }

                        /* Tombol Primary Gradient & Animasi */
                        .fi-btn-color-primary { background: linear-gradient(135deg, rgba(var(--primary-600), 1) 0%, rgba(var(--primary-400), 1) 100%); transition: transform 0.2s ease, box-shadow 0.2s ease; border: none !important; }
                        .fi-btn-color-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 15px -3px rgba(var(--primary-500), 0.4); }

                        /* Efek Tabel Row (Hover) */
                        .fi-ta-record { transition: background-color 0.2s ease, transform 0.1s ease; }
                        .fi-ta-record:hover { background-color: rgba(var(--primary-500), 0.04) !important; transform: scale(1.001); z-index: 10; position: relative; box-shadow: 0 2px 5px rgba(0,0,0,0.02); }
                        
                        /* Breadcrumbs Fade */
                        .fi-breadcrumbs { animation: fadeInDown 0.5s ease-out; }
                        @keyframes fadeInDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }

                        /* Flutter-like Mobile Bottom Nav */
                        .mobile-bottom-nav { display: none; }
                        
                        @media (max-width: 768px) {
                            .mobile-bottom-nav {
                                display: flex; position: fixed; bottom: 0; left: 0; right: 0; z-index: 50;
                                background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);
                                border-top: 1px solid rgba(229, 231, 235, 0.5); padding-bottom: env(safe-area-inset-bottom);
                                box-shadow: 0 -10px 15px -3px rgba(0, 0, 0, 0.05);
                                justify-content: space-around; align-items: center; height: 65px;
                            }
                            .dark .mobile-bottom-nav {
                                background: rgba(17, 24, 39, 0.85); border-top-color: rgba(55, 65, 81, 0.5);
                            }
                            .nav-item {
                                display: flex; flex-direction: column; align-items: center; justify-content: center;
                                flex: 1; height: 100%; color: #9ca3af; text-decoration: none; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                                position: relative;
                            }
                            .nav-item svg { width: 24px; height: 24px; margin-bottom: 3px; transition: transform 0.3s; }
                            .nav-item span { font-size: 10px; font-weight: 600; letter-spacing: 0.025em; transition: opacity 0.3s; }
                            
                            .nav-item:active svg { transform: scale(0.9); }
                            
                            /* Active State */
                            .nav-item.active { color: #10b981; }
                            .nav-item.active svg { transform: translateY(-2px); filter: drop-shadow(0 2px 4px rgba(16,185,129,0.3)); }
                            
                            /* Tambahkan padding bawah ke main agar tidak tertutup nav */
                            .fi-main { padding-bottom: 85px !important; }
                            
                            /* Sembunyikan sidebar & hamburger menu bawaan Filament di mobile untuk UX murni */
                            .fi-topbar-sidebar-toggle { display: none !important; }
                            .fi-sidebar { display: none !important; }
                        }
                    </style>
                    
                    <div class="mobile-bottom-nav">
                        <a href="/member" class="nav-item ' . (request()->is('member') ? 'active' : '') . '">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                            <span>Home</span>
                        </a>
                        <a href="/member/invoices" class="nav-item ' . (request()->is('member/invoices*') ? 'active' : '') . '">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <span>Tagihan</span>
                        </a>
                        <a href="/member/tickets" class="nav-item ' . (request()->is('member/tickets*') ? 'active' : '') . '">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            <span>Bantuan</span>
                        </a>
                        <a href="/member/profile" class="nav-item ' . (request()->is('member/profile*') ? 'active' : '') . '">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            <span>Profil</span>
                        </a>
                    </div>
                ') : null
            );
    }
}
