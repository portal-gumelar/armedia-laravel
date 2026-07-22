<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$widget = new \App\Filament\Member\Widgets\CustomerInfoWidget();
// Wait, we can't easily render a Livewire component like this.
