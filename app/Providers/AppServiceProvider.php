<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use App\Models\AcrPointTransaction;
use App\Observers\AcrPointTransactionObserver;
use App\Models\AcrRedemption;
use App\Observers\AcrRedemptionObserver;

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
        Vite::prefetch(concurrency: 3);
    }
}
