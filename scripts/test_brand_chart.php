<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$r = new \Illuminate\Http\Request(['period' => '7d']);
$c = app(\App\Http\Controllers\GoldPriceController::class);
$resp = $c->allBrandsChart($r);
$data = json_decode($resp->getContent(), true);
echo "Status: " . $resp->getStatusCode() . " Count: " . count($data) . "\n";
// Show first item keys
if (count($data) > 0) {
    echo "Keys: " . implode(', ', array_keys($data[0])) . "\n";
    echo "First: " . json_encode($data[0]) . "\n";
    echo "Last:  " . json_encode(end($data)) . "\n";
}
