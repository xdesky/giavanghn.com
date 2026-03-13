<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$date = $argv[1] ?? '2026-03-07';
$request = new \Illuminate\Http\Request(['date' => $date]);
$ctrl = app(\App\Http\Controllers\GoldPriceController::class);
$resp = $ctrl->pricesByDate($request);
$data = json_decode($resp->getContent(), true);

echo "Date: {$data['date']}\n";
echo "World records: " . count($data['world']) . "\n";
foreach ($data['brands'] as $brand => $items) {
    echo "  {$brand}: " . count($items) . " items\n";
}
echo "usWeekPoints: " . count($data['usWeekPoints']) . "\n";
echo "sjcWeekSellPoints: " . count($data['sjcWeekSellPoints']) . "\n";
