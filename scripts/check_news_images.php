<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$articles = App\Models\NewsArticle::orderByDesc('published_at')->limit(15)->get(['id','title','source','image_url']);
foreach ($articles as $a) {
    $img = $a->image_url ? 'HAS_IMG' : 'NO_IMG';
    echo "{$a->id} | {$a->source} | {$img} | " . mb_substr($a->title, 0, 60) . "\n";
    if ($a->image_url) {
        echo "   -> " . mb_substr($a->image_url, 0, 100) . "\n";
    }
}

echo "\nTotal: " . App\Models\NewsArticle::count() . " articles\n";
echo "With images: " . App\Models\NewsArticle::whereNotNull('image_url')->where('image_url', '!=', '')->count() . "\n";
