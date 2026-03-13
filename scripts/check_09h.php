<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$rows = \App\Models\GoldPrice::where('source', 'sjc')
    ->where('created_at', '>=', '2026-03-09 09:00:00')
    ->where('created_at', '<', '2026-03-09 10:00:00')
    ->orderBy('id')
    ->get(['brand', 'sell_price', 'buy_price', 'created_at']);

foreach ($rows as $r) {
    echo "{$r->brand} | sell={$r->sell_price} buy={$r->buy_price} | {$r->created_at}\n";
}
