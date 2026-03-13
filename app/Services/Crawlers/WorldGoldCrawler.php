<?php

namespace App\Services\Crawlers;

use App\Models\WorldPrice;

class WorldGoldCrawler extends BaseCrawler
{
    public function crawlerName(): string
    {
        return 'world';
    }

    public function run(): int
    {
        $count = 0;

        // 1) XAU/USD from multiple sources
        $count += $this->crawlGoldApi();

        // 2) Other metals & DXY from exchange rate API
        $count += $this->crawlMetals();

        return $count;
    }

    private function crawlGoldApi(): int
    {
        $count = 0;

        try {
            // goldpricez.com homepage has current spot price
            $html = $this->fetch('https://www.goldpricez.com/');
            // Look for price pattern like "$5,171.83" or "5171.83"
            if (preg_match('/(?:Gold\s*Price|gold_price|price.*per.*ounce)[^$]*\$?\s*([0-9,]+\.?\d*)/i', $html, $m)) {
                $price = (float) str_replace(',', '', $m[1]);
                if ($price > 1000) {
                    $this->saveWorldPrice('XAU/USD', 'Vang The Gioi (Spot)', $price);
                    $count++;
                }
            }
            // Also try more generic number pattern near "gold" keyword
            if ($count === 0 && preg_match('/(?:per\s*ounce|\/oz)[^0-9]*([0-9,]+\.\d{2})/i', $html, $m)) {
                $price = (float) str_replace(',', '', $m[1]);
                if ($price > 1000) {
                    $this->saveWorldPrice('XAU/USD', 'Vang The Gioi (Spot)', $price);
                    $count++;
                }
            }
        } catch (\Throwable) {
            // fallback
        }

        return $count;
    }

    private function crawlMetals(): int
    {
        $count = 0;

        $symbols = [
            'XAG' => ['name' => 'Bac (XAG/USD)', 'symbol' => 'XAG/USD'],
        ];

        try {
            $data = $this->fetchJson('https://api.exchangerate-api.com/v4/latest/USD');
            $rates = $data['rates'] ?? [];

            // Calculate XAU in other currencies if we have XAU/USD base
            $latestXau = WorldPrice::where('symbol', 'XAU/USD')->orderByDesc('id')->first();
            if ($latestXau) {
                $xauUsd = $latestXau->price;

                $pairs = [
                    'EUR' => 'XAU/EUR',
                    'GBP' => 'XAU/GBP',
                    'CNY' => 'XAU/CNY',
                    'JPY' => 'XAU/JPY',
                ];

                foreach ($pairs as $curr => $symbol) {
                    if (isset($rates[$curr])) {
                        $price = round($xauUsd * $rates[$curr], 2);
                        $this->saveWorldPrice($symbol, "Vang ({$symbol})", $price, $curr);
                        $count++;
                    }
                }
            }
        } catch (\Throwable) {
            // ok
        }

        return $count;
    }

    private function saveWorldPrice(string $symbol, string $name, float $price, string $currency = 'USD'): void
    {
        $prev = WorldPrice::where('symbol', $symbol)->orderByDesc('id')->first();

        if ($prev && (float) $prev->price === $price) {
            $prev->touch();
            return;
        }

        $changePercent = 0;
        $changeAmount = 0;
        if ($prev && $prev->price > 0) {
            $changeAmount = round($price - $prev->price, 4);
            $changePercent = round(($price - $prev->price) / $prev->price * 100, 4);
        }

        WorldPrice::create([
            'symbol' => $symbol,
            'name' => $name,
            'price' => $price,
            'change_percent' => $changePercent,
            'change_amount' => $changeAmount,
            'currency' => $currency,
        ]);
    }
}
