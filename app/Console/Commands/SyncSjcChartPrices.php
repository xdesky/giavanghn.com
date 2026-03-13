<?php

namespace App\Console\Commands;

use App\Services\SjcChartPriceService;
use Illuminate\Console\Command;

class SyncSjcChartPrices extends Command
{
    protected $signature = 'sync:sjc-chart-prices
                            {--date= : Specific date Y-m-d}
                            {--backfill : Backfill from gold_prices historical records}
                            {--days=400 : Backfill range in days}
                            {--webgia : Fetch and sync from webgia.com}';

    protected $description = 'Sync SJC chart prices into dedicated chart table';

    public function handle(SjcChartPriceService $service): int
    {
        if ((bool) $this->option('webgia')) {
            $this->info('Fetching data from webgia.com...');
            $count = $service->syncFromWebgia('1-nam');
            $this->info("Webgia sync completed: {$count} rows inserted/updated.");
            return self::SUCCESS;
        }

        if ((bool) $this->option('backfill')) {
            $days = (int) $this->option('days');
            $count = $service->backfillFromGoldPrices($days);
            $this->info("Backfill completed: {$count} rows synced.");
            return self::SUCCESS;
        }

        $date = $this->option('date') ?: null;
        $row = $service->syncFromGoldPrices($date);

        if (!$row) {
            $this->warn('No SJC data found for selected date.');
            return self::SUCCESS;
        }

        $this->info("Synced {$row->price_date->toDateString()} | sell={$row->sell_million} | buy={$row->buy_million}");
        return self::SUCCESS;
    }
}
