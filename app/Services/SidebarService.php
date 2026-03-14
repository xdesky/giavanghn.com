<?php

namespace App\Services;

use App\Models\BankRate;
use App\Models\BaotinmanhhaiGoldPrice;
use App\Models\BtmcGoldPrice;
use App\Models\CryptoPrice;
use App\Models\DojiGoldPrice;
use App\Models\ExchangeRate;
use App\Models\GoldPrice;
use App\Models\PnjGoldPrice;
use App\Models\SilverPrice;
use App\Models\WorldPrice;
use Illuminate\Support\Facades\Cache;

class SidebarService
{
    public function getData(): array
    {
        return Cache::remember('sidebar_data', 120, function () {
            return [
                'vangMieng' => $this->getVangMieng(),
                'vangNhan' => $this->getVangNhan(),
                'theGioi' => $this->getTheGioi(),
                'bac' => $this->getBac(),
                'crypto' => $this->getCrypto(),
                'tyGia' => $this->getTyGia(),
            ];
        });
    }

    private function getVangMieng(): array
    {
        $items = [];

        // SJC - stored per lượng
        $sjc = GoldPrice::where('brand', 'like', '%SJC 1L%')
            ->orderByDesc('id')->first();
        if ($sjc) {
            $items[] = $this->formatGold('SJC', $sjc->buy_price, $sjc->sell_price);
        }

        // DOJI - stored per chỉ
        $doji = DojiGoldPrice::where('brand', 'like', '%SJC%')
            ->where('brand', 'like', '%Bán Lẻ%')
            ->orderByDesc('id')->first();
        if ($doji) {
            $items[] = $this->formatGold('DOJI', $doji->buy_price, $doji->sell_price);
        }

        // PNJ - stored per chỉ
        $pnj = PnjGoldPrice::where('brand', 'like', '%miếng SJC%')
            ->orderByDesc('id')->first();
        if ($pnj) {
            $items[] = $this->formatGold('PNJ', $pnj->buy_price, $pnj->sell_price);
        }

        // BTMC - stored per lượng
        $btmc = BtmcGoldPrice::where('brand', 'like', '%Miếng SJC%')
            ->orderByDesc('id')->first();
        if ($btmc) {
            $items[] = $this->formatGold('BTMC', $btmc->buy_price, $btmc->sell_price);
        }

        // BTMH - stored per chỉ
        $btmh = BaotinmanhhaiGoldPrice::where('brand', 'like', '%miếng SJC%')
            ->orderByDesc('id')->first();
        if ($btmh) {
            $items[] = $this->formatGold('BTMH', $btmh->buy_price, $btmh->sell_price);
        }

        return $items;
    }

    private function getVangNhan(): array
    {
        $items = [];

        // SJC nhẫn - stored per lượng
        $sjc = GoldPrice::where('brand', 'like', '%nhẫn SJC 99,99%')
            ->where('brand', 'like', '%1 chỉ%')
            ->orderByDesc('id')->first();
        if ($sjc) {
            $items[] = $this->formatGold('SJC', $sjc->buy_price, $sjc->sell_price);
        }

        // DOJI nhẫn - stored per chỉ
        $doji = DojiGoldPrice::where('brand', 'like', '%Nhẫn Tròn 9999%')
            ->orderByDesc('id')->first();
        if ($doji) {
            $items[] = $this->formatGold('DOJI', $doji->buy_price, $doji->sell_price);
        }

        // PNJ nhẫn - stored per chỉ
        $pnj = PnjGoldPrice::where('brand', 'like', '%Nhẫn Trơn PNJ%')
            ->orderByDesc('id')->first();
        if ($pnj) {
            $items[] = $this->formatGold('PNJ', $pnj->buy_price, $pnj->sell_price);
        }

        // BTMC nhẫn - stored per lượng
        $btmc = BtmcGoldPrice::where('brand', 'like', '%Nhẫn Tròn Trơn%')
            ->orderByDesc('id')->first();
        if ($btmc) {
            $items[] = $this->formatGold('BTMC', $btmc->buy_price, $btmc->sell_price);
        }

        // BTMH nhẫn - stored per chỉ
        $btmh = BaotinmanhhaiGoldPrice::where('brand', 'like', '%Nhẫn tròn 999%')
            ->orderByDesc('id')->first();
        if ($btmh) {
            $items[] = $this->formatGold('BTMH', $btmh->buy_price, $btmh->sell_price);
        }

        return $items;
    }

