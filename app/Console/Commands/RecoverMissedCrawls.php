<?php

namespace App\Console\Commands;

use App\Models\CrawlLog;
use App\Services\Crawlers\BaoTinManhHaiCrawler;
use App\Services\Crawlers\BtmcCrawler;
use App\Services\Crawlers\DailyStatsCrawler;
use App\Services\Crawlers\DojiCrawler;
use App\Services\Crawlers\ExchangeRateCrawler;
use App\Services\Crawlers\MiHongCrawler;
use App\Services\Crawlers\NewsCrawler;
use App\Services\Crawlers\NgocThamCrawler;
use App\Services\Crawlers\PhuQuyCrawler;
use App\Services\Crawlers\PnjCrawler;
use App\Services\Crawlers\SentimentCrawler;
use App\Services\Crawlers\SjcCrawler;
use App\Services\Crawlers\WorldGoldCrawler;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class RecoverMissedCrawls extends Command
{
    protected $signature = 'crawl:recover
                            {--threshold=20 : Minutes since last successful crawl to consider as missed}
                            {--dry-run : Only report missed crawls without executing}';

    protected $description = 'Detect and recover missed crawls after server downtime';

    private array $crawlers = [
        'sjc' => SjcCrawler::class,
        'doji' => DojiCrawler::class,
        'pnj' => PnjCrawler::class,
        'btmc' => BtmcCrawler::class,
        'phuquy' => PhuQuyCrawler::class,
        'mihong' => MiHongCrawler::class,
        'baotinmanhhai' => BaoTinManhHaiCrawler::class,
        'ngoctham' => NgocThamCrawler::class,
        'world' => WorldGoldCrawler::class,
        'exchange' => ExchangeRateCrawler::class,
        'news' => NewsCrawler::class,
        'dailystats' => DailyStatsCrawler::class,
        'sentiment' => SentimentCrawler::class,
    ];

    public function handle(): int
    {
        $threshold = (int) $this->option('threshold');
        $dryRun = (bool) $this->option('dry-run');

        $this->info('=== Crawl Recovery Check ===');
        $this->info('Threshold: ' . $threshold . ' minutes');
        $this->info('Time now: ' . now()->format('Y-m-d H:i:s'));
        $this->newLine();

        $missed = [];
        $recovered = [];
        $failed = [];

        foreach (array_keys($this->crawlers) as $source) {
            $lastSuccess = CrawlLog::where('crawler', $source)
                ->where('status', 'success')
                ->orderByDesc('created_at')
                ->first();

            $minutesSince = $lastSuccess
                ? now()->diffInMinutes($lastSuccess->created_at)
                : PHP_INT_MAX;

            if ($minutesSince > $threshold) {
                $lastAt = $lastSuccess
                    ? $lastSuccess->created_at->format('Y-m-d H:i:s')
                    : 'never';

                $missed[] = [
                    'source' => $source,
                    'last_success_at' => $lastAt,
                    'gap_minutes' => $minutesSince === PHP_INT_MAX ? '∞' : $minutesSince,
                ];
            }
        }

        if (empty($missed)) {
            $this->info('✓ All crawlers are up to date. No recovery needed.');
            return self::SUCCESS;
        }

        $this->warn(count($missed) . ' crawler(s) need recovery:');
        $this->table(
            ['Source', 'Last Success', 'Gap (min)'],
            array_map(fn ($m) => [$m['source'], $m['last_success_at'], $m['gap_minutes']], $missed)
        );
        $this->newLine();

        if ($dryRun) {
            $this->info('[Dry-run] No crawlers executed.');
            return self::SUCCESS;
        }

        // Calculate max downtime gap for price history sync
        $maxGapMinutes = 0;

        foreach ($missed as $m) {
            $source = $m['source'];
            $this->info("Recovering [{$source}]...");

            try {
                $crawler = new ($this->crawlers[$source])();
                $start = hrtime(true);
                $crawler->execute();
                $durationMs = (int) ((hrtime(true) - $start) / 1_000_000);

                $log = CrawlLog::where('crawler', $source)
                    ->orderByDesc('id')
                    ->first();

                if ($log?->status === 'success') {
                    $this->info("  ✓ Recovered {$log->records_count} records ({$durationMs}ms)");
                    $recovered[] = $source;
                } else {
                    $this->error("  ✗ Failed: " . ($log?->error_message ?? 'Unknown'));
                    $failed[] = $source;
                }
            } catch (\Throwable $e) {
                $this->error("  ✗ Exception: {$e->getMessage()}");
                $failed[] = $source;
            }

            $gap = $m['gap_minutes'];
            if (is_numeric($gap) && $gap > $maxGapMinutes) {
                $maxGapMinutes = (int) $gap;
            }
        }

        // Sync price histories covering the full downtime window
        if ($maxGapMinutes > 0) {
            $syncHours = max(1, (int) ceil($maxGapMinutes / 60)) + 1;
            $this->newLine();
            $this->info("Syncing price_histories for last {$syncHours}h to cover downtime gap...");
            Artisan::call('sync:price-histories', ['--hours' => $syncHours]);
            $this->info('  ✓ Price histories synced.');
        }

        // Summary
        $this->newLine();
        $this->info('=== Recovery Summary ===');
        $this->info('Recovered: ' . count($recovered) . ' (' . implode(', ', $recovered) . ')');

        if (!empty($failed)) {
            $this->error('Failed: ' . count($failed) . ' (' . implode(', ', $failed) . ')');
        }

        Log::info('[CrawlRecover] Recovered: ' . implode(', ', $recovered)
            . ($failed ? ' | Failed: ' . implode(', ', $failed) : ''));

        return empty($failed) ? self::SUCCESS : self::FAILURE;
    }
}
