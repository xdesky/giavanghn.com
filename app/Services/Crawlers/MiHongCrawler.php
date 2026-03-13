<?php

namespace App\Services\Crawlers;

use App\Models\MihongGoldPrice;
use Illuminate\Support\Facades\Http;

class MiHongCrawler extends BaseCrawler
{
    private const CODE_MAP = [
        'SJC' => ['name' => 'SJC', 'karat' => '9999'],
        '999' => ['name' => 'Vàng 999', 'karat' => '999'],
        '985' => ['name' => 'Vàng 985', 'karat' => '985'],
        '980' => ['name' => 'Vàng 980', 'karat' => '980'],
        '950' => ['name' => 'Vàng 950', 'karat' => '950'],
        '750' => ['name' => 'Vàng 18K (750)', 'karat' => '18K'],
        '680' => ['name' => 'Vàng 680', 'karat' => '680'],
        '610' => ['name' => 'Vàng 610', 'karat' => '610'],
        '580' => ['name' => 'Vàng 14K (580)', 'karat' => '14K'],
        '410' => ['name' => 'Vàng 10K (410)', 'karat' => '10K'],
    ];

    public function crawlerName(): string
    {
        return 'mihong';
    }

    public function run(): int
    {
        $response = Http::withOptions(['verify' => false])
            ->timeout(30)
            ->withHeaders([
                'x-market' => 'mihong',
                'Accept' => 'application/json',
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            ])
            ->get('https://api.mihong.vn/v1/gold-prices', [
                'market' => 'domestic',
            ]);

        if (!$response->successful()) {
            return 0;
        }

        $items = $response->json();

        if (!is_array($items)) {
            return 0;
        }

        $count = 0;

        foreach ($items as $item) {
            $code = $item['code'] ?? '';
            $buy = (int) ($item['buyingPrice'] ?? 0);
            $sell = (int) ($item['sellingPrice'] ?? 0);
            $buyChange = (float) ($item['buyChange'] ?? 0);
            $sellChange = (float) ($item['sellChange'] ?? 0);
            $sellChangePercent = (float) ($item['sellChangePercent'] ?? 0);

            if (empty($code) || ($buy <= 0 && $sell <= 0)) {
                continue;
            }

            $mapped = self::CODE_MAP[$code] ?? ['name' => $code, 'karat' => $code];

            $prev = MihongGoldPrice::where('code', $code)
                ->orderByDesc('id')
                ->first();

            if ($prev && $prev->buy_price === $buy && $prev->sell_price === $sell) {
                $prev->touch();
                continue;
            }

            MihongGoldPrice::create([
                'brand' => $mapped['name'],
                'code' => $code,
                'karat' => $mapped['karat'],
                'buy_price' => $buy,
                'sell_price' => $sell,
                'currency' => 'VND',
                'buy_change' => $buyChange,
                'sell_change' => $sellChange,
                'change_percent' => $sellChangePercent,
            ]);

            $count++;
        }

        return $count;
    }
}
