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

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->profile(\App\Filament\Pages\Auth\EditProfile::class)
            ->brandLogo('/images/logo-armedia.png')
            ->brandLogoHeight('2.5rem')
            ->colors([
                'primary' => Color::Sky, // Soft and calm branding
                'danger'  => Color::Rose,
                'gray'    => Color::Slate,
                'info'    => Color::Blue,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
            ])
            ->font('Poppins')
            ->sidebarCollapsibleOnDesktop()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
            ])
            ->navigationGroups([
                \Filament\Navigation\NavigationGroup::make()
                     ->label('Operasional ISP'),
                \Filament\Navigation\NavigationGroup::make()
                     ->label('Jaringan & Monitoring'),
                \Filament\Navigation\NavigationGroup::make()
                     ->label('Layanan Pelanggan'),
                \Filament\Navigation\NavigationGroup::make()
                     ->label('Member & Reward'),
                \Filament\Navigation\NavigationGroup::make()
                     ->label('Konten Website'),
                \Filament\Navigation\NavigationGroup::make()
                     ->label('Pengaturan'),
            ])
            ->plugins([
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make()
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
                fn () => request()->routeIs('filament.admin.auth.login') ? new \Illuminate\Support\HtmlString('
                    <div class="custom-login-wrapper">
                        <!-- Sisi Kiri: Gambar Background & Teks -->
                        <div class="promo-side" style="background-image: url(\'/images/bg-admin.jpg\');">
                            <div class="promo-overlay"></div>
                            <div class="promo-content">
                                <img src="/images/logo-armedia.png" alt="Logo" class="promo-logo">
                                <h1 class="promo-title">Sistem Manajemen Terpadu<br><span class="promo-highlight">ARMEDIA Portal</span></h1>
                                <p class="promo-desc">
                                    Kelola pelanggan, tagihan, infrastruktur jaringan (MikroTik &amp; OLT), serta keluhan tiket dalam satu dasbor pintar.
                                </p>
                                <div class="promo-cards">
                                    <div class="promo-card">
                                        <div class="promo-icon promo-icon-blue">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                        </div>
                                        <h3>Performa Cepat</h3>
                                        <p>Sistem asinkron teroptimasi untuk kecepatan maksimal</p>
                                    </div>
                                    <div class="promo-card">
                                        <div class="promo-icon promo-icon-green">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                        </div>
                                        <h3>Aman &amp; Terenkripsi</h3>
                                        <p>Keamanan data tingkat tinggi dengan enkripsi penuh</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Sisi Kanan: Form Login -->
                        <div class="login-side"></div>
                    </div>
                    <style>
                        .custom-login-wrapper {
                            position: fixed; top: 0; right: 0; bottom: 0; left: 0; z-index: -1; display: flex;
                        }
                        .promo-side {
                            display: none; width: 60%; background-size: cover; background-position: center; position: relative;
                        }
                        .promo-overlay {
                            position: absolute; top: 0; right: 0; bottom: 0; left: 0;
                            background: linear-gradient(to right, rgba(0,0,0,0.88), rgba(0,0,0,0.65), transparent);
                        }
                        .promo-content {
                            position: relative; z-index: 10; padding: 4rem; display: flex; flex-direction: column; justify-content: center; height: 100%; color: white;
                        }
                        .promo-logo { height: 3rem; width: auto; margin-bottom: 2rem; opacity: 0.9; filter: brightness(0) invert(1); }
                        .promo-title { font-size: 3rem; font-weight: 700; margin-bottom: 1rem; line-height: 1.2; }
                        .promo-highlight { color: #60a5fa; }
                        .promo-desc { font-size: 1.125rem; color: #e5e7eb; max-width: 36rem; line-height: 1.6; margin-bottom: 2rem; }
                        .promo-cards { display: flex; gap: 1rem; }
                        .promo-card { background: rgba(255,255,255,0.1); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); padding: 1.25rem; border-radius: 1rem; border: 1px solid rgba(255,255,255,0.2); flex: 1; }
                        .promo-icon { width: 2.5rem; height: 2.5rem; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; margin-bottom: 0.75rem; }
                        .promo-icon svg { width: 1.5rem; height: 1.5rem; }
                        .promo-icon-blue { background: rgba(59,130,246,0.3); color: #93c5fd; }
                        .promo-icon-green { background: rgba(16,185,129,0.3); color: #6ee7b7; }
                        .promo-card h3 { font-weight: 700; font-size: 1.1rem; color: white; margin: 0 0 0.25rem 0; }
                        .promo-card p { font-size: 0.875rem; color: #d1d5db; margin: 0; line-height: 1.5; }
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
                ') : ''
            )
            ->renderHook(
                'panels::head.end',
                fn () => new \Illuminate\Support\HtmlString(
                    '<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
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
                        
                        /* Leaflet Map Marker Pulse (Hanya untuk ODP Penuh/Bermasalah) */
                        .marker-pulse-red { animation: pulseRed 1.5s infinite; }
                        @keyframes pulseRed {
                            0% { filter: drop-shadow(0 0 0 rgba(239, 68, 68, 0.8)); transform: scale(1); }
                            50% { filter: drop-shadow(0 0 12px rgba(239, 68, 68, 0.8)); transform: scale(1.1); }
                            100% { filter: drop-shadow(0 0 0 rgba(239, 68, 68, 0)); transform: scale(1); }
                        }
                    </style>'
                )
            )
            ->renderHook(
                'panels::topbar.end',
                fn () => new \Illuminate\Support\HtmlString('
                    <div id="panel-switcher" style="display:flex; align-items:center; gap:6px; padding: 0 12px;">
                        <span style="font-size:11px; color:#9ca3af; font-weight:500; text-transform:uppercase; letter-spacing:0.05em; margin-right:4px;">Portal:</span>
                        <a href="/finance" target="_blank" title="Buka Finance Panel" style="display:inline-flex; align-items:center; gap:5px; padding:5px 12px; border-radius:9999px; background:linear-gradient(135deg,#f59e0b,#d97706); color:white; font-size:12px; font-weight:600; text-decoration:none; transition:all 0.2s; box-shadow:0 2px 8px rgba(245,158,11,0.35);"
                            onmouseover="this.style.transform=\'translateY(-1px)\'; this.style.boxShadow=\'0 4px 12px rgba(245,158,11,0.5)\';"
                            onmouseout="this.style.transform=\'none\'; this.style.boxShadow=\'0 2px 8px rgba(245,158,11,0.35)\';">
                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Finance
                        </a>
                        <a href="/hrm" target="_blank" title="Buka HRM Panel" style="display:inline-flex; align-items:center; gap:5px; padding:5px 12px; border-radius:9999px; background:linear-gradient(135deg,#8b5cf6,#7c3aed); color:white; font-size:12px; font-weight:600; text-decoration:none; transition:all 0.2s; box-shadow:0 2px 8px rgba(139,92,246,0.35);"
                            onmouseover="this.style.transform=\'translateY(-1px)\'; this.style.boxShadow=\'0 4px 12px rgba(139,92,246,0.5)\';"
                            onmouseout="this.style.transform=\'none\'; this.style.boxShadow=\'0 2px 8px rgba(139,92,246,0.35)\';">
                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            HRM
                        </a>
                        <a href="/mitra/login" target="_blank" title="Buka Mitra Panel" style="display:inline-flex; align-items:center; gap:5px; padding:5px 12px; border-radius:9999px; background:linear-gradient(135deg,#0ea5e9,#0284c7); color:white; font-size:12px; font-weight:600; text-decoration:none; transition:all 0.2s; box-shadow:0 2px 8px rgba(14,165,233,0.35);"
                            onmouseover="this.style.transform=\'translateY(-1px)\'; this.style.boxShadow=\'0 4px 12px rgba(14,165,233,0.5)\';"
                            onmouseout="this.style.transform=\'none\'; this.style.boxShadow=\'0 2px 8px rgba(14,165,233,0.35)\';">
                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            Mitra
                        </a>
                        <a href="/member/login" target="_blank" title="Buka Member Portal" style="display:inline-flex; align-items:center; gap:5px; padding:5px 12px; border-radius:9999px; background:linear-gradient(135deg,#10b981,#059669); color:white; font-size:12px; font-weight:600; text-decoration:none; transition:all 0.2s; box-shadow:0 2px 8px rgba(16,185,129,0.35);"
                            onmouseover="this.style.transform=\'translateY(-1px)\'; this.style.boxShadow=\'0 4px 12px rgba(16,185,129,0.5)\';"
                            onmouseout="this.style.transform=\'none\'; this.style.boxShadow=\'0 2px 8px rgba(16,185,129,0.35)\';">
                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Member
                        </a>
                    </div>
                ')
            )
            ->renderHook(
                'panels::body.end',
                fn () => new \Illuminate\Support\HtmlString(
                    '<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV/XN2/WXo=" crossorigin=""></script>
                    <script>
                        // Animasi Counter up untuk widget statistik
                        document.addEventListener("livewire:navigated", () => {
                            const easeOutExpo = (t) => t === 1 ? 1 : 1 - Math.pow(2, -10 * t);
                            const animateValue = (obj, start, end, duration) => {
                                let startTimestamp = null;
                                const step = (timestamp) => {
                                    if (!startTimestamp) startTimestamp = timestamp;
                                    const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                                    obj.innerHTML = Math.floor(easeOutExpo(progress) * (end - start) + start).toLocaleString();
                                    if (progress < 1) window.requestAnimationFrame(step);
                                };
                                window.requestAnimationFrame(step);
                            };
                            
                            setTimeout(() => {
                                document.querySelectorAll(".fi-wi-stats-overview-stat-value").forEach(el => {
                                    let text = el.innerText.replace(/[^0-9]/g, "");
                                    let val = parseInt(text);
                                    if(val > 0 && !el.hasAttribute("data-animated")) {
                                        el.setAttribute("data-animated", "true");
                                        animateValue(el, 0, val, 1500);
                                    }
                                });
                            }, 300); // beri jeda render
                        });
                    </script>'
                )
            );
    }
}
