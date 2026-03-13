<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

// Check SJC raw data
echo "=== SJC (gold_prices) today ===\n";
$rows = DB::table('gold_prices')
    ->where('brand', 'like', 'Vàng SJC 1L%')
    ->where('sell_price', '>', 0)
    ->whereDate('updated_at', now()->toDateString())
    ->select('brand', 'sell_price', 'buy_price', 'updated_at')
    ->orderBy('updated_at', 'desc')
    ->limit(5)
    ->get();
foreach ($rows as $r) {
    echo "{$r->brand} | buy={$r->buy_price} | sell={$r->sell_price} | {$r->updated_at}\n";
}

echo "\n=== DOJI today ===\n";
$rows = DB::table('doji_gold_prices')
    ->where('brand', 'like', 'SJC%Bán Lẻ%')
    ->where('sell_price', '>', 0)
    ->whereDate('updated_at', now()->toDateString())
    ->select('brand', 'sell_price', 'buy_price', 'updated_at')
    ->orderBy('updated_at', 'desc')
    ->limit(5)
    ->get();
foreach ($rows as $r) {
    echo "{$r->brand} | buy={$r->buy_price} | sell={$r->sell_price} | {$r->updated_at}\n";
}

echo "\n=== PNJ today ===\n";
$rows = DB::table('pnj_gold_prices')
    ->where('brand', '=', 'Vàng miếng SJC 999.9')
    ->where('sell_price', '>', 0)
    ->whereDate('updated_at', now()->toDateString())
    ->select('brand', 'sell_price', 'buy_price', 'updated_at')
    ->orderBy('updated_at', 'desc')
    ->limit(3)
    ->get();
foreach ($rows as $r) {
    echo "{$r->brand} | buy={$r->buy_price} | sell={$r->sell_price} | {$r->updated_at}\n";
}

echo "\n=== Mi Hồng today ===\n";
$rows = DB::table('mihong_gold_prices')
    ->where('brand', '=', 'SJC')
    ->where('sell_price', '>', 0)
    ->whereDate('updated_at', now()->toDateString())
    ->select('brand', 'sell_price', 'buy_price', 'updated_at')
    ->orderBy('updated_at', 'desc')
    ->limit(3)
    ->get();
foreach ($rows as $r) {
    echo "{$r->brand} | buy={$r->buy_price} | sell={$r->sell_price} | {$r->updated_at}\n";
}

echo "\n=== API result for today ===\n";
$request = Illuminate\Http\Request::create('/api/v1/all-brands-chart?period=today', 'GET');
$response = $app->make(Illuminate\Contracts\Http\Kernel::class)->handle($request);
echo "Status: {$response->getStatusCode()}\n";
$data = json_decode($response->getContent(), true);
if (is_array($data)) {
    echo "Count: " . count($data) . "\n";
    if (count($data) > 0) {
        echo "First: " . json_encode($data[0], JSON_UNESCAPED_UNICODE) . "\n";
        echo "Last: " . json_encode(end($data), JSON_UNESCAPED_UNICODE) . "\n";
    }
} else {
    echo substr($response->getContent(), 0, 1500) . "\n";
}
