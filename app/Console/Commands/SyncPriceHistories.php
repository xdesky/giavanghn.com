<?php

namespace App\Console\Commands;

use App\Models\GoldPrice;
use App\Models\PriceHistory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncPriceHistories extends Command
{
    protected $signature = 'sync:price-histories
                            {--hours=24 : Number of hours to sync back}';

    protected $description = 'Sync gold_prices → price_histories (1h candles) for domestic gold sources';

    /** Map gold_prices.source → [symbol, karat filter, brand filter] */
    private array $symbolMap = [
        'sjc' => ['symbol' => 'sjc', 'karat' => '9999', 'brand' => 'Vàng SJC 0.5 chỉ, 1 chỉ, 2 chỉ'],
        'btmc' => ['symbol' => 'v24k', 'karat' => '9999', 'brand' => null],
        'pnj' => ['symbol' => 'v18k', 'karat' => '9999', 'brand' => null],
    ];

    public function handle(): int
    {
        $hours = (int) $this->option('hours');
        $since = now()->subHours($hours)->startOfHour();

        $this->info("Syncing price_histories from gold_prices (last {$hours}h, since {$since})");

        $total = 0;

        foreach ($this->symbolMap as $source => $cfg) {
            $count = $this->syncSource($source, $cfg['symbol'], $cfg['karat'], $since, $cfg['brand']);
            $total += $count;
            $this->info("  [{$source} → {$cfg['symbol']}] {$count} candle(s) upserted");
        }

        $this->info("Done. Total: {$total} candle(s).");

        return self::SUCCESS;
    }

    private function syncSource(string $source, string $symbol, string $karat, $since, ?string $brand = null): int
    {
        // Group gold_prices by hour (updated_at), get OHLC — filter by karat to exclude jewelry
        // Use updated_at because SJC crawler updates records in-place;
        // the current sell/buy values correspond to the updated_at timestamp.
        $query = DB::table('gold_prices')
            ->select(DB::raw("
                DATE_FORMAT(updated_at, '%Y-%m-%d %H:00:00') as hour_slot,
                SUBSTRING_INDEX(GROUP_CONCAT(sell_price ORDER BY updated_at ASC), ',', 1) + 0 as open_price,
                MAX(sell_price) as high_price,
                MIN(sell_price) as low_price,
                SUBSTRING_INDEX(GROUP_CONCAT(sell_price ORDER BY updated_at DESC), ',', 1) + 0 as close_price,
                SUBSTRING_INDEX(GROUP_CONCAT(buy_price ORDER BY updated_at ASC), ',', 1) + 0 as open_buy,
                COUNT(*) as vol
            "))
            ->where('source', $source)
            ->where('karat', $karat)
            ->where('updated_at', '>=', $since)
            ->where('sell_price', '>', 0);

        if ($brand) {
            $query->where('brand', $brand);
        }

        $rows = $query->groupBy(DB::raw("DATE_FORMAT(updated_at, '%Y-%m-%d %H:00:00')"))
            ->orderBy('hour_slot')
            ->get();

        $count = 0;

        foreach ($rows as $row) {
            // Validate hour_slot is a clean hourly timestamp
            if (!preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:00:00$/', $row->hour_slot)) {
                continue;
            }

            DB::table('price_histories')->upsert(
                [
                    'symbol' => $symbol,
                    'period' => '1h',
                    'period_at' => $row->hour_slot,
                    'open' => $row->open_buy,
                    'high' => $row->high_price,
                    'low' => $row->low_price,
                    'close' => $row->close_price,
                    'volume' => $row->vol,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                ['symbol', 'period', 'period_at'],
                ['open', 'high', 'low', 'close', 'volume', 'updated_at']
            );
            $count++;
        }

        return $count;
    }
}
