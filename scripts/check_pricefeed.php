<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Recent SJC 0.5 chi records ===\n";
$rows = DB::table('gold_prices')
    ->where('source', 'sjc')
    ->where('brand', 'Vàng SJC 0.5 chỉ, 1 chỉ, 2 chỉ')
    ->orderByDesc('id')
    ->limit(10)
    ->get(['id','sell_price','buy_price','change_percent','created_at','updated_at']);
foreach ($rows as $r) {
    echo "id:{$r->id} sell:{$r->sell_price} buy:{$r->buy_price} chg%:{$r->change_percent} created:{$r->created_at} updated:{$r->updated_at}\n";
}

echo "\n=== Price feed today (change_percent != 0, source=sjc) ===\n";
$feed = DB::table('gold_prices')
    ->where('source', 'sjc')
    ->whereDate('created_at', '2026-03-09')
    ->where('change_percent', '!=', 0)
    ->orderByDesc('id')
    ->limit(10)
    ->get(['id','brand','sell_price','buy_price','change_percent','created_at','updated_at']);
foreach ($feed as $r) {
    echo "id:{$r->id} brand:{$r->brand} sell:{$r->sell_price} chg%:{$r->change_percent} created:{$r->created_at} updated:{$r->updated_at}\n";
}

echo "\n=== All SJC records today (all brands) ===\n";
$all = DB::table('gold_prices')
    ->where('source', 'sjc')
    ->whereDate('created_at', '2026-03-09')
    ->orderByDesc('id')
    ->limit(30)
    ->get(['id','brand','sell_price','change_percent','created_at','updated_at']);
foreach ($all as $r) {
    echo "id:{$r->id} chg%:{$r->change_percent} created:{$r->created_at} updated:{$r->updated_at} brand:" . mb_substr($r->brand, 0, 30) . "\n";
}
