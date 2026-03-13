<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$a = \App\Models\AnalysisArticle::latest('published_at')->first();
echo "Article ID: " . $a->id . PHP_EOL;
echo "Thumbnail path: " . ($a->thumbnail_path ?? 'NULL') . PHP_EOL;
echo "GD available: " . (function_exists('imagecreatetruecolor') ? 'yes' : 'no') . PHP_EOL;

// Check if file exists on disk
if ($a->thumbnail_path) {
    $exists = \Illuminate\Support\Facades\Storage::disk('public')->exists($a->thumbnail_path);
    echo "File exists on disk: " . ($exists ? 'yes' : 'no') . PHP_EOL;
    echo "Full path: " . \Illuminate\Support\Facades\Storage::disk('public')->path($a->thumbnail_path) . PHP_EOL;
}

// Check symlink
$publicStorage = public_path('storage');
echo "Public storage symlink exists: " . (file_exists($publicStorage) ? 'yes' : 'no') . PHP_EOL;
echo "Is link: " . (is_link($publicStorage) ? 'yes' : 'no') . PHP_EOL;

// Check content has <img
$hasImg = str_contains($a->content, '<img');
echo "Content has <img> tag: " . ($hasImg ? 'yes' : 'no') . PHP_EOL;

if ($hasImg) {
    preg_match('/<img[^>]+src="([^"]+)"/', $a->content, $m);
    echo "Image src: " . ($m[1] ?? 'not found') . PHP_EOL;
}
