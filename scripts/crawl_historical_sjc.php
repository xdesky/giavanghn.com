<?php
/**
 * Crawl historical SJC daily prices from webgia.com for 2023-2024.
 *
 * Usage: php scripts/crawl_historical_sjc.php [--year=2023] [--year=2024] [--dry-run]
 *
 * Data source: https://webgia.com/gia-vang/sjc/{dd-mm-yyyy}.html
 * Output: sjc_chart_prices table (price_date, sell_million, buy_million, source='webgia')
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\SjcChartPrice;
use Illuminate\Support\Facades\Http;

// Parse CLI args
$args = getopt('', ['year:', 'dry-run']);
$years = [];
if (isset($args['year'])) {
    $years = is_array($args['year']) ? $args['year'] : [$args['year']];
} else {
    $years = [2023, 2024];
}
$dryRun = isset($args['dry-run']);

echo "=== Crawl Historical SJC Prices from webgia.com ===\n";
echo "Years: " . implode(', ', $years) . ($dryRun ? " [DRY RUN]\n" : "\n");
echo str_repeat('=', 50) . "\n\n";

$totalInserted = 0;
$totalSkipped = 0;
$totalNoData = 0;
$totalErrors = 0;

foreach ($years as $year) {
    $year = (int) $year;
    echo "--- Year {$year} ---\n";

    $startDate = new DateTime("{$year}-01-01");
    $endDate = new DateTime("{$year}-12-31");
    $yearInserted = 0;
    $yearSkipped = 0;

    $current = clone $startDate;
    while ($current <= $endDate) {
        $dateStr = $current->format('d-m-Y');
        $isoDate = $current->format('Y-m-d');
        $dayOfWeek = (int) $current->format('N'); // 1=Mon, 7=Sun

        // Skip Sundays (gold markets closed)
        if ($dayOfWeek === 7) {
            $current->modify('+1 day');
            continue;
        }

        // Check if already exists
        if (!$dryRun && SjcChartPrice::where('price_date', $isoDate)->exists()) {
            $yearSkipped++;
            $current->modify('+1 day');
            continue;
        }

        // Fetch from webgia
        $url = "https://webgia.com/gia-vang/sjc/{$dateStr}.html";
        try {
            $response = Http::timeout(10)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; GoldPriceBot/1.0)'])
                ->get($url);

            if (!$response->successful()) {
                $totalErrors++;
                $current->modify('+1 day');
                usleep(200000);
                continue;
            }

            $html = $response->body();

            // Parse first table (gold price table)
            if (!preg_match('/<table[^>]*>(.*?)<\/table>/si', $html, $tableMatch)) {
                $totalNoData++;
                $current->modify('+1 day');
                usleep(200000);
                continue;
            }

            // Extract all data rows (skip header, skip footer "Cập nhật" row)
            preg_match_all('/<tr[^>]*>(.*?)<\/tr>/si', $tableMatch[1], $rowMatches);

            $lastBuy = null;
            $lastSell = null;

            foreach ($rowMatches[1] as $row) {
                preg_match_all('/<td[^>]*>(.*?)<\/td>/si', $row, $cellMatches);
                $cells = array_map(fn($c) => trim(strip_tags($c)), $cellMatches[1]);

                // Data row format: [Lần, Thời gian, Mua vào, Bán ra]
                if (count($cells) >= 4) {
                    $buyVal = str_replace(',', '', $cells[2]);
                    $sellVal = str_replace(',', '', $cells[3]);
                    if (is_numeric($buyVal) && is_numeric($sellVal) && (float)$buyVal > 0 && (float)$sellVal > 0) {
                        $lastBuy = (float) $buyVal;
                        $lastSell = (float) $sellVal;
                    }
                }
            }

            if ($lastBuy === null || $lastSell === null) {
                $totalNoData++;
                $current->modify('+1 day');
                usleep(200000);
                continue;
            }

            if ($dryRun) {
                echo "  {$isoDate}: buy={$lastBuy} sell={$lastSell}\n";
            } else {
                SjcChartPrice::updateOrCreate(
                    ['price_date' => $isoDate],
                    [
                        'sell_million' => round($lastSell, 2),
                        'buy_million' => round($lastBuy, 2),
                        'source' => 'webgia',
                    ]
                );
            }
            $yearInserted++;

        } catch (\Throwable $e) {
            $totalErrors++;
            echo "  {$isoDate}: ERROR {$e->getMessage()}\n";
        }

        $current->modify('+1 day');
        usleep(300000); // 300ms delay between requests
    }

    echo "  Inserted: {$yearInserted}, Skipped (existing): {$yearSkipped}\n";
    $totalInserted += $yearInserted;
    $totalSkipped += $yearSkipped;
}

echo "\n" . str_repeat('=', 50) . "\n";
echo "TOTAL: inserted={$totalInserted} skipped={$totalSkipped} noData={$totalNoData} errors={$totalErrors}\n";

// Verify
if (!$dryRun) {
    foreach ($years as $year) {
        $count = SjcChartPrice::whereYear('price_date', $year)->count();
        $min = SjcChartPrice::whereYear('price_date', $year)->min('sell_million');
        $max = SjcChartPrice::whereYear('price_date', $year)->max('sell_million');
        echo "  {$year}: {$count} rows, sell range {$min} - {$max}\n";
    }
}
