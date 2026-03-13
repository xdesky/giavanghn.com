<?php

namespace App\Console\Commands;

use App\Services\GoldAnalysisArticleService;
use Illuminate\Console\Command;

class GenerateGoldAnalysisArticle extends Command
{
    protected $signature = 'generate:analysis-article
                            {--trigger=auto : auto|daily|change|summary}
                            {--force : Force generate article even if duplicate signature exists}';

    protected $description = 'Generate long-form gold market analysis article (daily, on price changes, or daily summary) and store it in database';

    public function handle(GoldAnalysisArticleService $service): int
    {
        $trigger = (string) $this->option('trigger');
        $force = (bool) $this->option('force');

        if (!in_array($trigger, ['auto', 'daily', 'change', 'summary'], true)) {
            $this->error('Invalid --trigger value. Use auto|daily|change|summary.');
            return self::FAILURE;
        }

        $created = [];

        if ($trigger === 'auto') {
            $daily = $service->generate('daily', null, $force);
            if ($daily) {
                $created[] = $daily;
                $this->info("[daily] Created article #{$daily->id}: {$daily->title}");
            } else {
                $this->line('[daily] No new article needed.');
            }

            $change = $service->generate('change', null, $force);
            if ($change) {
                $created[] = $change;
                $this->info("[change] Created article #{$change->id}: {$change->title}");
            } else {
                $this->line('[change] No new article needed.');
            }
        } else {
            $article = $service->generate($trigger, null, $force);
            if ($article) {
                $created[] = $article;
                $this->info("[{$trigger}] Created article #{$article->id}: {$article->title}");
            } else {
                $this->line("[{$trigger}] No new article needed.");
            }
        }

        if (empty($created)) {
            return self::SUCCESS;
        }

        foreach ($created as $article) {
            $this->line("- words={$article->word_count}, thumbnail={$article->thumbnail_path}");
        }

        return self::SUCCESS;
    }
}
