<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Check each brand table structure and sample flagship product
$tables = [
    'gold_prices' => 'SJC (gold_prices)',
    'btmc_gold_prices' => 'BTMC',
    'doji_gold_prices' => 'DOJI',
    'pnj_gold_prices' => 'PNJ',
    'phuquy_gold_prices' => 'Phú Quý',
    'mihong_gold_prices' => 'Mi Hồng',
    'baotinmanhhai_gold_prices' => 'Bảo Tín Mạnh Hải',
    'ngoctham_gold_prices' => 'Ngọc Thẩm',
];

foreach ($tables as $table => $label) {
    echo "\n=== $label ($table) ===\n";
    try {
        $cols = \Illuminate\Support\Facades\Schema::getColumnListing($table);
        echo "Columns: " . implode(', ', $cols) . "\n";
        $brands = DB::table($table)->select('brand')->distinct()->pluck('brand');
        echo "Brands/Products: " . $brands->implode(' | ') . "\n";
        // Find flagship (SJC 1L or vang mieng or 9999)
        $flagship = DB::table($table)->where('brand', 'like', '%SJC 1L%')
            ->orWhere('brand', 'like', '%Vàng miếng%')
            ->orWhere('brand', 'like', '%999%')
            ->orderByDesc('updated_at')->first();
        if ($flagship) echo "Flagship sample: " . json_encode($flagship, JSON_UNESCAPED_UNICODE) . "\n";
    } catch (Throwable $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}

// World price
echo "\n=== World Prices ===\n";
$cols = \Illuminate\Support\Facades\Schema::getColumnListing('world_prices');
echo "Columns: " . implode(', ', $cols) . "\n";
$symbols = DB::table('world_prices')->select('symbol')->distinct()->pluck('symbol');
echo "Symbols: " . $symbols->implode(' | ') . "\n";
$xau = DB::table('world_prices')->where('symbol', 'XAUUSD')->orderByDesc('updated_at')->first();
if ($xau) echo "XAU sample: " . json_encode($xau, JSON_UNESCAPED_UNICODE) . "\n";

// Exchange rate for conversion
echo "\n=== Exchange Rates ===\n";
$usdvnd = DB::table('exchange_rates')->where('pair', 'USDVND')->orderByDesc('updated_at')->first();
if ($usdvnd) echo "USDVND: " . json_encode($usdvnd, JSON_UNESCAPED_UNICODE) . "\n";
