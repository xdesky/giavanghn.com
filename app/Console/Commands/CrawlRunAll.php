<?php

namespace App\Console\Commands;

use App\Models\CrawlLog;
use App\Services\Crawlers\BankRateCrawler;
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
use App\Services\Crawlers\SilverPriceCrawler;
use App\Services\Crawlers\SjcCrawler;
use App\Services\Crawlers\WorldGoldCrawler;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class CrawlRunAll extends Command
{
    protected $signature = 'crawl:run
                            {--source=all : Source to crawl (all|sjc|doji|pnj|...)}
                            {--threshold=20 : Minutes gap to trigger recovery}
                            {--skip-sync : Skip price history sync after crawl}
                            {--dry-run : Only check status, do not crawl}';

    protected $description = 'Check data gaps → recover if missing → crawl fresh → sync histories (all-in-one)';

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
        'silver' => SilverPriceCrawler::class,
        'bankrate' => BankRateCrawler::class,
    ];

    public function handle(): int
    {
        $threshold = (int) $this->option('threshold');
        $dryRun = (bool) $this->option('dry-run');
        $skipSync = (bool) $this->option('skip-sync');
        $sourceOpt = $this->option('source');

        $sources = $this->resolveSources($sourceOpt);
        if ($sources === null) {
            return self::FAILURE;
        }

        $this->info('╔══════════════════════════════════════╗');
        $this->info('║        GOLD PRICE CRAWL RUNNER       ║');
        $this->info('╚══════════════════════════════════════╝');
        $this->info('Time: ' . now()->format('Y-m-d H:i:s'));
        $this->newLine();

        // ── STEP 1: Check data gaps ──────────────────────────────
        $this->info('▸ Step 1/3: Checking data gaps...');

        $missed = [];
        $maxGapMinutes = 0;

        foreach ($sources as $source) {
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

                $gap = $minutesSince === PHP_INT_MAX ? '∞' : $minutesSince;

                $missed[$source] = [
                    'last_success_at' => $lastAt,
                    'gap_minutes' => $gap,
                ];

                if (is_numeric($gap) && $gap > $maxGapMinutes) {
                    $maxGapMinutes = (int) $gap;
                }
            }
        }

        if (empty($missed)) {
            $this->info('  ✓ All sources up to date.');
        } else {
            $this->warn('  ⚠ ' . count($missed) . ' source(s) have stale data:');
            $this->table(
                ['Source', 'Last Success', 'Gap (min)'],
                array_map(fn ($s, $m) => [$s, $m['last_success_at'], $m['gap_minutes']], array_keys($missed), $missed)
            );
        }

        if ($dryRun) {
            $this->newLine();
            $this->info('[Dry-run] No crawlers executed.');
            return self::SUCCESS;
        }

        // ── STEP 2: Recover + Fresh crawl ────────────────────────
        $this->newLine();
        $this->info('▸ Step 2/3: Crawling all sources...');
        if (!empty($missed)) {
            $this->info('  (includes recovery for stale sources)');
        }

        $results = [];
        $hasFailure = false;

        foreach ($sources as $source) {
            $wasMissed = isset($missed[$source]);
            $label = $wasMissed ? "↻ {$source}" : $source;

            $crawler = new ($this->crawlers[$source])();

            $start = hrtime(true);
            $crawler->execute();
            $durationMs = (int) ((hrtime(true) - $start) / 1_000_000);

            $log = CrawlLog::where('crawler', $source)
                ->orderByDesc('id')
                ->first();

            $status = $log?->status ?? 'unknown';
            $count = $log?->records_count ?? 0;

            $results[] = [
                $label,
                $status,
                $count,
                "{$durationMs}ms",
                $wasMissed ? $missed[$source]['gap_minutes'] . 'm gap' : '—',
            ];

            if ($status === 'success') {
                $this->info("  ✓ [{$label}] {$count} records ({$durationMs}ms)");
            } else {
                $this->error("  ✗ [{$label}] " . ($log?->error_message ?? 'Unknown error'));
                $hasFailure = true;
            }
        }

        $this->newLine();
        $this->table(['Source', 'Status', 'Records', 'Duration', 'Recovery'], $results);

        // ── STEP 3: Sync price histories ─────────────────────────
        $this->newLine();
        if ($skipSync) {
            $this->info('▸ Step 3/3: Sync skipped (--skip-sync).');
        } else {
            $syncHours = $maxGapMinutes > 0
                ? max(1, (int) ceil($maxGapMinutes / 60)) + 1
                : 24;

            $this->info("▸ Step 3/3: Syncing price_histories (last {$syncHours}h)...");
            Artisan::call('sync:price-histories', ['--hours' => $syncHours]);
            $this->info('  ✓ Price histories synced.');
        }

        // ── Summary ──────────────────────────────────────────────
        $this->newLine();
        $this->info('══ Done at ' . now()->format('H:i:s') . ' ══');

        if (!empty($missed)) {
            Log::info('[crawl:run] Recovered ' . count($missed) . ' stale source(s): ' . implode(', ', array_keys($missed)));
        }

        return $hasFailure ? self::FAILURE : self::SUCCESS;
    }

    private function resolveSources(string $sourceOpt): ?array
    {
        if ($sourceOpt === 'all') {
            return array_keys($this->crawlers);
        }

        if (!isset($this->crawlers[$sourceOpt])) {
            $this->error("Unknown source: {$sourceOpt}");
            $this->info('Available: ' . implode(', ', array_keys($this->crawlers)));
            return null;
        }

        return [$sourceOpt];
    }
}
