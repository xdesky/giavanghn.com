<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\PriceHistory;
use App\Models\GoldPrice;

echo "=== Price histories for SJC today ===\n";
$today = now()->toDateString();
$sjcHistories = PriceHistory::where('symbol', 'sjc')
    ->where('period', '1h')
    ->whereDate('period_at', $today)
    ->orderBy('period_at')
    ->get();

foreach ($sjcHistories as $h) {
    $close_m = round($h->close / 1_000_000, 2);
    $open_m = round($h->open / 1_000_000, 2);
    $high_m = round($h->high / 1_000_000, 2);
    $low_m = round($h->low / 1_000_000, 2);
    echo "  {$h->period_at} | Open(buy): {$open_m}tr | High: {$high_m}tr | Low: {$low_m}tr | Close(sell): {$close_m}tr\n";
}

echo "\n=== Price histories for SJC last 24h ===\n";
$sjc24h = PriceHistory::where('symbol', 'sjc')
    ->where('period', '1h')
    ->where('period_at', '>=', now()->subHours(24))
    ->orderBy('period_at')
    ->get();

foreach ($sjc24h as $h) {
    $close_m = round($h->close / 1_000_000, 2);
    $open_m = round($h->open / 1_000_000, 2);
    echo "  {$h->period_at} | Open(buy): {$open_m}tr | Close(sell): {$close_m}tr\n";
}

echo "\n=== Raw gold_prices for SJC 9999 today ===\n";
$sjcToday = GoldPrice::where('source', 'sjc')
    ->where('karat', '9999')
    ->where('brand', 'like', '%1L%')
    ->whereDate('created_at', $today)
    ->orderBy('created_at')
    ->get(['brand', 'region', 'buy_price', 'sell_price', 'created_at']);

foreach ($sjcToday as $p) {
    $sell_m = round($p->sell_price / 1_000_000, 2);
    $buy_m = round($p->buy_price / 1_000_000, 2);
    echo "  {$p->created_at} | {$p->region} | Buy: {$buy_m}tr | Sell: {$sell_m}tr\n";
}

echo "\n=== SJC chart data shown to user (buildChart24h) ===\n";
$dashboard = new App\Services\DashboardService();
$data = $dashboard->buildSnapshot();
$chart = $data['chart24h'];
for ($i = 0; $i < count($chart['labels']); $i++) {
    $label = $chart['labels'][$i];
    $sell = $chart['sjc'][$i] ?? '?';
    $buy = $chart['sjcBuy'][$i] ?? '?';
    echo "  {$label} | Sell: {$sell}tr | Buy: {$buy}tr\n";
}
