<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$s = app('App\Services\DashboardService');
$r = new ReflectionMethod($s, 'buildChart24h');
$r->setAccessible(true);
$result = $r->invoke($s);
echo count($result['series']) . " data points\n";
if (!empty($result['series'])) {
    echo "First: " . json_encode($result['series'][0]) . "\n";
    echo "Last:  " . json_encode(end($result['series'])) . "\n";
}
