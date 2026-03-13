<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Sync today's SJC chart price
$service = new App\Services\SjcChartPriceService();
$result = $service->syncFromGoldPrices();

if ($result) {
    echo "Synced today: Buy {$result->buy_million}tr | Sell {$result->sell_million}tr\n";
} else {
    echo "No data to sync for today\n";
}

// Verify last 5 entries  
echo "\nLast 5 sjc_chart_prices:\n";
$entries = App\Models\SjcChartPrice::orderByDesc('price_date')->limit(5)->get();
foreach ($entries as $e) {
    echo "  {$e->price_date->format('Y-m-d')} | Buy: {$e->buy_million}tr | Sell: {$e->sell_million}tr | src: {$e->source}\n";
}
