<?php

namespace App\Services\Crawlers;

use App\Models\BaotinmanhhaiGoldPrice;

class BaoTinManhHaiCrawler extends BaseCrawler
{
    public function crawlerName(): string
    {
        return 'baotinmanhhai';
    }

    public function run(): int
    {
        $html = $this->fetch('https://baotinmanhhai.vn/');

        return $this->parse($html);
    }

    private function parse(string $html): int
    {
        $count = 0;

        // Find gold price table with class gold-table-content
        if (!preg_match('/<table[^>]*class="[^"]*gold-table-content[^"]*"[^>]*>(.*?)<\/table>/si', $html, $tableMatch)) {
            return 0;
        }

        $tableHtml = $tableMatch[1];

        // Extract rows
        preg_match_all('/<tr[^>]*>(.*?)<\/tr>/si', $tableHtml, $rows);

        foreach ($rows[1] as $row) {
            // Skip header rows
            if (preg_match('/<th/i', $row)) {
                continue;
            }

            preg_match_all('/<td[^>]*>(.*?)<\/td>/si', $row, $cells);
            $cellValues = array_map(fn($c) => trim(strip_tags($c)), $cells[1] ?? []);

            // Expect 3 columns: LOẠI VÀNG, MUA VÀO, BÁN RA
            if (count($cellValues) < 3) {
                continue;
            }

            $brand = $cellValues[0];
            if (empty($brand) || preg_match('/^(loai|stt|#)/iu', $brand)) {
                continue;
            }

            $buy = $this->parsePrice($cellValues[1]);
            $sell = $this->parsePrice($cellValues[2]);

            if ($buy <= 0 && $sell <= 0) {
                continue;
            }

            $prev = BaotinmanhhaiGoldPrice::where('brand', $brand)
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

            BaotinmanhhaiGoldPrice::create([
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

    private function parsePrice(string $raw): int
    {
        // Prices formatted as 18.200.000 (dot-separated)
        $cleaned = preg_replace('/[^\d]/', '', trim($raw));
        if (empty($cleaned)) {
            return 0;
        }
        return (int) $cleaned;
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