    private function getTheGioi(): array
    {
        $items = [];

        $xau = WorldPrice::where('symbol', 'XAU/USD')->orderByDesc('id')->first();
        if ($xau) {
            $items[] = [
                'label' => 'XAU/USD',
                'price' => number_format($xau->price, 2, ',', '.'),
                'change' => $this->formatChange($xau->change_amount, $xau->change_percent),
                'up' => $xau->change_percent >= 0,
            ];
        }

        $xag = WorldPrice::where('symbol', 'XAG/USD')->orderByDesc('id')->first();
        if ($xag) {
            $items[] = [
                'label' => 'XAG/USD',
                'price' => number_format($xag->price, 2, ',', '.'),
                'change' => $this->formatChange($xag->change_amount, $xag->change_percent),
                'up' => $xag->change_percent >= 0,
            ];
        }

        $usd = ExchangeRate::where('pair', 'USD/VND')
            ->where('source', 'vcb')
            ->orderByDesc('id')->first();
        if ($usd) {
            $items[] = [
                'label' => 'USD/VND',
                'price' => number_format($usd->rate, 0, ',', '.'),
                'change' => '',
                'up' => true,
            ];
        }

        return $items;
    }

    private function getBac(): array
    {
        $items = [];

        $silvers = SilverPrice::latestByBrand()->get();
        foreach ($silvers as $s) {
            $label = match ($s->source) {
                'phuquy' => 'Phú Quý',
                'btmc' => 'BTMC',
                default => $s->source,
            };

            if (mb_stripos($s->brand, 'thỏi') !== false) {
                $label .= ' (thỏi)';
            } elseif (mb_stripos($s->brand, 'mỹ nghệ') !== false) {
                $label .= ' (mỹ nghệ)';
            }

            // Silver prices from Phú Quý are per chỉ → ×10 for per lượng
            // Display as ×1.000đ/lượng: price_per_luong / 1000
            $buyLuong = $s->buy_price * 10;
            $sellLuong = $s->sell_price * 10;

            $items[] = [
                'label' => $label,
                'buy' => $buyLuong > 0 ? number_format($buyLuong / 1000, 0, ',', '.') : '-',
                'sell' => $sellLuong > 0 ? number_format($sellLuong / 1000, 0, ',', '.') : '-',
            ];
        }

        return $items;
    }

    private function getTyGia(): array
    {
        $items = [];

        $banks = BankRate::latestByBank()->where('currency', 'USD')->get();
        foreach ($banks as $b) {
            $items[] = [
                'label' => $b->bank,
                'buy' => number_format($b->buy_rate, 0, ',', '.'),
                'sell' => number_format($b->sell_rate, 0, ',', '.'),
            ];
        }

        return $items;
    }

    /**
     * Normalize gold price to triệu đồng/lượng.
     * If the reference price < 100,000,000, it's stored per chỉ → ×10.
     */
    private function formatGold(string $label, int $buy, int $sell): array
    {
        $ref = max($buy, $sell);
        if ($ref > 0 && $ref < 100_000_000) {
            $buy *= 10;
            $sell *= 10;
        }

        return [
            'label' => $label,
            'buy' => $buy > 0 ? number_format($buy / 1_000_000, 1, ',', '.') : '-',
            'sell' => $sell > 0 ? number_format($sell / 1_000_000, 1, ',', '.') : '-',
        ];
    }

    private function getCrypto(): array
    {
        $order = ['BTC', 'ETH', 'USDT', 'BNB', 'XRP'];
        $items = [];

        $cryptos = CryptoPrice::latestBySymbol()->get()->keyBy('symbol');

        foreach ($order as $symbol) {
            $c = $cryptos[$symbol] ?? null;
            if (!$c) {
                continue;
            }

            $items[] = [
                'symbol' => $symbol,
                'price' => '$' . number_format($c->price, 2, ',', '.'),
                'change' => round($c->change_24h, 2),
            ];
        }

        return $items;
    }

    private function formatChange(float $amount, float $percent): string
    {
        $sign = $amount >= 0 ? '+' : '';
        return $sign . number_format($amount, 2, ',', '.') . ' (' . $sign . number_format($percent, 2, ',', '.') . '%)';
    }
}
