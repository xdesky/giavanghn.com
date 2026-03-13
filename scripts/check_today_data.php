<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$today = '2026-03-08';

$models = [
    'GoldPrice' => App\Models\GoldPrice::class,
    'DojiGoldPrice' => App\Models\DojiGoldPrice::class,
    'BtmcGoldPrice' => App\Models\BtmcGoldPrice::class,
    'PnjGoldPrice' => App\Models\PnjGoldPrice::class,
    'PhuquyGoldPrice' => App\Models\PhuquyGoldPrice::class,
    'MihongGoldPrice' => App\Models\MihongGoldPrice::class,
    'BaotinmanhhaiGoldPrice' => App\Models\BaotinmanhhaiGoldPrice::class,
    'NgocthamGoldPrice' => App\Models\NgocthamGoldPrice::class,
    'WorldPrice' => App\Models\WorldPrice::class,
    'ExchangeRate' => App\Models\ExchangeRate::class,
    'NewsArticle' => App\Models\NewsArticle::class,
    'DailyStat' => App\Models\DailyStat::class,
    'MarketSentiment' => App\Models\MarketSentiment::class,
];

echo "=== Data check for {$today} ===\n\n";
echo str_pad("Model", 30) . str_pad("Count", 10) . "Last Record\n";
echo str_repeat("-", 80) . "\n";

foreach ($models as $name => $class) {
    try {
        $count = $class::whereDate('created_at', $today)->count();
        $last = $class::whereDate('created_at', $today)->latest()->value('created_at');
        echo str_pad($name, 30) . str_pad($count, 10) . ($last ?? 'none') . "\n";
    } catch (\Throwable $e) {
        echo str_pad($name, 30) . "ERROR: " . $e->getMessage() . "\n";
    }
}

echo "\n=== Latest CrawlLog entries (last run per source) ===\n\n";
echo str_pad("Crawler", 20) . str_pad("Status", 10) . str_pad("Records", 10) . "Last Run\n";
echo str_repeat("-", 70) . "\n";

$logs = App\Models\CrawlLog::whereDate('created_at', $today)
    ->selectRaw('crawler, status, records_count, created_at')
    ->orderByDesc('created_at')
    ->get()
    ->groupBy('crawler');

foreach ($logs as $crawler => $entries) {
    $latest = $entries->first();
    $successCount = $entries->where('status', 'success')->count();
    $failCount = $entries->where('status', 'failed')->count();
    echo str_pad($crawler, 20) 
        . str_pad($latest->status, 10) 
        . str_pad($latest->records_count ?? 0, 10) 
        . $latest->created_at 
        . " (ok:{$successCount} fail:{$failCount})"
        . "\n";
}
