<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$service = new App\Services\DashboardService();
$snapshot = $service->buildSnapshot();

$brands = ['btmhCard', 'btmcCard', 'pnjCard', 'dojiCard', 'phuquyCard', 'mihongCard', 'ngocthamCard'];
foreach ($brands as $brand) {
    $c = $snapshot[$brand];
    $s = count($c['weekSellPoints']);
    $b = count($c['weekBuyPoints']);
    $d = count($c['weekDates']);
    $match = ($s === $b && $s === $d) ? 'OK' : 'MISMATCH';
    echo "{$c['title']}: sell=$s buy=$b dates=$d → $match\n";
}
