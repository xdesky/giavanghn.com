<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$articles = \App\Models\AnalysisArticle::select('id','slug','published_at')
    ->orderByDesc('published_at')->get();

foreach ($articles as $a) {
    echo "{$a->id} | {$a->slug} | {$a->published_at}\n";
}

echo "\nTotal: " . $articles->count() . "\n";

// Also check news_articles for old link
$news = \App\Models\NewsArticle::where('url', 'like', '%gia-vang-giam-manh%')->get(['id','title','url']);
echo "\nNews linking to old slug:\n";
foreach ($news as $n) {
    echo "{$n->id} | {$n->url}\n";
}
