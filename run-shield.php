<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

\Laravel\Prompts\Prompt::fallbackWhen(true);

try {
    Artisan::call('shield:generate', ['--all' => true, '--option' => 'policies_and_permissions']);
    echo Artisan::output();
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}

