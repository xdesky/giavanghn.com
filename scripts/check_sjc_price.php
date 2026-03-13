<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$h = App\Models\PriceHistory::where('symbol', 'sjc')
    ->where('period', '1h')
    ->latest('period_at')
    ->take(3)
    ->get(['close', 'period_at']);

foreach ($h as $row) {
    echo "close={$row->close}  period_at={$row->period_at}\n";
}

if ($h->isEmpty()) {
    echo "No PriceHistory data for sjc\n";
}

// Also check drawMainChart to see what unit it uses
echo "\n--- Also check gold_prices sell_price ---\n";
$gp = App\Models\GoldPrice::where('source', 'sjc')->latest()->first();
if ($gp) {
    echo "sell_price={$gp->sell_price}  buy_price={$gp->buy_price}\n";
}
