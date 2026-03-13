<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Http;

// Check BTMC for silver keys
echo "=== BTMC API silver keys ===\n";
$date = now()->format('d/m/Y');
$r = Http::withOptions(['verify' => false])->timeout(15)
    ->withHeaders(['User-Agent' => 'Mozilla/5.0', 'X-Requested-With' => 'XMLHttpRequest', 'Referer' => 'https://btmc.vn/'])
    ->get("https://btmc.vn/ProductHome/getGoldDate?date={$date}");
$data = $r->json()['Data'] ?? [];
foreach ($data as $k => $v) {
    if (stripos($k, 'bac') !== false || stripos($k, 'silver') !== false) {
        echo "  $k: $v\n";
    }
}

// Show all keys from BTMC
echo "\n=== ALL BTMC keys ===\n";
foreach ($data as $k => $v) {
    echo "  $k: $v\n";
}

// Check Phu Quy homepage for silver
echo "\n=== Phu Quy silver sections ===\n";
$html = Http::withOptions(['verify' => false])->timeout(15)
    ->withHeaders(['User-Agent' => 'Mozilla/5.0'])
    ->get('https://phuquygroup.vn/')->body();
if (preg_match_all('/<h\d[^>]*>([^<]*(?:bạc|Bạc|BAC|silver)[^<]*)<\/h\d>/i', $html, $m)) {
    foreach ($m[0] as $match) echo "  $match\n";
}

// Check for silver table sections  
if (preg_match('/Bạc/i', $html)) {
    echo "  (Phú Quý page contains 'Bạc')\n";
}

// Check VCB bank rates XML
echo "\n=== VCB XML sample (USD) ===\n";
$xml = Http::withOptions(['verify' => false])->timeout(15)
    ->get('https://portal.vietcombank.com.vn/Usercontrols/TVPortal.TyGia/pXML.aspx')->body();
if (preg_match('/<Exrate\s+CurrencyCode="USD"[^>]*\/?>/i', $xml, $m)) {
    echo "  $m[0]\n";
}
