<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

// All symbols
$symbols = DB::table('world_prices')->select('symbol', 'name')->distinct()->get();
echo "=== All symbols ===\n";
foreach ($symbols as $s) {
    echo "  {$s->symbol}: {$s->name}\n";
}

// Latest prices
echo "\n=== Latest prices per symbol ===\n";
$latest = DB::table('world_prices as wp')
    ->joinSub(
        DB::table('world_prices')->selectRaw('symbol, MAX(id) as max_id')->groupBy('symbol'),
        'latest',
        fn($j) => $j->on('wp.id', '=', 'latest.max_id')
    )
    ->get();
foreach ($latest as $r) {
    echo "  {$r->symbol}: price={$r->price} change={$r->change_percent}% ({$r->change_amount}) {$r->currency} updated={$r->updated_at}\n";
}

// Historical data depth per symbol
echo "\n=== Data depth per symbol ===\n";
$depth = DB::table('world_prices')
    ->selectRaw('symbol, COUNT(*) as cnt, MIN(DATE(created_at)) as first_date, MAX(DATE(created_at)) as last_date, COUNT(DISTINCT DATE(created_at)) as days')
    ->groupBy('symbol')
    ->get();
foreach ($depth as $d) {
    echo "  {$d->symbol}: {$d->cnt} records, {$d->days} days ({$d->first_date} to {$d->last_date})\n";
}

// Weekly chart data for XAU/USD
echo "\n=== XAU/USD 7-day chart ===\n";
$weekly = DB::table('world_prices')
    ->selectRaw('DATE(created_at) as d, AVG(price) as avg_price, MAX(price) as high, MIN(price) as low')
    ->where('symbol', 'XAU/USD')
    ->where('price', '>', 0)
    ->groupByRaw('DATE(created_at)')
    ->orderByDesc('d')
    ->limit(7)
    ->get();
foreach ($weekly as $r) {
    echo "  {$r->d}: avg=" . round($r->avg_price, 2) . " high={$r->high} low={$r->low}\n";
}
