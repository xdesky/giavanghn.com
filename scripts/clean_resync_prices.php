<?php
/**
 * Clean and re-sync price_histories from gold_prices data.
 * Also adds yesterday's close as today's midnight opening point.
 */
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Cleaning all price_histories ===\n";
$deleted = DB::table('price_histories')->delete();
echo "Deleted {$deleted} records\n";

echo "\n=== Re-syncing from gold_prices ===\n";
Artisan::call('sync:price-histories', ['--hours' => 720]);
echo Artisan::output();

echo "\n=== Verifying SJC entries ===\n";
$sjc = DB::table('price_histories')
    ->where('symbol', 'sjc')
    ->orderBy('period_at')
    ->get();

foreach ($sjc as $h) {
    $close_m = round($h->close / 1_000_000, 2);
    $open_m = round($h->open / 1_000_000, 2);
    echo "  {$h->period_at} | Open: {$open_m}tr | Close: {$close_m}tr\n";
}
echo "\nTotal SJC entries: " . $sjc->count() . "\n";
