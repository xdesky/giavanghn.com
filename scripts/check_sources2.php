<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Http;

// BTMC silver page
echo "=== BTMC silver page ===\n";
try {
    $html = Http::withOptions(['verify' => false])->timeout(15)
        ->withHeaders(['User-Agent' => 'Mozilla/5.0'])
        ->get('https://btmc.vn/gia-bac-hom-nay')->body();
    // Find silver price table
    if (preg_match_all('/<tr[^>]*>.*?<td[^>]*>(.*?)<\/td>.*?<td[^>]*>(.*?)<\/td>.*?<td[^>]*>(.*?)<\/td>/si', $html, $m, PREG_SET_ORDER)) {
        foreach (array_slice($m, 0, 10) as $row) {
            $brand = trim(strip_tags($row[1]));
            $buy = trim(strip_tags($row[2]));
            $sell = trim(strip_tags($row[3]));
            if (!empty($brand) && (is_numeric(str_replace([',', '.'], '', $buy)) || is_numeric(str_replace([',', '.'], '', $sell)))) {
                echo "  $brand | Buy: $buy | Sell: $sell\n";
            }
        }
    } else {
        echo "  No table found. Checking for prices...\n";
        // Try find section with bac
        if (preg_match('/<div[^>]*class="[^"]*price[^"]*"[^>]*>.*?bạc.*?<\/div>/si', $html, $m2)) {
            echo "  Found: " . substr(strip_tags($m2[0]), 0, 200) . "\n";
        }
    }
} catch (\Throwable $e) {
    echo "  Error: " . $e->getMessage() . "\n";
}

// Phu Quy - check for silver table
echo "\n=== Phu Quy silver ===\n";
try {
    $html = Http::withOptions(['verify' => false])->timeout(15)
        ->withHeaders(['User-Agent' => 'Mozilla/5.0'])
        ->get('https://phuquygroup.vn/')->body();
    
    // Find all tables
    preg_match_all('/<table[^>]*>(.*?)<\/table>/si', $html, $tables);
    foreach ($tables[1] as $idx => $table) {
        if (stripos($table, 'bạc') !== false || stripos($table, 'Bạc') !== false) {
            echo "  Table $idx contains 'Bạc':\n";
            preg_match_all('/<tr[^>]*>(.*?)<\/tr>/si', $table, $rows);
            foreach ($rows[1] as $row) {
                $cells = [];
                preg_match_all('/<t[hd][^>]*>(.*?)<\/t[hd]>/si', $row, $cs);
                foreach ($cs[1] as $c) $cells[] = trim(strip_tags($c));
                if (count($cells) >= 2) echo "    " . implode(' | ', $cells) . "\n";
            }
        }
    }
} catch (\Throwable $e) {
    echo "  Error: " . $e->getMessage() . "\n";
}

// Check VCB full XML for all banks
echo "\n=== VCB XML (all USD-related) ===\n";
try {
    $xml = Http::withOptions(['verify' => false])->timeout(15)
        ->get('https://portal.vietcombank.com.vn/Usercontrols/TVPortal.TyGia/pXML.aspx')->body();
    preg_match('/<Exrate\s+CurrencyCode="USD"[^>]*\/?>/i', $xml, $m);
    echo "  VCB: $m[0]\n";
} catch (\Throwable $e) {
    echo "  Error: " . $e->getMessage() . "\n";
}

// Check BIDV
echo "\n=== BIDV exchange rate ===\n";
try {
    $r = Http::withOptions(['verify' => false])->timeout(15)
        ->withHeaders(['User-Agent' => 'Mozilla/5.0'])
        ->get('https://www.bidv.com.vn/ServicesBIDV/ExchangeDetailServlet');
    $data = $r->json();
    if ($data) {
        foreach ($data as $item) {
            if (is_array($item) && isset($item['currency']) && strtoupper($item['currency']) === 'USD') {
                echo "  " . json_encode($item) . "\n";
                break;
            }
            if (is_array($item) && isset($item['nameEn']) && stripos($item['nameEn'], 'USD') !== false) {
                echo "  " . json_encode($item) . "\n";
                break;
            }
        }
        // Show first 2 items structure
        echo "  First item: " . json_encode(array_slice($data, 0, 1)) . "\n";
    }
} catch (\Throwable $e) {
    echo "  Error: " . $e->getMessage() . "\n";
}
