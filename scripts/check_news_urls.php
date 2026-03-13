<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Fix giavanghn news URLs to point to actual analysis article slugs
$giavanghnNews = App\Models\NewsArticle::where('source', 'giavanghn')
    ->where('url', 'like', '/phan-tich/%')
    ->get();

foreach ($giavanghnNews as $n) {
    $date = $n->published_at->toDateString();
    // Find the latest analysis article for the same date
    $article = App\Models\AnalysisArticle::whereDate('analysis_date', $date)
        ->latest()
        ->first();

    if ($article) {
        $correctUrl = '/phan-tich/' . $article->slug;
        if ($n->url !== $correctUrl) {
            echo "FIXING #" . $n->id . PHP_EOL;
            echo "  OLD: " . $n->url . PHP_EOL;
            echo "  NEW: " . $correctUrl . PHP_EOL;
            $n->update(['url' => $correctUrl]);
        } else {
            echo "OK #" . $n->id . " | " . $n->url . PHP_EOL;
        }
    } else {
        echo "NO ARTICLE for news #" . $n->id . " date=" . $date . PHP_EOL;
    }
}

echo PHP_EOL . "Done." . PHP_EOL;
