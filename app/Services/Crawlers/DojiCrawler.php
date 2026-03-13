<?php

namespace App\Services\Crawlers;

use App\Models\DojiGoldPrice;

class DojiCrawler extends BaseCrawler
{
    private const URL = 'https://giavang.doji.vn/';

    private const CATEGORY_MAP = [
        'Giá vàng trong nước' => 'trong_nuoc',
        'Giá vàng nữ trang 24K' => 'nu_trang_24k',
        'Giá vàng nữ trang 18K' => 'nu_trang_18k',
        'Giá vàng nữ trang 14K' => 'nu_trang_14k',
    ];

    public function crawlerName(): string
    {
        return 'doji';
    }

    public function run(): int
    {
        $html = $this->fetch(self::URL);

        preg_match_all(
            '/<table[^>]*class="goldprice-view[^"]*"[^>]*>([\s\S]*?)<\/table>/i',
            $html,
            $tables
        );

        if (empty($tables[0])) {
            return 0;
        }

        $count = 0;

        foreach ($tables[1] as $tableHtml) {
            $category = 'trong_nuoc';
            if (preg_match('/class="table_title"[^>]*>(.*?)<\/div>/i', $tableHtml, $titleMatch)) {
                $title = trim(strip_tags($titleMatch[1]));
                $category = self::CATEGORY_MAP[$title] ?? 'khac';
            }

            preg_match_all(
                '/<tr[^>]*>\s*<td[^>]*>([\s\S]*?)<\/td>\s*<td[^>]*>([\s\S]*?)<\/td>\s*<td[^>]*>([\s\S]*?)<\/td>\s*<\/tr>/i',
                $tableHtml,
                $rows,
                PREG_SET_ORDER
            );

            foreach ($rows as $row) {
                $brand = $this->cleanText($row[1]);
                $buy = $this->parsePrice($row[2]);
                $sell = $this->parsePrice($row[3]);

                if (empty($brand) || ($buy <= 0 && $sell <= 0)) {
                    continue;
                }

                $prev = DojiGoldPrice::where('brand', $brand)
                    ->where('category', $category)
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

                DojiGoldPrice::create([
                    'brand' => $brand,
                    'karat' => $this->detectKarat($brand, $category),
                    'category' => $category,
                    'buy_price' => $buy,
                    'sell_price' => $sell,
                    'currency' => 'VND',
                    'change_percent' => $change,
                ]);

                $count++;
            }
        }

        return $count;
    }

    private function cleanText(string $html): string
    {
        if (preg_match('/class="title[^"]*"[^>]*>(.*?)<\/span>/i', $html, $m)) {
            return trim(strip_tags($m[1]));
        }
        return trim(preg_replace('/\s+/', ' ', strip_tags($html)));
    }

    private function parsePrice(string $html): int
    {
        $text = strip_tags($html);
        $text = preg_replace('/[^\d.,]/', '', $text);
        if (empty($text)) {
            return 0;
        }
        $text = str_replace('.', '', $text);
        $text = str_replace(',', '', $text);
        $val = (int) $text;
        // DOJI prices are in nghìn/chỉ (thousands per chi), multiply by 1000
        if ($val > 0 && $val < 100000) {
            $val *= 1000;
        }
        return $val;
    }

    private function detectKarat(string $brand, string $category): string
    {
        if (str_contains($category, '18k')) return '18K';
        if (str_contains($category, '14k')) return '14K';
        if (preg_match('/99[,.]?99|9999/i', $brand)) return '9999';
        if (preg_match('/999(?!\d)/i', $brand)) return '999';
        if (preg_match('/99[,.]?9/i', $brand)) return '999';
        if (preg_match('/75%|18k/i', $brand)) return '18K';
        if (preg_match('/58[,.]?3|14k/i', $brand)) return '14K';
        return '9999';
    }
}
