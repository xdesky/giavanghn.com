<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Now: " . now()->toDateTimeString() . PHP_EOL . PHP_EOL;

// SJC 24h
$sjc24h = DB::table('gold_prices')
    ->where('source', 'sjc')
    ->where('sell_price', '>', 0)
    ->where('created_at', '>=', now()->subHours(24))
    ->orderBy('created_at')
    ->get(['sell_price', 'buy_price', 'created_at']);

echo "SJC strict 24h: " . $sjc24h->count() . " records" . PHP_EOL;

if ($sjc24h->count() < 3) {
    $sjc24h = DB::table('gold_prices')
        ->where('source', 'sjc')
        ->where('sell_price', '>', 0)
        ->orderByDesc('created_at')
        ->limit(30)
        ->get(['sell_price', 'buy_price', 'created_at'])
        ->sortBy('created_at')
        ->values();
    echo "SJC fallback last 30: " . $sjc24h->count() . " records" . PHP_EOL;
}

foreach ($sjc24h->take(3) as $r) {
    $at = Carbon\Carbon::parse($r->created_at);
    $label = $at->isToday() ? $at->format('H:i') : $at->format('H:i d/m');
    echo "  {$label} sell=" . round($r->sell_price / 1_000_000, 2) . " buy=" . round($r->buy_price / 1_000_000, 2) . PHP_EOL;
}
echo "  ..." . PHP_EOL;
$last = $sjc24h->last();
if ($last) {
    $at = Carbon\Carbon::parse($last->created_at);
    $label = $at->isToday() ? $at->format('H:i') : $at->format('H:i d/m');
    echo "  {$label} sell=" . round($last->sell_price / 1_000_000, 2) . " buy=" . round($last->buy_price / 1_000_000, 2) . PHP_EOL;
}

// XAU
echo PHP_EOL;
$xau24h = DB::table('world_prices')
    ->where('symbol', 'XAU/USD')
    ->where('created_at', '>=', now()->subHours(24))
    ->orderBy('created_at')
    ->get(['price', 'created_at']);

echo "XAU strict 24h: " . $xau24h->count() . " records" . PHP_EOL;

if ($xau24h->count() < 3) {
    $xau24h = DB::table('world_prices')
        ->where('symbol', 'XAU/USD')
        ->where('price', '>', 0)
        ->orderByDesc('created_at')
        ->limit(30)
        ->get(['price', 'created_at'])
        ->sortBy('created_at')
        ->values();
    echo "XAU fallback last 30: " . $xau24h->count() . " records" . PHP_EOL;
}

foreach ($xau24h->take(3) as $r) {
    $at = Carbon\Carbon::parse($r->created_at);
    $label = $at->isToday() ? $at->format('H:i') : $at->format('H:i d/m');
    echo "  {$label} price=" . round($r->price, 2) . PHP_EOL;
}
echo "  ..." . PHP_EOL;
$last = $xau24h->last();
if ($last) {
    $at = Carbon\Carbon::parse($last->created_at);
    $label = $at->isToday() ? $at->format('H:i') : $at->format('H:i d/m');
    echo "  {$label} price=" . round($last->price, 2) . PHP_EOL;
}
