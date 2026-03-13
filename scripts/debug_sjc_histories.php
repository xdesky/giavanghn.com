<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== All price_histories for SJC ===\n";
$all = App\Models\PriceHistory::where('symbol', 'sjc')
    ->orderBy('id')
    ->get();

foreach ($all as $h) {
    echo "  ID {$h->id} | period_at: {$h->period_at} | recorded_at: {$h->recorded_at} | Open: " . round($h->open/1e6,2) . "tr | Close: " . round($h->close/1e6,2) . "tr | created: {$h->created_at}\n";
}
echo "\nTotal: {$all->count()}\n";
