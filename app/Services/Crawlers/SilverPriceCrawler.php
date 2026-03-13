<?php

namespace App\Services\Crawlers;

use App\Models\SilverPrice;

class SilverPriceCrawler extends BaseCrawler
{
    public function crawlerName(): string
    {
        return 'silver';
    }

    public function run(): int
    {
        $count = 0;
        $count += $this->crawlPhuQuy();
        $count += $this->crawlBtmc();

        return $count;
    }

    private function crawlPhuQuy(): int
    {
        $html = $this->fetch('https://phuquygroup.vn/');
        $count = 0;

        if (!preg_match('/<table[^>]*class="[^"]*m-auto text-center[^"]*"[^>]*>(.*?)<\/table>/si', $html, $tableMatch)) {
            return 0;
        }

        preg_match_all('/<tr[^>]*>(.*?)<\/tr>/si', $tableMatch[1], $rows);

        foreach ($rows[1] as $row) {
            if (preg_match('/<th/i', $row)) {
                continue;
            }

            preg_match_all('/<td[^>]*>(.*?)<\/td>/si', $row, $cells);
            $cellValues = array_map(fn($c) => trim(strip_tags($c)), $cells[1] ?? []);
            $cellHtml = $cells[0] ?? [];

            if (count($cellValues) < 3) {
                continue;
            }

            $brand = html_entity_decode($cellValues[0], ENT_QUOTES | ENT_HTML5, 'UTF-8');

            // Only pick silver products (Bạc)
            if (mb_stripos($brand, 'Bạc') === false && mb_stripos($brand, 'bạc') === false) {
                continue;
            }

            $buy = 0;
            $sell = 0;

            foreach ($cellHtml as $td) {
                if (preg_match('/class="[^"]*buy-price[^"]*"/i', $td)) {
                    $buy = $this->parsePrice(strip_tags($td));
                }
                if (preg_match('/class="[^"]*sell-price[^"]*"/i', $td)) {
                    $sell = $this->parsePrice(strip_tags($td));
                }
            }

            if ($buy <= 0 && $sell <= 0 && count($cellValues) >= 3) {
                $buy = $this->parsePrice($cellValues[1]);
                $sell = $this->parsePrice($cellValues[2]);
            }

            if ($buy <= 0 && $sell <= 0) {
                continue;
            }

            $this->saveSilverPrice('phuquy', $brand, $buy, $sell);
            $count++;
        }

        return $count;
    }

    private function crawlBtmc(): int
    {
        try {
            $html = $this->fetch('https://btmc.vn/gia-bac-hom-nay');
        } catch (\Throwable) {
            return 0;
        }

        $count = 0;

        preg_match_all('/<table[^>]*>(.*?)<\/table>/si', $html, $tables);

        foreach ($tables[1] as $tableHtml) {
            preg_match_all('/<tr[^>]*>(.*?)<\/tr>/si', $tableHtml, $rows);

            foreach ($rows[1] as $row) {
                if (preg_match('/<th/i', $row)) {
                    continue;
                }

                preg_match_all('/<td[^>]*>(.*?)<\/td>/si', $row, $cells);
                $cellValues = array_map(fn($c) => trim(strip_tags($c)), $cells[1] ?? []);

                if (count($cellValues) < 3) {
                    continue;
                }

                $brand = html_entity_decode($cellValues[0], ENT_QUOTES | ENT_HTML5, 'UTF-8');

                if (empty($brand) || mb_stripos($brand, 'bạc') === false) {
                    continue;
                }

                $buy = $this->parsePrice($cellValues[1]);
                $sell = $this->parsePrice($cellValues[2]);

                if ($buy <= 0 && $sell <= 0) {
                    continue;
                }

                $this->saveSilverPrice('btmc', $brand, $buy, $sell);
                $count++;
            }
        }

        return $count;
    }

    private function saveSilverPrice(string $source, string $brand, int $buy, int $sell): void
    {
        $prev = SilverPrice::where('source', $source)
            ->where('brand', $brand)
            ->orderByDesc('id')
            ->first();

        if ($prev && $prev->buy_price === $buy && $prev->sell_price === $sell) {
            $prev->touch();
            return;
        }

        $change = 0;
        if ($prev && $prev->sell_price > 0 && $sell > 0) {
            $change = round(($sell - $prev->sell_price) / $prev->sell_price * 100, 4);
        }

        SilverPrice::create([
            'source' => $source,
            'brand' => $brand,
            'buy_price' => $buy,
            'sell_price' => $sell,
            'currency' => 'VND',
            'change_percent' => $change,
        ]);
    }

    private function parsePrice(string $raw): int
    {
        $cleaned = preg_replace('/[^\d]/', '', trim($raw));
        return $cleaned !== '' ? (int) $cleaned : 0;
    }
}
