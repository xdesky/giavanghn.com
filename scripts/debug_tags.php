<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\AnalysisArticle;
use Illuminate\Support\Facades\DB;

$a = AnalysisArticle::whereNotNull('published_at')->first();
echo "Raw tags: " . $a->getRawOriginal('tags') . PHP_EOL;
echo "Cast tags type: " . gettype($a->tags) . PHP_EOL;
echo "Tags: " . json_encode($a->tags, JSON_UNESCAPED_UNICODE) . PHP_EOL;

// Test whereJsonContains
$count = AnalysisArticle::whereNotNull('published_at')
    ->whereJsonContains('tags', 'giá vàng')
    ->count();
echo "whereJsonContains('tags', 'giá vàng'): $count" . PHP_EOL;

// Test LIKE-based approach
$count2 = AnalysisArticle::whereNotNull('published_at')
    ->where('tags', 'like', '%giá vàng%')
    ->count();
echo "where LIKE '%giá vàng%': $count2" . PHP_EOL;

// Check MySQL version
$version = DB::selectOne("SELECT VERSION() as v")->v;
echo "MySQL version: $version" . PHP_EOL;

// Check column actual type
$colInfo = DB::selectOne("SHOW COLUMNS FROM analysis_articles WHERE Field = 'tags'");
echo "Column type: " . $colInfo->Type . PHP_EOL;
