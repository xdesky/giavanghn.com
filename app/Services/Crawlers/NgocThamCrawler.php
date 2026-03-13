<?php

namespace App\Services\Crawlers;

use App\Models\NgocthamGoldPrice;
use Illuminate\Support\Facades\Http;

class NgocThamCrawler extends BaseCrawler
{
    public function crawlerName(): string
    {
        return 'ngoctham';
    }

    public function run(): int
    {
        $response = Http::withOptions(['verify' => false])
            ->timeout(30)
            ->withHeaders([
                'Accept' => 'application/json',
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'X-Requested-With' => 'XMLHttpRequest',
                'Referer' => 'https://ngoctham.com/',
            ])
            ->get('https://ngoctham.com/ajax/proxy_banggia.php');

        if (!$response->successful()) {
            return 0;
        }

        $data = $response->json();
        $items = $data['chitiet'] ?? [];

        if (!is_array($items) || empty($items)) {
            return 0;
        }

        $count = 0;

        foreach ($items as $item) {
            $brand = $item['loaivang'] ?? '';
            $buy = (int) ($item['giamua'] ?? 0);
            $sell = (int) ($item['giaban'] ?? 0);

            if (empty($brand) || ($buy <= 0 && $sell <= 0)) {
                continue;
            }

            $prev = NgocthamGoldPrice::where('brand', $brand)
                ->orderByDesc('id')
                ->first();

            if ($prev && $prev->buy_price === $buy && $prev->sell_price === $sell) {
                $prev->touch();
                continue;
            }

            $change = 0;
            if ($prev && $prev->sell_price > 0 && $sell > 0) {
                $change = round(($sell - $prev->sell_price) / $prev->sell_price * 100, 4);
            }

            NgocthamGoldPrice::create([
                'brand' => $brand,
                'karat' => $this->detectKarat($brand),
                'buy_price' => $buy,
                'sell_price' => $sell,
                'currency' => 'VND',
                'change_percent' => $change,
            ]);

            $count++;
        }

        return $count;
    }

    private function detectKarat(string $brand): string
    {
        if (preg_match('/999\.9|9999/i', $brand)) return '9999';
        if (preg_match('/999(?!\.\d)/i', $brand)) return '999';
        if (preg_match('/24k/i', $brand)) return '24K';
        if (preg_match('/18k/i', $brand)) return '18K';
        if (preg_match('/14k/i', $brand)) return '14K';
        return '9999';
    }
}
