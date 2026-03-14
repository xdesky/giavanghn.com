<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$articles = App\Models\NewsArticle::where('url', 'like', '/phan-tich/%')->get();
echo "Found " . $articles->count() . " news articles with old /phan-tich/ URLs\n";

foreach ($articles as $n) {
    $old = $n->url;
    $n->url = str_replace('/phan-tich/', '/tin-tuc-gia-vang/trong-nuoc/', $n->url);
    $n->save();
    echo "Updated: {$old} -> {$n->url}\n";
}

echo "Done.\n";
