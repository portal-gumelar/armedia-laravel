<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;
use App\Jobs\FetchOltMetricsJob;
use App\Jobs\FetchMikrotikNetwatchJob;
use App\Jobs\MikrotikSecurityScannerJob;

Schedule::command('armedia:generate-invoices')->monthlyOn(1, '01:00');
Schedule::command('armedia:auto-isolir')->dailyAt('00:05');

// Schedule Monitoring Jobs
Schedule::job(new FetchOltMetricsJob)->everyFiveMinutes();
Schedule::job(new FetchMikrotikNetwatchJob)->everyTwoMinutes();
Schedule::job(new MikrotikSecurityScannerJob)->everyTenMinutes();
