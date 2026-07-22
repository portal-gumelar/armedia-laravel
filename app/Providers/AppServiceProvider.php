<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use App\Models\AcrPointTransaction;
use App\Observers\AcrPointTransactionObserver;
use App\Models\AcrRedemption;
use App\Observers\AcrRedemptionObserver;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\HtmlString;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Database\Eloquent\Model::unguard();
        AcrPointTransaction::observe(AcrPointTransactionObserver::class);
        AcrRedemption::observe(AcrRedemptionObserver::class);
        \App\Models\Customer::observe(\App\Observers\CustomerObserver::class);
        \App\Models\VpnAccount::observe(\App\Observers\VpnAccountObserver::class);
        Vite::prefetch(concurrency: 3);

        // Beri akses penuh ke super_admin (Bypass semua permission policy)
        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            return $user->hasRole('super_admin') ? true : null;
        });

        // Inject Premium Glassmorphism CSS for Filament UI
        FilamentView::registerRenderHook(
            PanelsRenderHook::HEAD_END,
            fn (): HtmlString => new HtmlString('
                <style>
                    /* Premium Navy-Gold Glassmorphism Styles */
                    :root {
                        --glass-bg: rgba(255, 255, 255, 0.7);
                        --glass-border: rgba(255, 255, 255, 0.4);
                        --glass-shadow: 0 8px 32px 0 rgba(4, 8, 20, 0.08);
                    }
                    .dark:root {
                        --glass-bg: rgba(4, 8, 20, 0.6);
                        --glass-border: rgba(255, 215, 0, 0.15);
                        --glass-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.4);
                    }

                    /* Stats Overview Cards */
                    .fi-wi-stats-overview-stat {
                        background: var(--glass-bg) !important;
                        backdrop-filter: blur(12px) !important;
                        -webkit-backdrop-filter: blur(12px) !important;
                        border: 1px solid var(--glass-border) !important;
                        box-shadow: var(--glass-shadow) !important;
                        border-radius: 1rem !important;
                        transition: transform 0.2s ease, box-shadow 0.2s ease;
                    }
                    .fi-wi-stats-overview-stat:hover {
                        transform: translateY(-2px);
                        box-shadow: 0 12px 40px 0 rgba(4, 8, 20, 0.12) !important;
                    }

                    /* Sections / Generic Cards */
                    .fi-section {
                        background: var(--glass-bg) !important;
                        backdrop-filter: blur(12px) !important;
                        -webkit-backdrop-filter: blur(12px) !important;
                        border: 1px solid var(--glass-border) !important;
                        box-shadow: var(--glass-shadow) !important;
                        border-radius: 1rem !important;
                    }
                    
                    /* Table adjustments for glassmorphism */
                    .fi-ta-ctn {
                        background: var(--glass-bg) !important;
                        backdrop-filter: blur(12px) !important;
                        border-radius: 1rem !important;
                        border: 1px solid var(--glass-border) !important;
                        box-shadow: var(--glass-shadow) !important;
                    }
                    
                    /* Buttons inside Filament */
                    .fi-btn-color-primary {
                        background-color: #040814 !important;
                        color: #d4af37 !important;
                    }
                    .fi-btn-color-primary:hover {
                        background-color: #0a1128 !important;
                        box-shadow: 0 0 15px rgba(212, 175, 55, 0.3) !important;
                    }
                    .dark .fi-btn-color-primary {
                        border: 1px solid rgba(212,175,55,0.4) !important;
                    }
                </style>
            ')
        );
    }
}
