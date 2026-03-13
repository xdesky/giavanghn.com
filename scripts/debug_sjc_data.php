<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\GoldPrice;
use App\Models\SjcChartPrice;
use App\Models\PriceHistory;

echo "=== SJC Gold Prices (latest 10 from gold_prices) ===\n";
$prices = GoldPrice::where('source', 'sjc')
    ->where('karat', '9999')
    ->orderByDesc('id')
    ->limit(10)
    ->get(['id', 'brand', 'karat', 'region', 'buy_price', 'sell_price', 'created_at']);

foreach ($prices as $p) {
    $sell_m = round($p->sell_price / 1_000_000, 2);
    $buy_m = round($p->buy_price / 1_000_000, 2);
    echo "  ID {$p->id} | {$p->brand} | {$p->region} | Buy: {$buy_m}tr | Sell: {$sell_m}tr | {$p->created_at}\n";
}

echo "\n=== SJC Chart Prices (sjc_chart_prices, last 5) ===\n";
$chartPrices = SjcChartPrice::orderByDesc('price_date')->limit(5)->get();
foreach ($chartPrices as $cp) {
    echo "  {$cp->price_date->format('Y-m-d')} | Buy: {$cp->buy_million}tr | Sell: {$cp->sell_million}tr | src: {$cp->source}\n";
}

echo "\n=== Price Histories for SJC (last 10) ===\n";
$histories = PriceHistory::where('symbol', 'sjc')->orderByDesc('id')->limit(10)->get();
foreach ($histories as $h) {
    $close_m = round($h->close / 1_000_000, 2);
    $open_m = round($h->open / 1_000_000, 2);
    echo "  ID {$h->id} | Open: {$open_m}tr | Close: {$close_m}tr | {$h->recorded_at}\n";
}

echo "\n=== Unique SJC brands in gold_prices ===\n";
$brands = GoldPrice::where('source', 'sjc')
    ->select('brand', 'karat')
    ->distinct()
    ->get();
foreach ($brands as $b) {
    echo "  {$b->brand} (karat: {$b->karat})\n";
}

echo "\n=== Try fetching SJC API directly ===\n";
$ctx = stream_context_create(['http' => ['timeout' => 10, 'header' => "User-Agent: Mozilla/5.0\r\n"]]);
$response = @file_get_contents('https://sjc.com.vn/GoldPrice/Services/PriceService.ashx', false, $ctx);
if ($response) {
    $data = json_decode($response, true);
    if (!empty($data['data'])) {
        foreach (array_slice($data['data'], 0, 5) as $item) {
            $type = $item['TypeName'] ?? '?';
            $branch = $item['BranchName'] ?? '?';
            $buy = $item['BuyValue'] ?? 0;
            $sell = $item['SellValue'] ?? 0;
            $buy_m = round($buy / 1_000_000, 2);
            $sell_m = round($sell / 1_000_000, 2);
            echo "  {$type} | {$branch} | Buy: {$buy_m}tr | Sell: {$sell_m}tr\n";
        }
    } else {
        echo "  No data in response\n";
    }
} else {
    echo "  Failed to fetch SJC API\n";
}
