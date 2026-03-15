<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Now: " . now()->toDateTimeString() . PHP_EOL . PHP_EOL;

// SJC fallback last 30 records - what the chart actually gets
$sjc = DB::table('gold_prices')
    ->where('source', 'sjc')
    ->where('sell_price', '>', 0)
    ->orderByDesc('created_at')
    ->limit(30)
    ->get(['brand', 'sell_price', 'buy_price', 'created_at'])
    ->sortBy('created_at')
    ->values();

echo "SJC last 30 records (chart data):" . PHP_EOL;
foreach ($sjc as $r) {
    $sell = round($r->sell_price / 1_000_000, 2);
    $buy = round($r->buy_price / 1_000_000, 2);
    $at = Carbon\Carbon::parse($r->created_at);
    $label = $at->isToday() ? $at->format('H:i') : $at->format('H:i d/m');
    echo "  {$label} | sell={$sell} buy={$buy} | brand={$r->brand}" . PHP_EOL;
}

echo PHP_EOL . "Unique brands in this set:" . PHP_EOL;
$brands = $sjc->groupBy('brand');
foreach ($brands as $brand => $items) {
    $sells = $items->pluck('sell_price')->map(fn($v) => round($v/1_000_000, 2));
    echo "  [{$brand}] count={$items->count()} sell_range={$sells->min()}-{$sells->max()}" . PHP_EOL;
}
