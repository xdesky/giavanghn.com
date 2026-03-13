<?php

namespace App\Services\Crawlers;

use App\Models\GoldPrice;

class SjcCrawler extends BaseCrawler
{
    public function crawlerName(): string
    {
        return 'sjc';
    }

    public function run(): int
    {
        $data = $this->fetchJson('https://sjc.com.vn/GoldPrice/Services/PriceService.ashx');

        if (empty($data['data']) || empty($data['success'])) {
            return 0;
        }

        $count = 0;

        foreach ($data['data'] as $item) {
            $typeName = $item['TypeName'] ?? '';
            $branchName = $item['BranchName'] ?? '';
            $buyValue = (int) ($item['BuyValue'] ?? 0);
            $sellValue = (int) ($item['SellValue'] ?? 0);

            if (empty($typeName) || ($buyValue <= 0 && $sellValue <= 0)) {
                continue;
            }

            $brand = trim($typeName);
            $karat = $this->detectKarat($brand);
            $region = $this->detectRegion($branchName);

            $prev = GoldPrice::where('source', 'sjc')
                ->where('brand', $brand)
                ->where('region', $region)
                ->orderByDesc('id')
                ->first();

            if ($prev && $prev->buy_price === $buyValue && $prev->sell_price === $sellValue) {
                $prev->touch();
                continue;
            }

            $change = 0;
            if ($prev && $prev->sell_price > 0 && $sellValue > 0) {
                $change = round(($sellValue - $prev->sell_price) / $prev->sell_price * 100, 4);
            }

            GoldPrice::create([
                'source' => 'sjc',
                'brand' => $brand,
                'karat' => $karat,
                'region' => $region,
                'buy_price' => $buyValue,
                'sell_price' => $sellValue,
                'currency' => 'VND',
                'change_percent' => $change,
            ]);

            $count++;
        }

        return $count;
    }

    private function detectKarat(string $brand): string
    {
        if (preg_match('/99,99|9999|99\.99/i', $brand)) return '9999';
        if (preg_match('/999|99\.9|99%/i', $brand)) return '999';
        if (preg_match('/75%/i', $brand)) return '18K';
        if (preg_match('/68%/i', $brand)) return '16K';
        if (preg_match('/61%/i', $brand)) return '14K';
        if (preg_match('/58,3|58\.3/i', $brand)) return '14K';
        if (preg_match('/41,7|41\.7/i', $brand)) return '10K';
        return '9999';
    }

    private function detectRegion(string $branch): string
    {
        $lower = mb_strtolower($branch);
        if (str_contains($lower, 'hồ chí minh') || str_contains($lower, 'hcm')) return 'tp_hcm';
        if (str_contains($lower, 'miền bắc') || str_contains($lower, 'hà nội')) return 'ha_noi';
        if (str_contains($lower, 'miền trung') || str_contains($lower, 'đà nẵng') || str_contains($lower, 'huế')) return 'da_nang';
        if (str_contains($lower, 'hạ long') || str_contains($lower, 'hải phòng')) return 'ha_noi';
        if (str_contains($lower, 'nha trang') || str_contains($lower, 'quảng ngãi')) return 'da_nang';
        if (str_contains($lower, 'biên hòa') || str_contains($lower, 'miền tây') || str_contains($lower, 'bạc liêu') || str_contains($lower, 'cà mau')) return 'tp_hcm';
        return 'tp_hcm';
    }
}
