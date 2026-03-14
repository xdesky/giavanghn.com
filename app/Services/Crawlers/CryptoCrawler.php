<?php

namespace App\Services\Crawlers;

use App\Models\CryptoPrice;

class CryptoCrawler extends BaseCrawler
{
    private const COINS = [
        'bitcoin' => 'BTC',
        'ethereum' => 'ETH',
        'tether' => 'USDT',
        'binancecoin' => 'BNB',
        'ripple' => 'XRP',
    ];

    public function crawlerName(): string
    {
        return 'crypto';
    }

    public function run(): int
    {
        $ids = implode(',', array_keys(self::COINS));
        $url = "https://api.coingecko.com/api/v3/simple/price?ids={$ids}&vs_currencies=usd&include_24hr_change=true";

        $data = $this->fetchJson($url);

        $count = 0;

        foreach (self::COINS as $id => $symbol) {
            if (!isset($data[$id]['usd'])) {
                continue;
            }

            $price = (float) $data[$id]['usd'];
            $change = (float) ($data[$id]['usd_24h_change'] ?? 0);

            $prev = CryptoPrice::where('symbol', $symbol)
                ->orderByDesc('id')
                ->first();

            if ($prev && abs($prev->price - $price) < 0.01) {
                $prev->touch();
            } else {
                CryptoPrice::create([
                    'symbol' => $symbol,
                    'price' => $price,
                    'change_24h' => round($change, 2),
                ]);
            }

            $count++;
        }

        return $count;
    }
}
