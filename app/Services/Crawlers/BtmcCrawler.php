<?php

namespace App\Services\Crawlers;

use App\Models\BtmcGoldPrice;

class BtmcCrawler extends BaseCrawler
{
    // API field key => [brand, product_type, karat, buyKey, sellKey]
    private const PRODUCTS = [
        ['Vàng Miếng VRTL Bảo Tín Minh Châu', 'Vàng miếng', '9999', 'btmcvangmiengmua', 'btmcvangmiengban'],
        ['Nhẫn Tròn Trơn Bảo Tín Minh Châu', 'Nhẫn tròn trơn', '9999', 'btmcvangnhanmua', 'btmcvangnhanban'],
        ['Quà Mừng Bản Vị Vàng Bảo Tín Minh Châu', 'Quà mừng', '9999', 'btmcvangquamungmua', 'btmcvangquamungban'],
        ['Vàng Miếng SJC', 'Vàng miếng SJC', '9999', 'sjcmua', 'sjcban'],
        ['Trang Sức Vàng Rồng Thăng Long 999.9', 'Trang sức VRTL', '9999', 'trangsucmua', 'trangsucban'],
        ['Trang Sức Vàng Rồng Thăng Long 99.9', 'Trang sức VRTL', '999', 'trangsucmua1', 'trangsucban1'],
        ['Vàng Nguyên Liệu', 'Nguyên liệu', '9999', 'vangnguyenlieumua', 'vangnguyenlieuban'],
    ];

    public function crawlerName(): string
    {
        return 'btmc';
    }

    public function run(): int
    {
        $date = now()->format('d/m/Y');
        $url = 'https://btmc.vn/ProductHome/getGoldDate?date=' . urlencode($date);
        $json = $this->fetchJson($url, [
            'X-Requested-With' => 'XMLHttpRequest',
            'Referer' => 'https://btmc.vn/',
        ]);

        $data = $json['Data'] ?? [];
        if (empty($data)) {
            return 0;
        }

        $count = 0;

        foreach (self::PRODUCTS as [$brand, $productType, $karat, $buyKey, $sellKey]) {
            $buyRaw = $this->extractPrice($data[$buyKey] ?? '');
            $sellRaw = $this->extractPrice($data[$sellKey] ?? '');

            if ($buyRaw <= 0 && $sellRaw <= 0) {
                continue;
            }

            // Prices are in nghìn/chỉ → convert to VNĐ/lượng (×10,000)
            $buy = $buyRaw > 0 ? $buyRaw * 10000 : 0;
            $sell = $sellRaw > 0 ? $sellRaw * 10000 : 0;

            $prev = BtmcGoldPrice::where('brand', $brand)
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

            BtmcGoldPrice::create([
                'brand' => $brand,
                'product_type' => $productType,
                'karat' => $karat,
                'buy_price' => $buy,
                'sell_price' => $sell,
                'currency' => 'VND',
                'change_percent' => $change,
            ]);

            $count++;
        }

        return $count;
    }

    private function extractPrice(string $raw): int
    {
        $cleaned = preg_replace('/[^\d]/', '', strip_tags($raw));
        return $cleaned !== '' ? (int) $cleaned : 0;
    }
}
