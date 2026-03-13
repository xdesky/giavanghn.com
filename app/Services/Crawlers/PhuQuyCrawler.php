<?php

namespace App\Services\Crawlers;

use App\Models\PhuquyGoldPrice;
use Carbon\Carbon;

class PhuQuyCrawler extends BaseCrawler
{
    public function crawlerName(): string
    {
        return 'phuquy';
    }

    public function run(): int
    {
        $html = $this->fetch('https://phuquygroup.vn/');

        return $this->parse($html);
    }

    /**
     * Fetch and parse prices for a specific date.
     * Returns the number of records saved.
     */
    public function runForDate(string $date): int
    {
        $html = $this->fetch("https://phuquygroup.vn/XemLai?date={$date}");

        return $this->parse($html, $date);
    }

    private function parse(string $html, ?string $dateOverride = null): int
    {
        $count = 0;

        // Find table with class m-auto text-center
        if (!preg_match('/<table[^>]*class="[^"]*m-auto text-center[^"]*"[^>]*>(.*?)<\/table>/si', $html, $tableMatch)) {
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

            // Look for buy-price and sell-price classes
            preg_match_all('/<td[^>]*>(.*?)<\/td>/si', $row, $cells);
            $cellValues = array_map(fn($c) => trim(strip_tags($c)), $cells[1] ?? []);
            $cellHtml = $cells[0] ?? [];

            if (count($cellValues) < 3) {
                continue;
            }

            $brand = html_entity_decode($cellValues[0], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            if (empty($brand) || preg_match('/^(loai|stt|#)/i', $brand)) {
                continue;
            }

            // Extract buy and sell prices from cells with specific classes
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

            // Fallback: use positional columns if classes not found
            if ($buy <= 0 && $sell <= 0 && count($cellValues) >= 3) {
                $buy = $this->parsePrice($cellValues[1]);
                $sell = $this->parsePrice($cellValues[2]);
            }

            if ($buy <= 0 && $sell <= 0) {
                continue;
            }

            $prev = PhuquyGoldPrice::where('brand', $brand)
                ->orderByDesc('id')
                ->first();

            if (!$dateOverride && $prev && $prev->buy_price === $buy && $prev->sell_price === $sell) {
                $prev->touch();
                continue;
            }

            $change = 0;
            if ($prev && $prev->sell_price > 0 && $sell > 0) {
                $change = round(($sell - $prev->sell_price) / $prev->sell_price * 100, 4);
            }

            $record = PhuquyGoldPrice::create([
                'brand' => $brand,
                'karat' => $this->detectKarat($brand),
                'buy_price' => $buy,
                'sell_price' => $sell,
                'currency' => 'VND',
                'change_percent' => $change,
            ]);

            // Override created_at for historical backfill
            if ($dateOverride) {
                $record->created_at = Carbon::parse($dateOverride)->setTime(16, 0, 0);
                $record->updated_at = Carbon::parse($dateOverride)->setTime(16, 0, 0);
                $record->saveQuietly();
            }

            $count++;
        }

        return $count;
    }

    private function parsePrice(string $raw): int
    {
        $cleaned = preg_replace('/[^\d]/', '', trim($raw));
        if (empty($cleaned)) {
            return 0;
        }
        return (int) $cleaned;
    }

    private function detectKarat(string $brand): string
    {
        if (preg_match('/9999/i', $brand)) return '9999';
        if (preg_match('/999(?!\d)/i', $brand)) return '999';
        if (preg_match('/24k/i', $brand)) return '24K';
        if (preg_match('/18k/i', $brand)) return '18K';
        if (preg_match('/14k/i', $brand)) return '14K';
        return '9999';
    }
}
