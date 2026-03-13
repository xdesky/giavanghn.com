<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::create('/api/v1/all-brands-chart?period=7d');
$response = $kernel->handle($request);
$data = json_decode($response->getContent(), true);
echo 'Status: ' . $response->getStatusCode() . PHP_EOL;
echo 'Body: ' . substr($response->getContent(), 0, 2000) . PHP_EOL;
if (!empty($data)) {
    echo 'First: ' . json_encode($data[0]) . PHP_EOL;
    $last = end($data);
    echo 'Last: ' . json_encode($last) . PHP_EOL;
    echo 'Keys: ' . implode(', ', array_keys($data[0])) . PHP_EOL;
}
