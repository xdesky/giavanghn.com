<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== RAW DATA: All brands today ===\n\n";

$configs = [
    'SJC'        => ['table' => 'gold_prices',               'where' => ['brand', 'like', 'Vàng SJC 1L%']],
    'DOJI'       => ['table' => 'doji_gold_prices',           'where' => ['brand', 'like', 'SJC%Bán Lẻ%']],
    'PNJ'        => ['table' => 'pnj_gold_prices',            'where' => ['brand', '=', 'Vàng miếng SJC 999.9']],
    'BTMC'       => ['table' => 'btmc_gold_prices',            'where' => ['brand', '=', 'Vàng Miếng SJC']],
    'Phú Quý'    => ['table' => 'phuquy_gold_prices',         'where' => ['brand', '=', 'Vàng miếng SJC']],
    'Mi Hồng'    => ['table' => 'mihong_gold_prices',          'where' => ['brand', '=', 'SJC']],
    'Bảo Tín MH' => ['table' => 'baotinmanhhai_gold_prices',  'where' => ['brand', 'like', 'Vàng miếng SJC%']],
    'Ngọc Thẩm'  => ['table' => 'ngoctham_gold_prices',       'where' => ['brand', 'like', 'Vàng Miếng SJC%']],
];

foreach ($configs as $label => $cfg) {
    echo "--- {$label} ({$cfg['table']}) ---\n";
    $rows = DB::table($cfg['table'])
        ->where($cfg['where'][0], $cfg['where'][1], $cfg['where'][2])
        ->where('sell_price', '>', 0)
        ->whereDate('updated_at', now()->toDateString())
        ->select('brand', 'buy_price', 'sell_price', 'updated_at')
        ->orderBy('updated_at')
        ->get()
        ->unique(fn($r) => substr($r->updated_at, 0, 16));

    foreach ($rows as $r) {
        $sell = (float) $r->sell_price;
        $normalized = $sell;
        if ($sell > 0 && $sell < 100_000_000) {
            $normalized = $sell * 10;
        }
        $trieu = round($normalized / 1_000_000, 2);
        echo "  {$r->updated_at} | raw_sell={$r->sell_price} | normalized={$normalized} | triệu/lượng={$trieu}\n";
    }
    echo "\n";
}

echo "\n=== API output: today ===\n";
$request = Illuminate\Http\Request::create('/api/v1/all-brands-chart?period=today', 'GET');
$response = $app->make(Illuminate\Contracts\Http\Kernel::class)->handle($request);
$data = json_decode($response->getContent(), true);
foreach ($data as $d) {
    echo json_encode($d, JSON_UNESCAPED_UNICODE) . "\n";
}

echo "\n=== API output: 7d (fixed - latest per day) ===\n";
$request2 = Illuminate\Http\Request::create('/api/v1/all-brands-chart?period=7d', 'GET');
$response2 = $app->make(Illuminate\Contracts\Http\Kernel::class)->handle($request2);
$data2 = json_decode($response2->getContent(), true);
foreach ($data2 as $d) {
    echo json_encode($d, JSON_UNESCAPED_UNICODE) . "\n";
}
echo "\nDone.\n";
