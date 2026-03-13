<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$xau = DB::table('world_prices')->where('symbol', 'XAU/USD')->orderByDesc('updated_at')->first();
$usd = DB::table('exchange_rates')->where('pair', 'USD/VND')->orderByDesc('updated_at')->first();

echo "XAU/USD: {$xau->price}\n";
echo "USD/VND: {$usd->rate}\n";

// Existing formula  
$worldVnd = (int) ($xau->price / 31.1035 * 0.8294 * $usd->rate);
echo "Existing formula result: " . number_format($worldVnd) . " VND\n";

// Correct per-luong formula: price_per_oz * (37.5g / 31.1035g) * rate
$worldPerLuong = (int) ($xau->price * 37.5 / 31.1035 * $usd->rate);
echo "Per luong formula: " . number_format($worldPerLuong) . " VND\n";

// Per luong in triệu  
echo "Per luong (triệu): " . round($worldPerLuong / 1_000_000, 2) . " tr\n";
