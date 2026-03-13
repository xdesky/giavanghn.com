<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Fetch VnExpress RSS and show raw descriptions
$rss = file_get_contents('https://vnexpress.net/rss/kinh-doanh.rss');

preg_match_all(
    '/<item>.*?<title>(?:<!\[CDATA\[)?(.*?)(?:\]\]>)?<\/title>.*?<description>(?:<!\[CDATA\[)?(.*?)(?:\]\]>)?<\/description>/si',
    $rss,
    $matches,
    PREG_SET_ORDER
);

echo "=== kinh-doanh RSS items ===\n";
foreach (array_slice($matches, 0, 5) as $i => $m) {
    $title = trim(strip_tags(html_entity_decode($m[1])));
    $desc = $m[2];
    echo "\n--- Item {$i} ---\n";
    echo "Title: {$title}\n";
    echo "Desc (raw): " . mb_substr($desc, 0, 300) . "\n";
    
    // Try to extract image
    if (preg_match('/<img[^>]+src=["\']([^"\'>]+)["\']/', $desc, $img)) {
        echo "Image found: {$img[1]}\n";
    } else {
        echo "NO IMAGE in description\n";
    }
}

// Also check the actual description for gold articles without images
echo "\n\n=== Checking articles in DB without images ===\n";
$noImgArticles = App\Models\NewsArticle::where('source', 'vnexpress')
    ->whereNull('image_url')
    ->orderByDesc('published_at')
    ->limit(5)
    ->get(['id', 'title', 'url']);

foreach ($noImgArticles as $a) {
    echo "\nID {$a->id}: {$a->title}\n";
    echo "URL: {$a->url}\n";
    
    // Try fetching the article page and extract og:image
    $html = @file_get_contents($a->url);
    if ($html) {
        if (preg_match('/<meta\s+property=["\']og:image["\'][^>]*content=["\']([^"\']+)["\']/', $html, $og)) {
            echo "OG Image: {$og[1]}\n";
        } elseif (preg_match('/<meta\s+content=["\']([^"\']+)["\'][^>]*property=["\']og:image["\']/', $html, $og)) {
            echo "OG Image (alt): {$og[1]}\n";
        } else {
            echo "No og:image found\n";
        }
    } else {
        echo "Could not fetch page\n";
    }
}
