<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$d = new App\Services\DashboardService();
$data = $d->buildSnapshot();
foreach (array_slice($data['news'], 0, 8) as $n) {
    $img = !empty($n['image_url']) ? 'YES' : 'NO';
    echo "[{$img}] {$n['title']}\n";
}
