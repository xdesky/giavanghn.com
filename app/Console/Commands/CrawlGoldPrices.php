<?php

namespace App\Console\Commands;

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

class CrawlGoldPrices extends Command
{
    protected $signature = 'crawl:gold
                            {--source=all : Source to crawl (all|sjc|doji|pnj|btmc|phuquy|mihong|baotinmanhhai|ngoctham|world|exchange|news|dailystats|sentiment)}
                            {--verbose-log : Show detailed log output}';

    protected $description = 'Crawl gold prices, exchange rates, and news from various sources';

    private array $crawlers;

    public function __construct()
    {
        parent::__construct();

        $this->crawlers = [
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
    }

    public function handle(): int
    {
        $source = $this->option('source');

        $this->info('=== Gold Price Crawler ===');
        $this->info('Started at: ' . now()->format('Y-m-d H:i:s'));
        $this->newLine();

        if ($source === 'all') {
            $sources = array_keys($this->crawlers);
        } else {
            if (!isset($this->crawlers[$source])) {
                $this->error("Unknown source: {$source}");
                $this->info('Available: ' . implode(', ', array_keys($this->crawlers)));
                return self::FAILURE;
            }
            $sources = [$source];
        }

        $results = [];

        foreach ($sources as $name) {
            $this->info("Crawling [{$name}]...");

            $crawler = new ($this->crawlers[$name])();

            $start = hrtime(true);
            $crawler->execute();
            $durationMs = (int) ((hrtime(true) - $start) / 1_000_000);

            // Get latest log
            $log = \App\Models\CrawlLog::where('crawler', $name)
                ->orderByDesc('id')
                ->first();

            $status = $log?->status ?? 'unknown';
            $count = $log?->records_count ?? 0;

            $results[] = [$name, $status, $count, "{$durationMs}ms"];

            if ($status === 'success') {
                $this->info("  ✓ {$count} records ({$durationMs}ms)");
            } else {
                $this->error("  ✗ Failed: " . ($log?->error_message ?? 'Unknown error'));
            }
        }

        $this->newLine();
        $this->table(['Source', 'Status', 'Records', 'Duration'], $results);

        $this->newLine();
        $this->info('Completed at: ' . now()->format('Y-m-d H:i:s'));

        return self::SUCCESS;
    }
}
