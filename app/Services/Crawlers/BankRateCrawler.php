<?php

namespace App\Services\Crawlers;

use App\Models\BankRate;

class BankRateCrawler extends BaseCrawler
{
    public function crawlerName(): string
    {
        return 'bankrate';
    }

    public function run(): int
    {
        $count = 0;
        $count += $this->crawlVcb();

        return $count;
    }

    private function crawlVcb(): int
    {
        $xml = $this->fetch('https://portal.vietcombank.com.vn/Usercontrols/TVPortal.TyGia/pXML.aspx');

        preg_match_all(
            '/<Exrate\s+CurrencyCode="([^"]+)"[^>]*Buy="([^"]*)"[^>]*Transfer="([^"]*)"[^>]*Sell="([^"]*)"[^>]*\/?>/i',
            $xml,
            $matches,
            PREG_SET_ORDER
        );

        $count = 0;

        foreach ($matches as $m) {
            $currency = trim($m[1]);
            if ($currency !== 'USD') {
                continue;
            }

            $buy = (float) str_replace(',', '', trim($m[2]));
            $sell = (float) str_replace(',', '', trim($m[4]));

            if ($buy <= 0 || $sell <= 0) {
                continue;
            }

            $this->saveBankRate('VCB', $currency, $buy, $sell);
            $count++;
        }

        return $count;
    }

    private function saveBankRate(string $bank, string $currency, float $buy, float $sell): void
    {
        $prev = BankRate::where('bank', $bank)
            ->where('currency', $currency)
            ->orderByDesc('id')
            ->first();

        if ($prev && $prev->buy_rate == $buy && $prev->sell_rate == $sell) {
            $prev->touch();
            return;
        }

        BankRate::create([
            'bank' => $bank,
            'currency' => $currency,
            'buy_rate' => $buy,
            'sell_rate' => $sell,
        ]);
    }
}
