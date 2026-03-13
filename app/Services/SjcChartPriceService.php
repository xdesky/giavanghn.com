<?php

namespace App\Services;

use App\Models\SjcChartPrice;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SjcChartPriceService
{
    /**
     * Sync one day of SJC data from gold_prices into chart table.
     * Takes the latest (last) price record of the day.
     */
    public function syncFromGoldPrices(?string $date = null): ?SjcChartPrice
    {
        $date = $date ?: now()->toDateString();

        $row = DB::table('gold_prices')
            ->where('source', 'sjc')
            ->where('brand', 'Vàng SJC 1L, 10L, 1KG')
            ->whereDate('created_at', $date)
            ->orderByDesc('created_at')
            ->first();

        if (!$row || !$row->sell_price || !$row->buy_price) {
            return null;
        }

        $sellMillion = round(((float) $row->sell_price) / 1_000_000, 2);
        $buyMillion = round(((float) $row->buy_price) / 1_000_000, 2);

        $existing = SjcChartPrice::where('price_date', $date)->first();

        if ($existing && $existing->sell_million == $sellMillion && $existing->buy_million == $buyMillion) {
            return $existing;
        }

        return SjcChartPrice::updateOrCreate(
            ['price_date' => $date],
            [
                'sell_million' => $sellMillion,
                'buy_million' => $buyMillion,
                'source' => 'sync',
            ]
        );
    }

    /**
     * Backfill chart table from historical SJC records.
     */
    public function backfillFromGoldPrices(int $days = 400): int
    {
        $rows = DB::table('gold_prices')
            ->selectRaw('DATE(created_at) as d, MAX(sell_price) as max_sell, MAX(buy_price) as max_buy')
            ->where('source', 'sjc')
            ->where('brand', 'Vàng SJC 0.5 chỉ, 1 chỉ, 2 chỉ')
            ->where('created_at', '>=', now()->subDays($days))
            ->groupByRaw('DATE(created_at)')
            ->orderBy('d')
            ->get();

        $count = 0;
        foreach ($rows as $row) {
            if (!$row->max_sell || !$row->max_buy) {
                continue;
            }

            SjcChartPrice::updateOrCreate(
                ['price_date' => $row->d],
                [
                    'sell_million' => round(((float) $row->max_sell) / 1_000_000, 2),
                    'buy_million' => round(((float) $row->max_buy) / 1_000_000, 2),
                    'source' => 'sync',
                ]
            );
            $count++;
        }

        return $count;
    }

    /**
     * Import chart data from arrays of [timestampMs, value].
     */
    public function importFromSeries(array $sellSeries, array $buySeries): int
    {
        $buyByDate = [];
        foreach ($buySeries as $point) {
            if (!is_array($point) || count($point) < 2) {
                continue;
            }
            $date = now()->setTimestamp((int) (((int) $point[0]) / 1000))->toDateString();
            $buyByDate[$date] = round((float) $point[1], 2);
        }

        $count = 0;
        foreach ($sellSeries as $point) {
            if (!is_array($point) || count($point) < 2) {
                continue;
            }

            $date = now()->setTimestamp((int) (((int) $point[0]) / 1000))->toDateString();
            $sell = round((float) $point[1], 2);
            $buy = $buyByDate[$date] ?? null;
            if ($buy === null) {
                continue;
            }

            SjcChartPrice::updateOrCreate(
                ['price_date' => $date],
                [
                    'sell_million' => $sell,
                    'buy_million' => $buy,
                    'source' => 'manual',
                ]
            );
            $count++;
        }

        return $count;
    }

    /**
     * Return amCharts-ready dataset for dashboard.
     */
    public function getChartData(int $days = 366): Collection
    {
        return SjcChartPrice::query()
            ->where('price_date', '>=', now()->subDays($days)->toDateString())
            ->orderBy('price_date')
            ->get(['price_date', 'sell_million', 'buy_million'])
            ->map(fn (SjcChartPrice $item) => [
                'date' => $item->price_date->toDateString(),
                'sell' => (float) $item->sell_million,
                'buy' => (float) $item->buy_million,
            ]);
    }

    /**
     * Fetch latest SJC chart data from webgia.com and sync into database.
     */
    public function syncFromWebgia(string $period = '1-nam'): int
    {
        $url = "https://webgia.com/gia-vang/sjc/bieu-do-{$period}.html";

        try {
            $response = Http::timeout(15)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; GoldPriceBot/1.0)'])
                ->get($url);

            if (!$response->successful()) {
                Log::warning("SjcChartPriceService: Failed to fetch {$url}, status={$response->status()}");
                return 0;
            }

            $html = $response->body();

            preg_match_all('/data:\s*\[((?:\[\d{10,13}\s*,\s*[\d.]+\][\s,]*)+)\]/', $html, $dataMatches);

            if (count($dataMatches[1]) < 2) {
                Log::warning('SjcChartPriceService: Could not parse 2 data series from webgia.com');
                return 0;
            }

            $sellSeries = $this->parseDataChunk($dataMatches[1][0]);
            $buySeries = $this->parseDataChunk($dataMatches[1][1]);

            if (empty($sellSeries) || empty($buySeries)) {
                return 0;
            }

            return $this->importFromSeries($sellSeries, $buySeries);
        } catch (\Throwable $e) {
            Log::error("SjcChartPriceService: syncFromWebgia error: {$e->getMessage()}");
            return 0;
        }
    }

    private function parseDataChunk(string $chunk): array
    {
        preg_match_all('/\[(\d{10,13})\s*,\s*(\d+(?:\.\d+)?)\]/', $chunk, $points, PREG_SET_ORDER);

        return array_map(
            fn ($p) => [(int) $p[1], (float) $p[2]],
            $points
        );
    }
}
