<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\AnalysisArticle;
use Illuminate\Support\Facades\DB;

$articles = AnalysisArticle::whereNotNull('published_at')->get();
echo "Total published: " . $articles->count() . PHP_EOL . PHP_EOL;

foreach ($articles->take(2) as $a) {
    $raw = DB::table('analysis_articles')->where('id', $a->id)->value('tags');
    echo "ID: {$a->id}" . PHP_EOL;
    echo "  Raw DB: {$raw}" . PHP_EOL;
    echo "  Cast: " . json_encode($a->tags, JSON_UNESCAPED_UNICODE) . PHP_EOL;
    echo PHP_EOL;
}

// Test different search approaches for "phân tích giá vàng"
$tagName = 'phân tích giá vàng';
$encoded = trim(json_encode($tagName, JSON_UNESCAPED_SLASHES), '"');
echo "json_encode search: {$encoded}" . PHP_EOL;

$count1 = AnalysisArticle::whereNotNull('published_at')
    ->where('tags', 'like', '%' . $encoded . '%')
    ->count();
echo "LIKE with encoded: {$count1}" . PHP_EOL;

// Fix: escape backslashes for LIKE
$escaped = str_replace('\\', '\\\\', $encoded);
echo "Escaped for LIKE: {$escaped}" . PHP_EOL;
$count4 = AnalysisArticle::whereNotNull('published_at')
    ->where('tags', 'like', '%' . $escaped . '%')
    ->count();
echo "LIKE with escaped: {$count4}" . PHP_EOL;

// Also test whereRaw JSON_CONTAINS
$count5 = AnalysisArticle::whereNotNull('published_at')
    ->whereRaw("JSON_CONTAINS(tags, ?)", [json_encode($tagName)])
    ->count();
echo "JSON_CONTAINS: {$count5}" . PHP_EOL;

echo PHP_EOL . "MySQL version: " . DB::selectOne('SELECT VERSION() as v')->v . PHP_EOL;
echo "Column type: " . DB::selectOne("SELECT DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='analysis_articles' AND COLUMN_NAME='tags'")->DATA_TYPE . PHP_EOL;

// Check MySQL version
$version = DB::selectOne("SELECT VERSION() as v")->v;
echo "MySQL version: $version" . PHP_EOL;

// Check column actual type
$colInfo = DB::selectOne("SHOW COLUMNS FROM analysis_articles WHERE Field = 'tags'");
echo "Column type: " . $colInfo->Type . PHP_EOL;
