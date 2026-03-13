<?php

namespace App\Services\Crawlers;

use App\Models\PnjGoldPrice;
use Illuminate\Support\Facades\Http;

class PnjCrawler extends BaseCrawler
{
    private const ZONES = [
        '00' => 'Hồ Chí Minh',
        '07' => 'Cần Thơ',
        '11' => 'Hà Nội',
        '13' => 'Đà Nẵng',
        '14' => 'Tây Nguyên',
        '21' => 'Đông Nam Bộ',
    ];

    public function crawlerName(): string
    {
        return 'pnj';
    }

    public function run(): int
    {
        $count = 0;

        foreach (self::ZONES as $zone => $zoneName) {
            try {
                $response = Http::withOptions(['verify' => false])
                    ->timeout(30)
                    ->withHeaders([
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                        'Accept' => 'application/json, text/plain, */*',
                        'Origin' => 'https://www.pnj.com.vn',
                        'Referer' => 'https://www.pnj.com.vn/site/gia-vang',
                    ])
                    ->get("https://edge-api.pnj.io/ecom-frontend/v1/get-gold-price?zone={$zone}");

                if (!$response->successful()) {
                    continue;
                }

                $data = $response->json();
                $items = $data['data'] ?? [];

                if (!is_array($items)) {
                    continue;
                }

                foreach ($items as $item) {
                    $brand = $item['tensp'] ?? $item['name'] ?? '';
                    $buy = (int) ($item['giamua'] ?? 0);
                    $sell = (int) ($item['giaban'] ?? 0);

                    if (empty($brand) || ($buy <= 0 && $sell <= 0)) {
                        continue;
                    }

                    // PNJ prices are in nghìn/chỉ → convert to VNĐ/lượng (×10,000)
                    if ($buy > 0 && $buy < 100000) {
                        $buy *= 10000;
                    }
                    if ($sell > 0 && $sell < 100000) {
                        $sell *= 10000;
                    }

                    $prev = PnjGoldPrice::where('brand', $brand)
                        ->where('zone', $zone)
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

                    PnjGoldPrice::create([
                        'brand' => $brand,
                        'karat' => $this->detectKarat($brand),
                        'zone' => $zone,
                        'buy_price' => $buy,
                        'sell_price' => $sell,
                        'currency' => 'VND',
                        'change_percent' => $change,
                    ]);

                    $count++;
                }
            } catch (\Throwable $e) {
                continue;
            }
        }

        return $count;
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
