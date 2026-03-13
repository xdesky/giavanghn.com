<?php

namespace App\Services\Crawlers;

use App\Services\SentimentService;

class SentimentCrawler extends BaseCrawler
{
    public function crawlerName(): string
    {
        return 'sentiment';
    }

    public function run(): int
    {
        $service = new SentimentService();
        $sentiment = $service->calculate(now());

        return $sentiment->wasRecentlyCreated ? 1 : 0;
    }
}
