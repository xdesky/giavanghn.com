<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== gold_prices for '0.5 chỉ' after 20:00 today ===\n";
$rows = DB::table('gold_prices')
    ->where('source', 'sjc')
    ->where('brand', 'Vàng SJC 0.5 chỉ, 1 chỉ, 2 chỉ')
    ->where('created_at', '>', '2026-03-09 17:00:00')
    ->get(['id', 'brand', 'region', 'created_at', 'buy_price', 'sell_price']);
echo "Count: " . $rows->count() . "\n";
foreach ($rows as $r) {
    echo "  id={$r->id} | {$r->created_at} | {$r->region} | Buy={$r->buy_price} | Sell={$r->sell_price}\n";
}

echo "\n=== ALL gold_prices after 17:00 today (any brand) ===\n";
$rows2 = DB::table('gold_prices')
    ->where('source', 'sjc')
    ->where('created_at', '>', '2026-03-09 17:00:00')
    ->orderBy('created_at')
    ->take(20)
    ->get(['id', 'brand', 'region', 'created_at', 'buy_price', 'sell_price']);
echo "Count: " . $rows2->count() . "\n";
foreach ($rows2 as $r) {
    echo "  id={$r->id} | {$r->created_at} | {$r->region} | {$r->brand} | Sell={$r->sell_price}\n";
}

echo "\n=== Current SJC price_histories (after clean) ===\n";
$ph = DB::table('price_histories')->where('symbol', 'sjc')->orderBy('period_at')->get();
foreach ($ph as $r) {
    echo "  {$r->period_at} | open={$r->open} | close={$r->close}\n";
}
