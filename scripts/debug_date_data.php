<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$date = $argv[1] ?? '2026-03-08';

echo "=== Raw data for {$date} ===\n\n";

// BTMC
$btmc = DB::table('btmc_gold_prices')->whereDate('created_at', $date)->get();
echo "BTMC ({$btmc->count()} records):\n";
foreach ($btmc as $r) {
    echo "  brand={$r->brand} buy={$r->buy_price} sell={$r->sell_price} change={$r->change_percent}\n";
}

// BaoTinManhHai
$btmh = DB::table('baotinmanhhai_gold_prices')->whereDate('created_at', $date)->get();
echo "\nBaoTinManhHai ({$btmh->count()} records):\n";
foreach ($btmh as $r) {
    echo "  brand={$r->brand} buy={$r->buy_price} sell={$r->sell_price} change={$r->change_percent}\n";
}

// MiHong
$mihong = DB::table('mihong_gold_prices')->whereDate('created_at', $date)->get();
echo "\nMiHong ({$mihong->count()} records):\n";
foreach ($mihong as $r) {
    echo "  brand={$r->brand} code={$r->code} buy={$r->buy_price} sell={$r->sell_price}\n";
}

// SJC (gold_prices source=sjc)
$sjc = DB::table('gold_prices')->where('source', 'sjc')->whereDate('created_at', $date)->get();
echo "\nSJC gold_prices ({$sjc->count()} records):\n";
foreach ($sjc as $r) {
    echo "  brand={$r->brand} region={$r->region} buy={$r->buy_price} sell={$r->sell_price}\n";
}

// World
$world = DB::table('world_prices')->whereDate('created_at', $date)->orderByDesc('id')->get()->unique('symbol');
echo "\nWorld ({$world->count()} unique symbols):\n";
foreach ($world as $r) {
    echo "  {$r->symbol} price={$r->price} change={$r->change_percent}\n";
}
