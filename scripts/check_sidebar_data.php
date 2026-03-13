<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Check gold_prices brands (SJC table)
echo "=== gold_prices (SJC) brands ===\n";
$brands = DB::table('gold_prices')->select('brand')->distinct()->pluck('brand');
foreach ($brands as $b) echo "  $b\n";

// Check BTMC brands  
echo "\n=== btmc_gold_prices brands ===\n";
$brands = DB::table('btmc_gold_prices')->select('brand')->distinct()->pluck('brand');
foreach ($brands as $b) echo "  $b\n";

// Check DOJI brands
echo "\n=== doji_gold_prices brands ===\n";
$brands = DB::table('doji_gold_prices')->select('brand')->distinct()->pluck('brand');
foreach ($brands as $b) echo "  $b\n";

// Check PNJ brands
echo "\n=== pnj_gold_prices brands ===\n";
$brands = DB::table('pnj_gold_prices')->select('brand')->distinct()->pluck('brand');
foreach ($brands as $b) echo "  $b\n";

// Check if silver_prices table exists
echo "\n=== Tables matching 'silver' or 'bac' ===\n";
$tables = DB::select("SHOW TABLES LIKE '%silver%'");
foreach ($tables as $t) echo "  " . json_encode($t) . "\n";
$tables = DB::select("SHOW TABLES LIKE '%bac%'");
foreach ($tables as $t) echo "  " . json_encode($t) . "\n";

// Check if bank_rates table exists
echo "\n=== Tables matching 'bank' ===\n";
$tables = DB::select("SHOW TABLES LIKE '%bank%'");
foreach ($tables as $t) echo "  " . json_encode($t) . "\n";

// Check exchange_rates sources
echo "\n=== exchange_rates sources ===\n";
$sources = DB::table('exchange_rates')->select('source')->distinct()->pluck('source');
foreach ($sources as $s) echo "  $s\n";

// Check Bao Tin Manh Hai brands  
echo "\n=== baotinmanhhai_gold_prices brands ===\n";
$brands = DB::table('baotinmanhhai_gold_prices')->select('brand')->distinct()->pluck('brand');
foreach ($brands as $b) echo "  $b\n";
