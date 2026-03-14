<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Update the news article that links to the old deleted slug
$newSlug = \App\Models\AnalysisArticle::latest('published_at')->value('slug');

$updated = \App\Models\NewsArticle::where('url', 'like', '%gia-vang-giam-manh%')
    ->update(['url' => '/tin-tuc-gia-vang/trong-nuoc/' . $newSlug]);

echo "Updated {$updated} news article(s) to point to: /tin-tuc-gia-vang/trong-nuoc/{$newSlug}\n";
