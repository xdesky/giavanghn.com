<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$a = \App\Models\AnalysisArticle::latest('published_at')->first();

// Find all internal links (not anchor-only links)
preg_match_all('/<a href="(\/[^#][^"]*)"[^>]*>/', $a->content, $matches);
echo "Internal links found:\n";
foreach ($matches[1] as $url) {
    echo "  -> {$url}\n";
}
echo "Total: " . count($matches[1]) . "\n";
