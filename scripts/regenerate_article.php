<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\GoldAnalysisArticleService;
use App\Services\DashboardService;
use App\Models\AnalysisArticle;

$service = app(GoldAnalysisArticleService::class);

// Delete today's articles so we can regenerate with new format
$deleted = AnalysisArticle::whereDate('analysis_date', now()->toDateString())->delete();
echo "Deleted {$deleted} old articles for today\n";

// Force regenerate to get the new HTML content with tables & image
$article = $service->generate('change', now(), true);

if ($article) {
    echo "Generated article: " . $article->title . PHP_EOL;
    echo "Slug: " . $article->slug . PHP_EOL;
    echo "Thumbnail: " . ($article->thumbnail_path ?? 'none') . PHP_EOL;
    echo "Word count: " . $article->word_count . PHP_EOL;
    echo "Has <img>: " . (str_contains($article->content, '<img') ? 'yes' : 'no') . PHP_EOL;
    echo "Has <table>: " . (str_contains($article->content, '<table') ? 'yes' : 'no') . PHP_EOL;
    echo "Has TOC: " . (str_contains($article->content, 'Mục lục') ? 'yes' : 'no') . PHP_EOL;
    echo "Has internal links: " . (preg_match_all('/<a href="\/[^"]*"/', $article->content) ?: 0) . PHP_EOL;
    echo PHP_EOL . "URL: /phan-tich/" . $article->slug . PHP_EOL;
} else {
    echo "Failed to generate article\n";
}
