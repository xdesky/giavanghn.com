<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Backfill image_url for existing news articles from VnExpress RSS
$rss = file_get_contents('https://vnexpress.net/rss/kinh-doanh.rss');
preg_match_all(
    '/<item>.*?<title>(?:<!\[CDATA\[)?(.*?)(?:\]\]>)?<\/title>.*?<description>(?:<!\[CDATA\[)?(.*?)(?:\]\]>)?<\/description>.*?<\/item>/si',
    $rss,
    $matches,
    PREG_SET_ORDER
);

$updated = 0;
foreach ($matches as $m) {
    $title = trim(strip_tags(html_entity_decode($m[1])));
    $desc = $m[2];
    if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/', $desc, $img)) {
        $imageUrl = $img[1];
        $affected = \App\Models\NewsArticle::where('title', $title)
            ->whereNull('image_url')
            ->update(['image_url' => $imageUrl]);
        if ($affected) {
            echo "Updated: {$title}\n  -> {$imageUrl}\n\n";
            $updated += $affected;
        }
    }
}

// Also try tin-moi-nhat RSS
$rss2 = file_get_contents('https://vnexpress.net/rss/tin-moi-nhat.rss');
preg_match_all(
    '/<item>.*?<title>(?:<!\[CDATA\[)?(.*?)(?:\]\]>)?<\/title>.*?<description>(?:<!\[CDATA\[)?(.*?)(?:\]\]>)?<\/description>.*?<\/item>/si',
    $rss2,
    $matches2,
    PREG_SET_ORDER
);

foreach ($matches2 as $m) {
    $title = trim(strip_tags(html_entity_decode($m[1])));
    $desc = $m[2];
    if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/', $desc, $img)) {
        $imageUrl = $img[1];
        $affected = \App\Models\NewsArticle::where('title', $title)
            ->whereNull('image_url')
            ->update(['image_url' => $imageUrl]);
        if ($affected) {
            echo "Updated: {$title}\n  -> {$imageUrl}\n\n";
            $updated += $affected;
        }
    }
}

echo "Total updated: {$updated}\n";

// Summary
$total = \App\Models\NewsArticle::count();
$withImage = \App\Models\NewsArticle::whereNotNull('image_url')->count();
echo "News articles: {$total} total, {$withImage} with images\n";

