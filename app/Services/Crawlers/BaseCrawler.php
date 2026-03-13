<?php

namespace App\Services\Crawlers;

use App\Models\CrawlLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

abstract class BaseCrawler
{
    abstract public function crawlerName(): string;

    abstract public function run(): int;

    public function execute(): void
    {
        $start = hrtime(true);
        $status = 'success';
        $count = 0;
        $error = null;

        try {
            $count = $this->run();
        } catch (\Throwable $e) {
            $status = 'failed';
            $error = $e->getMessage();
            Log::error("[Crawler:{$this->crawlerName()}] {$error}");
        }

        $durationMs = (int) ((hrtime(true) - $start) / 1_000_000);

        CrawlLog::create([
            'crawler' => $this->crawlerName(),
            'status' => $status,
            'records_count' => $count,
            'error_message' => $error,
            'duration_ms' => $durationMs,
        ]);
    }

    protected function fetch(string $url, array $headers = []): string
    {
        $response = Http::withOptions(['verify' => false])
            ->timeout(30)
            ->withHeaders(array_merge([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language' => 'vi-VN,vi;q=0.9,en;q=0.8',
            ], $headers))
            ->get($url);

        $response->throw();

        return $response->body();
    }

    protected function fetchJson(string $url, array $headers = []): array
    {
        $response = Http::withOptions(['verify' => false])
            ->timeout(30)
            ->withHeaders(array_merge([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'Accept' => 'application/json',
            ], $headers))
            ->get($url);

        $response->throw();

        return $response->json() ?? [];
    }
}
