<?php
/**
 * Backfill image_url for news articles by fetching og:image from article pages.
 */
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\NewsArticle;

$articles = NewsArticle::whereNull('image_url')
    ->whereNotNull('url')
    ->where('url', '!=', '')
    ->get();

echo "Found {$articles->count()} articles without images to backfill\n\n";

$updated = 0;
foreach ($articles as $article) {
    echo "Processing: {$article->title}\n";
    echo "  URL: {$article->url}\n";

    try {
        $ctx = stream_context_create([
            'http' => [
                'timeout' => 10,
                'header' => "User-Agent: Mozilla/5.0\r\n",
            ],
        ]);
        $html = @file_get_contents($article->url, false, $ctx);
        if (!$html) {
            echo "  -> Could not fetch page\n";
            continue;
        }

        $imageUrl = null;

        // Try og:image (property="og:image" content="...")
        if (preg_match('/<meta\s[^>]*property=["\']og:image["\'][^>]*content=["\']([^"\']+)["\']/i', $html, $m)) {
            $imageUrl = $m[1];
        }
        // Try reversed attribute order (content="..." property="og:image")
        elseif (preg_match('/<meta\s[^>]*content=["\']([^"\']+)["\'][^>]*property=["\']og:image["\']/i', $html, $m)) {
            $imageUrl = $m[1];
        }

        if ($imageUrl) {
            $article->update(['image_url' => $imageUrl]);
            echo "  -> Updated: " . mb_substr($imageUrl, 0, 80) . "\n";
            $updated++;
        } else {
            echo "  -> No og:image found\n";
        }
    } catch (\Throwable $e) {
        echo "  -> Error: {$e->getMessage()}\n";
    }

    // Small delay to be polite
    usleep(300000);
}

echo "\nDone! Updated {$updated} of {$articles->count()} articles\n";
