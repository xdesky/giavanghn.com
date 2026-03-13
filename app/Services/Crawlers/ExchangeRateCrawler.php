<?php

namespace App\Services\Crawlers;

use App\Models\ExchangeRate;

class ExchangeRateCrawler extends BaseCrawler
{
    public function crawlerName(): string
    {
        return 'exchange';
    }

    public function run(): int
    {
        $count = 0;

        // 1) Try exchangerate-api.com (free, no key required)
        $count += $this->crawlExchangeRateApi();

        // 2) Try Vietcombank for official VND rates
        $count += $this->crawlVietcombank();

        return $count;
    }

    private function crawlExchangeRateApi(): int
    {
        $count = 0;

        try {
            $data = $this->fetchJson('https://api.exchangerate-api.com/v4/latest/USD');
            $rates = $data['rates'] ?? [];

            if (isset($rates['VND'])) {
                $this->saveRate('USD/VND', $rates['VND'], 'exchangerate-api');
                $count++;
            }

            $pairs = ['EUR' => 'EUR/VND', 'GBP' => 'GBP/VND', 'JPY' => 'JPY/VND', 'CNY' => 'CNY/VND'];
            foreach ($pairs as $curr => $pair) {
                if (isset($rates[$curr], $rates['VND'])) {
                    $rateVnd = $rates['VND'] / $rates[$curr];
                    $this->saveRate($pair, round($rateVnd, 2), 'exchangerate-api');
                    $count++;
                }
            }
        } catch (\Throwable) {
            // ok
        }

        return $count;
    }

    private function crawlVietcombank(): int
    {
        $count = 0;

        try {
            // Vietcombank returns XML format
            $xml = $this->fetch('https://portal.vietcombank.com.vn/Usercontrols/TVPortal.TyGia/pXML.aspx');

            preg_match_all(
                '/<Exrate\s+CurrencyCode="([^"]+)"[^>]*Sell="([^"]*)"[^>]*\/?>/i',
                $xml,
                $matches,
                PREG_SET_ORDER
            );

            foreach ($matches as $m) {
                $curr = trim($m[1]);
                $sellStr = trim($m[2]);

                if (empty($curr) || empty($sellStr)) {
                    continue;
                }

                $sell = (float) str_replace(',', '', $sellStr);
                if ($sell <= 0) {
                    continue;
                }

                // Only save major currencies
                $majorCurrencies = ['USD', 'EUR', 'GBP', 'JPY', 'CNY', 'AUD', 'CAD', 'CHF', 'SGD', 'KRW'];
                if (!in_array($curr, $majorCurrencies)) {
                    continue;
                }

                $this->saveRate("{$curr}/VND", $sell, 'vcb');
                $count++;
            }
        } catch (\Throwable) {
            // ok
        }

        return $count;
    }

    private function saveRate(string $pair, float $rate, string $source): void
    {
        $prev = ExchangeRate::where('pair', $pair)->orderByDesc('id')->first();

        $change = 0;
        if ($prev && $prev->rate > 0) {
            $change = round(($rate - $prev->rate) / $prev->rate * 100, 4);
        }

        ExchangeRate::create([
            'pair' => $pair,
            'rate' => $rate,
            'change_percent' => $change,
            'source' => $source,
        ]);
    }
}
