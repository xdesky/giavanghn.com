<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    $snapshot = app(\App\Services\DashboardService::class)->buildSnapshot();
    $html = view('gold.home', ['snapshot' => $snapshot, 'updatedAt' => now()->format('H:i:s d/m/Y')])->render();
    echo "RENDER OK - length: " . strlen($html) . PHP_EOL;
    // Extract news section
    $pos = strpos($html, 'id="tin-van"');
    if ($pos !== false) {
        echo PHP_EOL . "=== NEWS SECTION (from pos $pos) ===" . PHP_EOL;
        echo substr($html, $pos - 20, 2500) . PHP_EOL;
    } else {
        echo "News section not found!" . PHP_EOL;
    }
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . PHP_EOL;
    echo "File: " . $e->getFile() . ":" . $e->getLine() . PHP_EOL;
}
