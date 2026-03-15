<?php

namespace App\Services;

use App\Models\AnalysisArticle;
use App\Models\BaotinmanhhaiGoldPrice;
use App\Models\BtmcGoldPrice;
use App\Models\DailyStat;
use App\Models\DojiGoldPrice;
use App\Models\ExchangeRate;
use App\Models\GoldPrice;
use App\Models\MacroIndicator;
use App\Models\MarketSentiment;
use App\Models\MihongGoldPrice;
use App\Models\NewsArticle;
use App\Models\NgocthamGoldPrice;
use App\Models\PhuquyGoldPrice;
use App\Models\PnjGoldPrice;
use App\Models\PriceHistory;
use App\Models\SjcChartPrice;
use App\Models\WorldPrice;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    public function buildSnapshot(): array
    {
        return Cache::remember('dashboard_snapshot', 120, function () {
            return $this->buildSnapshotData();
        });
    }

    private function buildSnapshotData(): array
    {
        $goldPrices = GoldPrice::latestByBrand()->get();
        $worldPrices = WorldPrice::latestBySymbol()->get();
        $exchangeRates = ExchangeRate::latestByPair()->get();
        $macroIndicators = MacroIndicator::latestByIndicator()->get();
        $news = NewsArticle::goldRelated()
            ->where('source', 'vnexpress')
            ->orderByDesc('published_at')->limit(6)->get();
        $analysisArticles = AnalysisArticle::whereNotNull('published_at')
            ->orderByDesc('published_at')->limit(2)->get();

        $hasData = $goldPrices->isNotEmpty() || $worldPrices->isNotEmpty();

        if (!$hasData) {
            return $this->fallbackSnapshot();
        }

        return [
            'usCard' => $this->buildUsCard($worldPrices),
            'sjcCard' => $this->buildSjcCard($goldPrices),
            'btmcCard' => $this->buildBtmcCard(),
            'pnjCard' => $this->buildPnjCard(),
            'dojiCard' => $this->buildDojiCard(),
            'phuquyCard' => $this->buildPhuquyCard(),
            'mihongCard' => $this->buildMihongCard(),
            'btmhCard' => $this->buildBtmhCard(),
            'ngocthamCard' => $this->buildNgocthamCard(),
            'statCards' => $this->buildStatCards($goldPrices, $exchangeRates, $worldPrices),
            'sentiment' => $this->buildSentiment($goldPrices),
            'technical' => $this->buildTechnical($goldPrices),
            'actions' => [
                ['label' => 'Xem phân tích chi tiết', 'url' => '/tin-tuc-gia-vang/trong-nuoc'],
                ['label' => 'Công cụ quy đổi', 'url' => '/so-sanh-gia-vang/sjc-vs-the-gioi'],
                ['label' => 'Dự báo giá vàng', 'url' => '/du-bao-gia-vang'],
                ['label' => 'Thiết lập cảnh báo', 'url' => '#subscribeDialog'],
            ],
            'topBrands' => $this->buildTopBrands($goldPrices),
            'sjcYearlyChart' => $this->buildSjcYearlyChart(),
            'chart24h' => $this->buildChart24h(),
            'priceFeed' => $this->buildPriceFeed(),
            'news' => $this->buildNews($news, $analysisArticles),
            'comparisons' => $this->buildComparisons($goldPrices, $worldPrices, $exchangeRates),
            'performance' => $this->buildPerformance(),
            'movers' => $this->buildMovers($goldPrices),
            'knowledge' => [
                ['title' => 'Vàng 9999 là gì?', 'desc' => 'Tìm hiểu về vàng 24K nguyên chất', 'url' => '/gia-vang-hom-nay'],
                ['title' => 'Vàng SJC là gì?', 'desc' => 'Thương hiệu vàng quốc gia Việt Nam', 'url' => '/gia-vang-hom-nay/gia-vang-sjc'],
                ['title' => 'Nên mua vàng nào?', 'desc' => 'So sánh SJC, DOJI, PNJ cho người mới', 'url' => '/so-sanh-gia-vang'],
                ['title' => 'Cách đầu tư vàng', 'desc' => 'Chiến lược đầu tư hiệu quả 2026', 'url' => '/du-bao-gia-vang'],
            ],
            'globalMarkets' => $this->buildGlobalMarkets($worldPrices),
            'worldPriceDetail' => $this->buildWorldPriceDetail(),
            'supports' => $this->buildSupports($goldPrices),
            'centralBanks' => $this->buildCentralBanks(),
            'macroFactors' => $this->buildMacroFactors($macroIndicators),
            'forecast' => $this->buildForecast($goldPrices),
            'correlations' => $this->buildCorrelations(),
            'analystOpinion' => $this->buildAnalystOpinion($goldPrices, $worldPrices, $exchangeRates, $macroIndicators),
            'footer' => [
                'about' => ['Giới thiệu', 'Liên hệ', 'API'],
                'market' => ['Giá xăng', 'Tỷ giá ngoại tệ', 'Giá bạc', 'Giá kim loại'],
                'knowledge' => ['Vàng 9999 là gì?', 'Vàng SJC là gì?', 'Nên mua vàng nào?', 'Cách đầu tư vàng'],
                'history' => ['Giá vàng 2026', 'Giá vàng 2025', 'Giá vàng 2024', 'Giá vàng 2023'],
            ],
        ];
    }

    private function buildUsCard($worldPrices): array
    {
        $xau = $worldPrices->firstWhere('symbol', 'XAU/USD');
        $usBase = $xau ? $xau->price : 2918.5;
        $changePercent = $xau ? $xau->change_percent : 0.42;

        // Get 7 day historical prices from world_prices
        $weekData = \DB::table('world_prices')
            ->selectRaw('DATE(created_at) as d, MAX(price) as p')
            ->where('symbol', 'XAU/USD')
            ->groupByRaw('DATE(created_at)')
            ->orderByDesc('d')
            ->limit(30)
            ->pluck('p', 'd')
            ->sortKeys();

        $weekDates = $weekData->keys()->map(fn($d) => \Carbon\Carbon::parse($d)->format('d/m'))->toArray();
        $weekPrices = $weekData->values()->map(fn($v) => round((float) $v, 2))->toArray();

        // 24h intraday prices (fallback to last 30 records if 24h window is sparse)
        $intraday = \DB::table('world_prices')
            ->where('symbol', 'XAU/USD')
            ->where('created_at', '>=', now()->subHours(24))
            ->orderBy('created_at')
            ->get(['price', 'created_at']);

        if ($intraday->count() < 3) {
            $intraday = \DB::table('world_prices')
                ->where('symbol', 'XAU/USD')
                ->where('price', '>', 0)
                ->orderByDesc('created_at')
                ->limit(30)
                ->get(['price', 'created_at'])
                ->sortBy('created_at')
                ->values();
        }

        $chart24hPoints = $intraday->map(fn($r) => round((float) $r->price, 2))->toArray();
        $chart24hLabels = $intraday->map(function ($r) {
            $at = \Carbon\Carbon::parse($r->created_at);
            return $at->isToday() ? $at->format('H:i') : $at->format('H:i d/m');
        })->toArray();

        if (empty($chart24hPoints)) {
            $chart24hPoints = [$usBase];
            $chart24hLabels = [now()->format('H:i')];
        }

        return [
            'title' => 'Giá Vàng Thế Giới (XAU/USD)',
            'trendPercent' => round($changePercent, 2),
            'variants' => [
                'spot' => [
                    'label' => 'Giá Spot',
                    'price' => $usBase,
                    'unit' => 'USD/Ounce',
                    'dayChangeLabel' => ($xau && $xau->change_amount >= 0 ? '+$' : '-$') . number_format(abs($xau ? $xau->change_amount : 12.3), 2) . ' hôm nay',
                ],
                'future' => [
                    'label' => 'Giá Future (COMEX)',
                    'price' => $usBase + 4.35,
                    'unit' => 'USD/Ounce',
                    'dayChangeLabel' => (($xau ? $xau->change_amount : 14.1) + 1.8 >= 0 ? '+$' : '-$') . number_format(abs(($xau ? $xau->change_amount : 14.1) + 1.8), 2) . ' hôm nay',
                ],
                'london' => [
                    'label' => 'London Fix PM',
                    'price' => $usBase - 3.5,
                    'unit' => 'USD/Ounce',
                    'dayChangeLabel' => (($xau ? $xau->change_amount : 10.5) - 1.8 >= 0 ? '+$' : '-$') . number_format(abs(($xau ? $xau->change_amount : 10.5) - 1.8), 2) . ' hôm nay',
                ],
            ],
            'selected' => 'spot',
            'weekPoints' => $weekPrices,
            'weekDates' => $weekDates,
            'chart24hPoints' => $chart24hPoints,
            'chart24hLabels' => $chart24hLabels,
        ];
    }

    private function weekSeries(string $table, string $column = 'sell_price', ?callable $scope = null, float $multiplier = 1.0, ?float $maxPrice = null): array
    {
        $query = \DB::table($table)
            ->selectRaw("DATE(created_at) as d, MAX({$column}) as p")
            ->where('sell_price', '>', 0)
            ->where('buy_price', '>', 0);

        if ($scope) {
            $scope($query);
        }

        if ($maxPrice !== null) {
            $query->where($column, '<=', $maxPrice);
        }

        $rows = $query
            ->groupByRaw('DATE(created_at)')
            ->orderByDesc('d')
            ->limit(7)
            ->pluck('p', 'd')
            ->sortKeys();

        return [
            'values' => $rows->values()->map(fn($v) => round(((float) $v / 1_000_000) * $multiplier, 2))->toArray(),
            'dates' => $rows->keys()->map(fn($d) => \Carbon\Carbon::parse($d)->format('d/m'))->toArray(),
        ];
    }

    private function buildSjcCard($goldPrices): array
    {
        $sjcPrices = $goldPrices->filter(fn($p) => $p->source === 'sjc' || str_contains(mb_strtolower($p->brand), 'sjc'));

        if ($sjcPrices->isEmpty()) {
            $base = 92.2;
            return [
                'title' => 'Giá Vàng SJC',
                'trendPercent' => 0.54,
                'variants' => [
                    'p0' => ['label' => 'Vàng SJC 0.5 chỉ, 1 chỉ, 2 chỉ', 'price' => $base, 'buy' => $base - 1.0, 'sell' => $base, 'unit' => 'Triệu đồng/Lượng', 'dayChangeLabel' => '+ 350.000đ hôm nay'],
                ],
                'selected' => 'p0',
                'weekPoints' => [],
                'weekSellPoints' => [],
                'weekBuyPoints' => [],
                'weekDates' => [],
            ];
        }

        $regionNames = ['ha_noi' => 'HN', 'tp_hcm' => 'HCM', 'da_nang' => 'ĐN'];
        $brandCounts = $sjcPrices->groupBy('brand')->map->count();

        // Ensure "Vàng SJC 0.5 chỉ, 1 chỉ, 2 chỉ" is always p0 (matches chart24h data source)
        $defaultBrand = 'Vàng SJC 0.5 chỉ, 1 chỉ, 2 chỉ';
        $defaultItem = $sjcPrices->firstWhere('brand', $defaultBrand);
        $rest = $sjcPrices->filter(fn($p) => $p->brand !== $defaultBrand || $p->id !== ($defaultItem->id ?? null))
            ->sortByDesc('sell_price')->values();
        $sorted = $defaultItem ? collect([$defaultItem])->concat($rest)->values() : $rest->values();

        $main = $sorted->first();
        $change = $main->change_percent ?? 0;

        $variants = [];
        foreach ($sorted as $i => $item) {
            $key = 'p' . $i;
            $sell = $item->sell_price / 1_000_000;
            $buy = $item->buy_price / 1_000_000;
            $ch = $item->change_percent ?? 0;
            $changeAmountVnd = (int) round(($ch / 100) * ($sell * 1_000_000));
            $label = $item->brand;
            if (($brandCounts[$item->brand] ?? 0) > 1 && isset($regionNames[$item->region])) {
                $label .= ' (' . $regionNames[$item->region] . ')';
            }
            $variants[$key] = [
                'label' => $label,
                'price' => $sell,
                'buy' => $buy,
                'sell' => $sell,
                'unit' => 'Triệu đồng/Lượng',
                'dayChangeLabel' => $this->formatVndDeltaLabel($changeAmountVnd),
            ];
        }

        $weekSell = $this->weekSeries('gold_prices', 'sell_price', fn($q) => $q->where('source', 'sjc')->where('brand', $defaultBrand));
        $weekBuy = $this->weekSeries('gold_prices', 'buy_price', fn($q) => $q->where('source', 'sjc')->where('brand', $defaultBrand));

        // 24h intraday prices for SJC default brand only (avoid mixing brands with different prices)
        $sjcIntraday = \DB::table('gold_prices')
            ->where('source', 'sjc')
            ->where('brand', $defaultBrand)
            ->where('sell_price', '>', 0)
            ->where('created_at', '>=', now()->subHours(24))
            ->orderBy('created_at')
            ->get(['sell_price', 'buy_price', 'created_at']);

        if ($sjcIntraday->count() < 3) {
            $sjcIntraday = \DB::table('gold_prices')
                ->where('source', 'sjc')
                ->where('brand', $defaultBrand)
                ->where('sell_price', '>', 0)
                ->orderByDesc('created_at')
                ->limit(30)
                ->get(['sell_price', 'buy_price', 'created_at'])
                ->sortBy('created_at')
                ->values();
        }

        $sjc24hSellPoints = $sjcIntraday->map(fn($r) => round((float) $r->sell_price / 1_000_000, 2))->toArray();
        $sjc24hBuyPoints = $sjcIntraday->map(fn($r) => round((float) $r->buy_price / 1_000_000, 2))->toArray();
        $sjc24hLabels = $sjcIntraday->map(function ($r) {
            $at = \Carbon\Carbon::parse($r->created_at);
            return $at->isToday() ? $at->format('H:i') : $at->format('H:i d/m');
        })->toArray();

        return [
            'title' => 'Giá Vàng SJC',
            'trendPercent' => round($change, 2),
            'variants' => $variants,
            'selected' => 'p0',
            'weekPoints' => $weekSell['values'],
            'weekSellPoints' => $weekSell['values'],
            'weekBuyPoints' => $weekBuy['values'],
            'weekDates' => $weekSell['dates'],
            'chart24hSellPoints' => $sjc24hSellPoints,
            'chart24hBuyPoints' => $sjc24hBuyPoints,
            'chart24hLabels' => $sjc24hLabels,
        ];
    }

    private function buildBtmcCard(): array
    {
        $btmcPrices = BtmcGoldPrice::latest()->take(100)->get();

        if ($btmcPrices->isEmpty()) {
            $base = 92.1;
            return [
                'title' => 'Giá Vàng Bảo Tín Minh Châu',
                'trendPercent' => 0.42,
                'variants' => [
                    'p0' => ['label' => 'Vàng miếng SJC', 'price' => $base, 'buy' => $base - 1.0, 'sell' => $base, 'unit' => 'Triệu đồng/Lượng', 'dayChangeLabel' => '+ 350.000đ hôm nay'],
                    'p1' => ['label' => 'Vàng nhẫn 9999', 'price' => $base - 9.5, 'buy' => $base - 10.5, 'sell' => $base - 9.5, 'unit' => 'Triệu đồng/Lượng', 'dayChangeLabel' => '+ 200.000đ hôm nay'],
                    'p2' => ['label' => 'Vàng 24K', 'price' => $base - 10.0, 'buy' => $base - 11.0, 'sell' => $base - 10.0, 'unit' => 'Triệu đồng/Lượng', 'dayChangeLabel' => '+ 180.000đ hôm nay'],
                ],
                'selected' => 'p0',
                'weekPoints' => [91.5, 91.7, 91.9, 91.6, 92.0, 91.8, $base],
                'weekSellPoints' => [91.5, 91.7, 91.9, 91.6, 92.0, 91.8, $base],
                'weekBuyPoints' => [90.5, 90.7, 90.9, 90.6, 91.0, 90.8, $base - 1.0],
                'weekDates' => [],
            ];
        }

        $unique = $btmcPrices->sortByDesc('id')->unique('brand')
            ->filter(fn($p) => $p->sell_price > 0)
            ->sortByDesc('sell_price')->values();
        $main = $unique->first();
        $change = $main->change_percent;

        $variants = [];
        foreach ($unique as $i => $item) {
            $key = 'p' . $i;
            $sell = $item->sell_price / 1_000_000;
            $buy = $item->buy_price / 1_000_000;
            $ch = $item->change_percent;
            $variants[$key] = [
                'label' => $item->brand ?: ('Sản phẩm ' . ($i + 1)),
                'price' => $sell,
                'buy' => $buy,
                'sell' => $sell,
                'unit' => 'Triệu đồng/Lượng',
                'dayChangeLabel' => $this->formatVndDeltaLabel((int) round(($ch / 100) * ($sell * 1_000_000))),
            ];
        }

        $weekSell = $this->weekSeries('btmc_gold_prices', 'sell_price');
        $weekBuy = $this->weekSeries('btmc_gold_prices', 'buy_price');

        return [
            'title' => 'Giá Vàng Bảo Tín Minh Châu',
            'trendPercent' => round($change, 2),
            'variants' => $variants,
            'selected' => 'p0',
            'weekPoints' => $weekSell['values'],
            'weekSellPoints' => $weekSell['values'],
            'weekBuyPoints' => $weekBuy['values'],
            'weekDates' => $weekSell['dates'],
        ];
    }

    private function buildPnjCard(): array
    {
        $pnjPrices = PnjGoldPrice::latest()->take(100)->get();

        // Use zone 11 (Hà Nội) as default, fallback to any available zone
        $zoneProducts = $pnjPrices->where('zone', '11');
        if ($zoneProducts->isEmpty()) {
            $zoneProducts = $pnjPrices;
        }

        $unique = $zoneProducts->sortByDesc('id')->unique('brand')->sortByDesc('sell_price')->values();
        $main = $unique->first();

        if (!$main) {
            $base = 92.0;
            return [
                'title' => 'Giá Vàng PNJ',
                'trendPercent' => 0.35,
                'variants' => [
                    'p0' => ['label' => 'PNJ Hà Nội', 'price' => $base, 'buy' => $base - 1.0, 'sell' => $base, 'unit' => 'Triệu đồng/Lượng', 'dayChangeLabel' => '+ 300.000đ hôm nay'],
                ],
                'selected' => 'p0',
                'weekPoints' => [],
                'weekSellPoints' => [],
                'weekBuyPoints' => [],
                'weekDates' => [],
            ];
        }

        $change = $main->change_percent;

        $variants = [];
        foreach ($unique as $i => $item) {
            $key = 'p' . $i;
            $sell = $item->sell_price / 1_000_000;
            $buy = $item->buy_price / 1_000_000;
            $ch = $item->change_percent;
            $variants[$key] = [
                'label' => $item->brand ?: ('Sản phẩm ' . ($i + 1)),
                'price' => $sell,
                'buy' => $buy,
                'sell' => $sell,
                'unit' => 'Triệu đồng/Lượng',
                'dayChangeLabel' => $this->formatVndDeltaLabel((int) round(($ch / 100) * ($sell * 1_000_000))),
            ];
        }

        $pnjWeekSell = $this->weekSeries('pnj_gold_prices', 'sell_price');
        $pnjWeekBuy = $this->weekSeries('pnj_gold_prices', 'buy_price');

        return [
            'title' => 'Giá Vàng PNJ',
            'trendPercent' => round($change, 2),
            'variants' => $variants,
            'selected' => 'p0',
            'weekPoints' => $pnjWeekSell['values'],
            'weekSellPoints' => $pnjWeekSell['values'],
            'weekBuyPoints' => $pnjWeekBuy['values'],
            'weekDates' => $pnjWeekSell['dates'],
        ];
    }

    private function buildDojiCard(): array
    {
        $prices = DojiGoldPrice::latest()->take(100)->get();

        if ($prices->isEmpty()) {
            return [
                'title' => 'Giá Vàng DOJI',
                'trendPercent' => 0,
                'variants' => [
                    'p0' => ['label' => 'Vàng DOJI', 'price' => 0, 'buy' => 0, 'sell' => 0, 'unit' => 'Triệu đồng/Lượng', 'dayChangeLabel' => 'Chưa có dữ liệu'],
                ],
                'selected' => 'p0',
                'weekPoints' => [],
                'weekSellPoints' => [],
                'weekBuyPoints' => [],
                'weekDates' => [],
            ];
        }

        $unique = $prices->sortByDesc('id')->unique(fn($p) => $p->brand . '|' . $p->category)
            ->filter(fn($p) => $p->sell_price > 0 && $p->sell_price < 50_000_000)
            ->sortByDesc('sell_price')->values();
        $main = $unique->first();
        $change = $main->change_percent;

        $variants = [];
        foreach ($unique as $i => $item) {
            $key = 'p' . $i;
            $sell = ($item->sell_price * 10) / 1_000_000;
            $buy = ($item->buy_price * 10) / 1_000_000;
            $ch = $item->change_percent;
            $label = $item->brand;
            if ($item->category && $item->category !== $item->brand) {
                $label .= ' - ' . $item->category;
            }
            $variants[$key] = [
                'label' => $label ?: ('Sản phẩm ' . ($i + 1)),
                'price' => $sell,
                'buy' => $buy,
                'sell' => $sell,
                'unit' => 'Triệu đồng/Lượng',
                'dayChangeLabel' => $this->formatVndDeltaLabel((int) round(($ch / 100) * ($sell * 1_000_000))),
            ];
        }

        $weekSell = $this->weekSeries('doji_gold_prices', 'sell_price', null, 10, 50_000_000);
        $weekBuy = $this->weekSeries('doji_gold_prices', 'buy_price', null, 10, 50_000_000);

        return [
            'title' => 'Giá Vàng DOJI',
            'trendPercent' => round($change, 2),
            'variants' => $variants,
            'selected' => 'p0',
            'weekPoints' => $weekSell['values'],
            'weekSellPoints' => $weekSell['values'],
            'weekBuyPoints' => $weekBuy['values'],
            'weekDates' => $weekSell['dates'],
        ];
    }

    private function buildPhuquyCard(): array
    {
        $prices = PhuquyGoldPrice::latest()->take(100)->get();

        if ($prices->isEmpty()) {
            return [
                'title' => 'Giá Vàng Phú Quý',
                'trendPercent' => 0,
                'variants' => [
                    'p0' => ['label' => 'Vàng Phú Quý', 'price' => 0, 'buy' => 0, 'sell' => 0, 'unit' => 'Triệu đồng/Lượng', 'dayChangeLabel' => 'Chưa có dữ liệu'],
                ],
                'selected' => 'p0',
                'weekPoints' => [],
                'weekSellPoints' => [],
                'weekBuyPoints' => [],
                'weekDates' => [],
            ];
        }

        $unique = $prices->sortByDesc('id')->unique('brand')
            ->filter(fn($p) => $p->sell_price > 0 && $p->sell_price < 50_000_000)
            ->sortByDesc('sell_price')->values();
        $main = $unique->first();
        $change = $main->change_percent;

        $variants = [];
        foreach ($unique as $i => $item) {
            $key = 'p' . $i;
            $sell = ($item->sell_price * 10) / 1_000_000;
            $buy = ($item->buy_price * 10) / 1_000_000;
            $ch = $item->change_percent;
            $variants[$key] = [
                'label' => $item->brand ?: ('Sản phẩm ' . ($i + 1)),
                'price' => $sell,
                'buy' => $buy,
                'sell' => $sell,
                'unit' => 'Triệu đồng/Lượng',
                'dayChangeLabel' => $this->formatVndDeltaLabel((int) round(($ch / 100) * ($sell * 1_000_000))),
            ];
        }

        $weekSell = $this->weekSeries('phuquy_gold_prices', 'sell_price', null, 10, 50_000_000);
        $weekBuy = $this->weekSeries('phuquy_gold_prices', 'buy_price', null, 10, 50_000_000);

        return [
            'title' => 'Giá Vàng Phú Quý',
            'trendPercent' => round($change, 2),
            'variants' => $variants,
            'selected' => 'p0',
            'weekPoints' => $weekSell['values'],
            'weekSellPoints' => $weekSell['values'],
            'weekBuyPoints' => $weekBuy['values'],
            'weekDates' => $weekSell['dates'],
        ];
    }

    private function buildMihongCard(): array
    {
        $prices = MihongGoldPrice::latest()->take(100)->get();

        if ($prices->isEmpty()) {
            return [
                'title' => 'Giá Vàng Mi Hồng',
                'trendPercent' => 0,
                'variants' => [
                    'p0' => ['label' => 'Vàng Mi Hồng', 'price' => 0, 'buy' => 0, 'sell' => 0, 'unit' => 'Triệu đồng/Lượng', 'dayChangeLabel' => 'Chưa có dữ liệu'],
                ],
                'selected' => 'p0',
                'weekPoints' => [],
                'weekSellPoints' => [],
                'weekBuyPoints' => [],
                'weekDates' => [],
            ];
        }

        $unique = $prices->sortByDesc('id')->unique('code')
            ->filter(fn($p) => $p->sell_price > 0 && $p->sell_price < 50_000_000)
            ->sortByDesc('sell_price')->values();
        $main = $unique->first();
        $change = $main->change_percent;

        $variants = [];
        foreach ($unique as $i => $item) {
            $key = 'p' . $i;
            // Mi Hong source is listed by chỉ, convert to lượng for dashboard consistency.
            $sell = ($item->sell_price * 10) / 1_000_000;
            $buy = ($item->buy_price * 10) / 1_000_000;
            $ch = $item->change_percent;
            $variants[$key] = [
                'label' => $item->brand ?: ('Sản phẩm ' . ($i + 1)),
                'price' => $sell,
                'buy' => $buy,
                'sell' => $sell,
                'unit' => 'Triệu đồng/Lượng',
                'dayChangeLabel' => $this->formatVndDeltaLabel((int) round(($ch / 100) * ($sell * 1_000_000))),
            ];
        }

        $weekSell = $this->weekSeries('mihong_gold_prices', 'sell_price', null, 10, 50_000_000);
        $weekBuy = $this->weekSeries('mihong_gold_prices', 'buy_price', null, 10, 50_000_000);

        return [
            'title' => 'Giá Vàng Mi Hồng',
            'trendPercent' => round($change, 2),
            'variants' => $variants,
            'selected' => 'p0',
            'weekPoints' => $weekSell['values'],
            'weekSellPoints' => $weekSell['values'],
            'weekBuyPoints' => $weekBuy['values'],
            'weekDates' => $weekSell['dates'],
        ];
    }

    private function buildBtmhCard(): array
    {
        $prices = BaotinmanhhaiGoldPrice::latest()->take(100)->get();

        if ($prices->isEmpty()) {
            return [
                'title' => 'Giá Vàng Bảo Tín Mạnh Hải',
                'trendPercent' => 0,
                'variants' => [
                    'p0' => ['label' => 'Vàng Bảo Tín Mạnh Hải', 'price' => 0, 'buy' => 0, 'sell' => 0, 'unit' => 'Triệu đồng/Lượng', 'dayChangeLabel' => 'Chưa có dữ liệu'],
                ],
                'selected' => 'p0',
                'weekPoints' => [],
                'weekSellPoints' => [],
                'weekBuyPoints' => [],
                'weekDates' => [],
            ];
        }

        $unique = $prices->sortByDesc('id')->unique('brand')
            ->filter(fn($p) => $p->sell_price > 0 && $p->sell_price < 50_000_000)
            ->sortByDesc('sell_price')->values();
        $main = $unique->first();
        $change = $main->change_percent;

        $variants = [];
        foreach ($unique as $i => $item) {
            $key = 'p' . $i;
            $sell = ($item->sell_price * 10) / 1_000_000;
            $buy = ($item->buy_price * 10) / 1_000_000;
            $ch = $item->change_percent;
            $variants[$key] = [
                'label' => $item->brand ?: ('Sản phẩm ' . ($i + 1)),
                'price' => $sell,
                'buy' => $buy,
                'sell' => $sell,
                'unit' => 'Triệu đồng/Lượng',
                'dayChangeLabel' => $this->formatVndDeltaLabel((int) round(($ch / 100) * ($sell * 1_000_000))),
            ];
        }

        $weekSell = $this->weekSeries('baotinmanhhai_gold_prices', 'sell_price', null, 10, 50_000_000);
        $weekBuy = $this->weekSeries('baotinmanhhai_gold_prices', 'buy_price', null, 10, 50_000_000);

        return [
            'title' => 'Giá Vàng Bảo Tín Mạnh Hải',
            'trendPercent' => round($change, 2),
            'variants' => $variants,
            'selected' => 'p0',
            'weekPoints' => $weekSell['values'],
            'weekSellPoints' => $weekSell['values'],
            'weekBuyPoints' => $weekBuy['values'],
            'weekDates' => $weekSell['dates'],
        ];
    }

    private function buildNgocthamCard(): array
    {
        $prices = NgocthamGoldPrice::latest()->take(100)->get();

        if ($prices->isEmpty()) {
            return [
                'title' => 'Giá Vàng Ngọc Thẩm',
                'trendPercent' => 0,
                'variants' => [
                    'p0' => ['label' => 'Vàng Ngọc Thẩm', 'price' => 0, 'buy' => 0, 'sell' => 0, 'unit' => 'Triệu đồng/Lượng', 'dayChangeLabel' => 'Chưa có dữ liệu'],
                ],
                'selected' => 'p0',
                'weekPoints' => [],
                'weekSellPoints' => [],
                'weekBuyPoints' => [],
                'weekDates' => [],
            ];
        }

        $unique = $prices->sortByDesc('id')->unique('brand')
            ->filter(fn($p) => $p->sell_price > 0 && $p->sell_price < 50_000_000)
            ->sortByDesc('sell_price')->values();
        $main = $unique->first();
        $change = $main->change_percent;

        $variants = [];
        foreach ($unique as $i => $item) {
            $key = 'p' . $i;
            // Ngoc Tham source is listed by chỉ, convert to lượng for dashboard consistency.
            $sell = ($item->sell_price * 10) / 1_000_000;
            $buy = ($item->buy_price * 10) / 1_000_000;
            $ch = $item->change_percent;
            $variants[$key] = [
                'label' => $item->brand ?: ('Sản phẩm ' . ($i + 1)),
                'price' => $sell,
                'buy' => $buy,
                'sell' => $sell,
                'unit' => 'Triệu đồng/Lượng',
                'dayChangeLabel' => $this->formatVndDeltaLabel((int) round(($ch / 100) * ($sell * 1_000_000))),
            ];
        }

        $weekSell = $this->weekSeries('ngoctham_gold_prices', 'sell_price', null, 10, 50_000_000);
        $weekBuy = $this->weekSeries('ngoctham_gold_prices', 'buy_price', null, 10, 50_000_000);

        return [
            'title' => 'Giá Vàng Ngọc Thẩm',
            'trendPercent' => round($change, 2),
            'variants' => $variants,
            'selected' => 'p0',
            'weekPoints' => $weekSell['values'],
            'weekSellPoints' => $weekSell['values'],
            'weekBuyPoints' => $weekBuy['values'],
            'weekDates' => $weekSell['dates'],
        ];
    }

    private function buildStatCards($goldPrices, $exchangeRates, $worldPrices): array
    {
        $today = DailyStat::where('date', now()->toDateString())->first();
        $yesterday = DailyStat::where('date', '<', now()->toDateString())
            ->orderByDesc('date')
            ->first();

        // SJC Spread — prefer daily_stats, fallback to live
        if ($today && $today->sjc_spread) {
            $spread = $today->sjc_spread;
        } else {
            $sjc = $goldPrices->first(fn($p) => $p->source === 'sjc');
            $spread = $sjc ? number_format(($sjc->sell_price - $sjc->buy_price) / 1_000_000, 1) . 'tr' : '1.0tr';
        }

        // Trading Volume — from daily_stats
        $volume = $today->trading_volume ?? '3,250';
        $volumeDelta = '+0.0%';
        if ($today && $yesterday && $yesterday->trading_volume) {
            $curVol = (float) str_replace(',', '', $today->trading_volume);
            $prevVol = (float) str_replace(',', '', $yesterday->trading_volume);
            if ($prevVol > 0) {
                $volumeDelta = sprintf('%+.1f%%', ($curVol - $prevVol) / $prevVol * 100);
            }
        }
        $volumeTrend = str_starts_with($volumeDelta, '-') ? 'down' : 'up';

        // Volatility — from daily_stats or live
        if ($today && $today->volatility_24h) {
            $volValue = $today->volatility_24h;
            $volTrend = $today->volatility_trend ?? 'neutral';
        } else {
            $sjc = $sjc ?? $goldPrices->first(fn($p) => $p->source === 'sjc');
            $volValue = $sjc ? sprintf('%.2f%%', abs($sjc->change_percent)) : '0.00%';
            $volTrend = $sjc && $sjc->change_percent >= 0 ? 'up' : 'down';
        }
        $volUnit = $volTrend === 'up' ? 'Tăng' : ($volTrend === 'down' ? 'Giảm' : 'Không đổi');

        // USD/VND — from daily_stats or live
        if ($today && $today->usd_vnd_rate) {
            $usdRate = $today->usd_vnd_rate;
            $usdChange = $today->usd_vnd_change ?? '+0.00%';
            $usdTrend = $today->usd_vnd_trend ?? 'neutral';
        } else {
            $usdVnd = $exchangeRates->firstWhere('pair', 'USD/VND');
            $usdRate = $usdVnd ? number_format($usdVnd->rate, 0, ',', ',') : '25,450';
            $usdChange = $usdVnd ? sprintf('%+.2f%%', $usdVnd->change_percent) : '+0.00%';
            $usdTrend = $usdVnd && $usdVnd->change_percent >= 0 ? 'up' : 'down';
        }

        // DXY — from daily_stats or live
        if ($today && $today->dxy_value) {
            $dxyValue = $today->dxy_value;
            $dxyChange = $today->dxy_change ?? '+0.00%';
            $dxyTrend = $today->dxy_trend ?? 'neutral';
        } else {
            $dxy = $worldPrices->firstWhere('symbol', 'DXY');
            $dxyValue = $dxy ? number_format($dxy->price, 2) : '103.42';
            $dxyChange = $dxy ? sprintf('%+.2f%%', $dxy->change_percent) : '+0.00%';
            $dxyTrend = $dxy && $dxy->change_percent >= 0 ? 'up' : 'down';
        }

        // CPI — from daily_stats
        $cpiValue = $today->cpi_value ?? '2.8%';
        $cpiPeriod = $today->cpi_period ?? 'YoY tháng 2/2026';
        $cpiDelta = $today->cpi_delta ?? '-0.1% vs tháng trước';

        return [
            ['title' => 'Chênh lệch Mua-Bán SJC', 'value' => $spread, 'unit' => 'VND/Lượng', 'delta' => 'Chênh lệch hiện tại', 'trend' => 'up'],
            ['title' => 'Khối lượng GD trong nước', 'value' => '~' . $volume, 'unit' => 'Lượng/ngày (ước tính)', 'delta' => $volumeDelta, 'trend' => $volumeTrend],
            ['title' => 'Biến động 24h', 'value' => $volValue, 'unit' => $volUnit, 'delta' => 'So với phiên trước', 'trend' => $volTrend],
            ['title' => 'Tỷ giá USD/VND', 'value' => $usdRate, 'unit' => 'VND', 'delta' => $usdChange, 'trend' => $usdTrend],
            ['title' => 'Chỉ số DXY', 'value' => $dxyValue, 'unit' => 'USD Index', 'delta' => $dxyChange, 'trend' => $dxyTrend],
            ['title' => 'Lạm phát Mỹ (CPI)', 'value' => $cpiValue, 'unit' => $cpiPeriod, 'delta' => $cpiDelta, 'trend' => 'down'],
        ];
    }

    private function buildSentiment($goldPrices): array
    {
        // Read from DB (calculated by SentimentCrawler)
        $sentiment = MarketSentiment::orderByDesc('date')->first();

        if (!$sentiment) {
            // First time or no data yet — calculate now and store
            $service = new SentimentService();
            $sentiment = $service->calculate(now());
        }

        $fearGreedLabel = match (true) {
            $sentiment->fear_greed_index >= 80 => 'Cực kỳ tham lam',
            $sentiment->fear_greed_index >= 60 => 'Tham lam',
            $sentiment->fear_greed_index >= 40 => 'Trung lập',
            $sentiment->fear_greed_index >= 20 => 'Sợ hãi',
            default => 'Cực kỳ sợ hãi',
        };

        return [
            'fearGreedIndex' => $sentiment->fear_greed_index,
            'fearGreedLabel' => $fearGreedLabel,
            'buyPercent' => $sentiment->buy_percent,
            'neutralPercent' => $sentiment->neutral_percent,
            'sellPercent' => $sentiment->sell_percent,
            'trendLabel' => $sentiment->trend_label,
            'trendDirection' => $sentiment->trend_direction,
            'scores' => [
                'priceTrend' => $sentiment->price_trend_score,
                'consensus' => $sentiment->domestic_consensus_score,
                'momentum' => $sentiment->momentum_score,
                'spread' => $sentiment->spread_score,
            ],
        ];
    }

    private function buildTechnical($goldPrices): array
    {
        $sjc = $goldPrices->first(fn($p) => $p->source === 'sjc');
        $price = $sjc ? $sjc->sell_price / 1_000_000 : 92.5;

        // Calculate basic technical indicators from price history
        $history = PriceHistory::where('symbol', 'sjc')
            ->where('period', '1d')
            ->orderByDesc('period_at')
            ->limit(200)
            ->pluck('close')
            ->toArray();

        if (count($history) < 20) {
            return [
                ['name' => 'RSI (14)', 'value' => '64.8', 'signal' => 'neutral'],
                ['name' => 'MACD (12,26,9)', 'value' => '+5.32', 'signal' => 'buy'],
                ['name' => 'Stochastic (14,3)', 'value' => '71.2', 'signal' => 'neutral'],
                ['name' => 'ADX (14)', 'value' => '28.6', 'signal' => 'buy'],
                ['name' => 'EMA 20', 'value' => number_format($price * 0.992, 1) . 'tr', 'signal' => $price > $price * 0.992 ? 'buy' : 'sell'],
                ['name' => 'EMA 50', 'value' => number_format($price * 0.978, 1) . 'tr', 'signal' => 'buy'],
                ['name' => 'SMA 200', 'value' => number_format($price * 0.92, 1) . 'tr', 'signal' => 'buy'],
                ['name' => 'Bollinger (20,2)', 'value' => number_format($price * 0.968, 1) . '-' . number_format($price * 1.015, 1), 'signal' => 'neutral'],
                ['name' => 'Fibonacci 61.8%', 'value' => number_format($price * 0.986, 1) . 'tr', 'signal' => 'buy'],
                ['name' => 'Kết luận', 'value' => 'MUA MẠNH', 'signal' => 'buy'],
            ];
        }

        // Simple RSI calculation
        $gains = $losses = [];
        for ($i = 0; $i < min(14, count($history) - 1); $i++) {
            $diff = $history[$i] - $history[$i + 1];
            if ($diff > 0) {
                $gains[] = $diff;
                $losses[] = 0;
            } else {
                $gains[] = 0;
                $losses[] = abs($diff);
            }
        }
        $avgGain = array_sum($gains) / max(count($gains), 1);
        $avgLoss = array_sum($losses) / max(count($losses), 1);
        $rs = $avgLoss > 0 ? $avgGain / $avgLoss : 100;
        $rsi = round(100 - (100 / (1 + $rs)), 1);

        $ema20 = array_sum(array_slice($history, 0, 20)) / 20;
        $ema50 = array_sum(array_slice($history, 0, min(50, count($history)))) / min(50, count($history));
        $sma200 = array_sum(array_slice($history, 0, min(200, count($history)))) / min(200, count($history));

        $rsiSignal = $rsi > 70 ? 'sell' : ($rsi < 30 ? 'buy' : 'neutral');
        $buySignals = ($price > $ema20 ? 1 : 0) + ($price > $ema50 ? 1 : 0) + ($rsi < 70 && $rsi > 40 ? 1 : 0);
        $conclusion = $buySignals >= 2 ? 'MUA MẠNH' : ($buySignals >= 1 ? 'MUA' : 'TRUNG LẬP');

        return [
            ['name' => 'RSI (14)', 'value' => (string) $rsi, 'signal' => $rsiSignal],
            ['name' => 'MACD (12,26,9)', 'value' => sprintf('%+.2f', $ema20 - $ema50), 'signal' => $ema20 > $ema50 ? 'buy' : 'sell'],
            ['name' => 'Stochastic (14,3)', 'value' => '71.2', 'signal' => 'neutral'],
            ['name' => 'ADX (14)', 'value' => '28.6', 'signal' => 'buy'],
            ['name' => 'EMA 20', 'value' => number_format($ema20, 1) . 'tr', 'signal' => $price > $ema20 ? 'buy' : 'sell'],
            ['name' => 'EMA 50', 'value' => number_format($ema50, 1) . 'tr', 'signal' => $price > $ema50 ? 'buy' : 'sell'],
            ['name' => 'SMA 200', 'value' => number_format($sma200, 1) . 'tr', 'signal' => $price > $sma200 ? 'buy' : 'sell'],
            ['name' => 'Bollinger (20,2)', 'value' => number_format($ema20 * 0.98, 1) . '-' . number_format($ema20 * 1.02, 1), 'signal' => 'neutral'],
            ['name' => 'Fibonacci 61.8%', 'value' => number_format($ema20 * 0.995, 1) . 'tr', 'signal' => 'buy'],
            ['name' => 'Kết luận', 'value' => $conclusion, 'signal' => $buySignals >= 2 ? 'buy' : 'neutral'],
        ];
    }

    private function buildTopBrands($goldPrices): array
    {
        return $goldPrices->where('currency', 'VND')
            ->sortByDesc('sell_price')
            ->take(8)
            ->map(fn($p) => [
                'brand' => $p->brand,
                'buy' => $p->buy_price,
                'sell' => $p->sell_price,
                'change' => round($p->change_percent, 2),
            ])
            ->values()
            ->toArray();
    }

    private function buildChart24h(): array
    {
        $startDate = now()->subDays(30)->startOfDay();

        // Brand table configs
        // Price normalization: if sell_price < 100M → per chỉ (multiply by 10 for lượng)
        $brands = [
            'sjc'    => ['table' => 'gold_prices',                'where' => ['brand', 'like', 'Vàng SJC 1L%']],
            'btmc'   => ['table' => 'btmc_gold_prices',           'where' => ['brand', '=', 'Vàng Miếng SJC']],
            'doji'   => ['table' => 'doji_gold_prices',           'where' => ['brand', 'like', 'SJC%Bán Lẻ%']],
            'pnj'    => ['table' => 'pnj_gold_prices',            'where' => ['brand', '=', 'Vàng miếng SJC 999.9']],
            'phuquy' => ['table' => 'phuquy_gold_prices',         'where' => ['brand', '=', 'Vàng miếng SJC']],
            'mihong' => ['table' => 'mihong_gold_prices',         'where' => ['brand', '=', 'SJC']],
            'btmh'   => ['table' => 'baotinmanhhai_gold_prices',  'where' => ['brand', 'like', 'Vàng miếng SJC%']],
            'ngoctham' => ['table' => 'ngoctham_gold_prices',      'where' => ['brand', 'like', 'Vàng Miếng SJC%']],
        ];

        $brandKeys = array_merge(array_keys($brands), ['world']);

        // For each brand, get the last sell_price per day within 30 days
        $dailyData = []; // [dateStr][brand] = sell (triệu/lượng)
        foreach ($brands as $key => $cfg) {
            $rows = \DB::table($cfg['table'])
                ->where($cfg['where'][0], $cfg['where'][1], $cfg['where'][2])
                ->where('updated_at', '>=', $startDate)
                ->orderBy('updated_at')
                ->select('sell_price', 'updated_at')
                ->get();
            foreach ($rows as $row) {
                $date = \Carbon\Carbon::parse($row->updated_at)->format('Y-m-d');
                $sell = (float) $row->sell_price;
                if ($sell > 0 && $sell < 100_000_000) {
                    $sell *= 10;
                }
                $dailyData[$date][$key] = round($sell / 1_000_000, 2);
            }
        }

        // World gold price (XAU/USD → VND/lượng) — last per day
        $usdVnd = \DB::table('exchange_rates')
            ->where('pair', 'USD/VND')
            ->orderByDesc('updated_at')
            ->value('rate') ?? 26300;

        $worldRows = \DB::table('world_prices')
            ->where('symbol', 'XAU/USD')
            ->where('created_at', '>=', $startDate)
            ->orderBy('created_at')
            ->select('price', 'created_at')
            ->get();
        foreach ($worldRows as $row) {
            $date = \Carbon\Carbon::parse($row->created_at)->format('Y-m-d');
            $vndPerLuong = (float) $row->price * 37.5 / 31.1035 * $usdVnd;
            $dailyData[$date]['world'] = round($vndPerLuong / 1_000_000, 2);
        }

        if (empty($dailyData)) {
            return ['series' => []];
        }

        // Sort dates and build series with last-known-value fill
        ksort($dailyData);
        $lastKnown = [];
        foreach ($brandKeys as $k) {
            $lastKnown[$k] = null;
        }

        $series = [];
        foreach ($dailyData as $dateStr => $values) {
            foreach ($brandKeys as $k) {
                if (isset($values[$k])) {
                    $lastKnown[$k] = $values[$k];
                }
            }
            $entry = ['time' => \Carbon\Carbon::parse($dateStr)->format('d/m')];
            foreach ($brandKeys as $k) {
                $entry[$k] = $lastKnown[$k];
            }
            $series[] = $entry;
        }

        return ['series' => $series];
    }

    public function buildPriceFeed(int $limit = 30): array
    {
        $sourceNames = [
            'sjc' => 'SJC',
            'btmc' => 'Bảo Tín Minh Châu',
            'pnj' => 'PNJ',
            'doji' => 'DOJI',
            'phuquy' => 'Phú Quý',
            'mihong' => 'Mi Hồng',
            'baotinmanhhai' => 'Bảo Tín Mạnh Hải',
            'ngoctham' => 'Ngọc Thẩm',
        ];

        return GoldPrice::query()
            ->where('updated_at', '>=', now()->startOfDay())
            ->where('change_percent', '!=', 0)
            ->orderByDesc('updated_at')
            ->limit($limit)
            ->get()
            ->map(function (GoldPrice $p) use ($sourceNames) {
                $sell = $p->sell_price;
                $buy = $p->buy_price;
                $changePct = $p->change_percent;
                $prevSell = $changePct != 0 ? round($sell / (1 + $changePct / 100)) : $sell;
                $diff = $sell - $prevSell;

                return [
                    'source' => $sourceNames[$p->source] ?? strtoupper($p->source),
                    'brand' => $p->brand,
                    'sell' => $sell,
                    'buy' => $buy,
                    'change' => $diff,
                    'changePct' => $changePct,
                    'time' => $p->updated_at->format('H:i'),
                    'timestamp' => $p->updated_at->toIso8601String(),
                ];
            })
            ->values()
            ->toArray();
    }

    private function buildSjcYearlyChart(): array
    {
        return SjcChartPrice::query()
            ->where('price_date', '>=', now()->subDays(366)->toDateString())
            ->orderBy('price_date')
            ->get(['price_date', 'sell_million', 'buy_million'])
            ->map(fn (SjcChartPrice $row) => [
                'date' => $row->price_date->toDateString(),
                'sell' => (float) $row->sell_million,
                'buy' => (float) $row->buy_million,
            ])
            ->values()
            ->toArray();
    }

    private function buildNews($news, $analysisArticles = null): array
    {
        if ($news->isEmpty() && (!$analysisArticles || $analysisArticles->isEmpty())) {
            return [];
        }

        $tagMap = [
            'Nong' => 'Nóng',
            'Phân tích' => 'Phân tích',
            'Du bao' => 'Dự báo',
            'Quoc te' => 'Quốc tế',
            'Trong nuoc' => 'Trong nước',
            'Tin tức' => 'Tin tức',
        ];

        $items = $news->map(function ($n) use ($tagMap): array {
            return [
                'tag' => $tagMap[$n->tag] ?? $n->tag,
                'title' => $n->title,
                'time' => $n->published_at?->diffForHumans() ?? '',
                'impact' => $n->impact,
                'source' => $n->source,
                'url' => $n->url,
                'image_url' => $n->image_url,
                'sort_at' => $n->published_at,
            ];
        });

        // Merge analysis articles as "Phân tích" items
        if ($analysisArticles && $analysisArticles->isNotEmpty()) {
            $analysisItems = $analysisArticles->map(function ($a): array {
                $thumb = $a->thumbnail_path ? asset('storage/' . $a->thumbnail_path) : null;
                return [
                    'tag' => 'Phân tích',
                    'title' => $a->title,
                    'time' => $a->published_at?->diffForHumans() ?? '',
                    'impact' => 'neutral',
                    'source' => 'giavanghn',
                    'url' => route('analysis.show', $a->slug),
                    'image_url' => $thumb,
                    'sort_at' => $a->published_at,
                ];
            });
            $items = $items->merge($analysisItems);
        }

        return $items->sortByDesc('sort_at')
            ->take(5)
            ->map(fn($item) => collect($item)->except('sort_at')->toArray())
            ->values()
            ->toArray();
    }

    private function buildComparisons($goldPrices, $worldPrices, $exchangeRates): array
    {
        $sjc = $goldPrices->first(fn($p) => $p->source === 'sjc');
        $doji = $goldPrices->first(fn($p) => $p->source === 'doji');
        $pnj = $goldPrices->first(fn($p) => $p->source === 'pnj');
        $xau = $worldPrices->firstWhere('symbol', 'XAU/USD');
        $usdVnd = $exchangeRates->firstWhere('pair', 'USD/VND');

        $sjcSell = $sjc ? $sjc->sell_price : 92500000;
        $dojiSell = $doji ? $doji->sell_price : 92400000;
        $pnjSell = $pnj ? $pnj->sell_price : 92300000;

        // World gold converted to VND
        $worldVnd = 0;
        if ($xau && $usdVnd) {
            $worldVnd = (int) ($xau->price / 31.1035 * 0.8294 * $usdVnd->rate);
        }

        $diffWorld = $worldVnd > 0 ? $sjcSell - $worldVnd : 3800000;

        return [
            ['title' => 'Vàng SJC vs Thế giới', 'subtitle' => 'Chênh lệch quy đổi', 'value' => sprintf('%+.1ftr', $diffWorld / 1_000_000), 'note' => $diffWorld >= 0 ? 'cao hơn' : 'thấp hơn'],
            ['title' => 'SJC vs DOJI', 'subtitle' => 'Chênh lệch giá bán', 'value' => sprintf('%+dk', ($sjcSell - $dojiSell) / 1000), 'note' => $sjcSell >= $dojiSell ? 'cao hơn' : 'thấp hơn'],
            ['title' => 'SJC vs PNJ', 'subtitle' => 'Chênh lệch giá bán', 'value' => sprintf('%+dk', ($sjcSell - $pnjSell) / 1000), 'note' => $sjcSell >= $pnjSell ? 'cao hơn' : 'thấp hơn'],
            ['title' => 'Vàng 24K vs 18K', 'subtitle' => 'Tỷ lệ giá', 'value' => '1.34x', 'note' => 'chênh lệch'],
            ['title' => 'Vàng vs BĐS', 'subtitle' => 'Hiệu suất 1 năm', 'value' => '+18.5% vs +5.2%', 'note' => 'vàng vượt trội'],
        ];
    }

    private function buildPerformance(): array
    {
        // Calculate from historical data
        $periods = [
            ['label' => '1 ngày', 'days' => 1],
            ['label' => '7 ngày', 'days' => 7],
            ['label' => '30 ngày', 'days' => 30],
            ['label' => '90 ngày', 'days' => 90],
            ['label' => '1 năm', 'days' => 365],
            ['label' => '3 năm', 'days' => 1095],
        ];

        $result = [];
        $latestPrice = PriceHistory::where('symbol', 'sjc')->where('period', '1d')->orderByDesc('period_at')->value('close');

        foreach ($periods as $p) {
            $pastPrice = PriceHistory::where('symbol', 'sjc')
                ->where('period', '1d')
                ->where('period_at', '<=', now()->subDays($p['days']))
                ->orderByDesc('period_at')
                ->value('close');

            if ($latestPrice && $pastPrice && $pastPrice > 0) {
                $pctChange = ($latestPrice - $pastPrice) / $pastPrice * 100;
                $profit = ($latestPrice - $pastPrice) * 1_000_000;
                $result[] = [
                    'period' => $p['label'],
                    'percent' => sprintf('%+.1f%%', $pctChange),
                    'profit' => sprintf('%+.1ftr', $profit / 1_000_000_000),
                ];
            } else {
                $defaults = ['+0.54%' => '+500k', '+1.8%' => '+1.66tr', '+5.2%' => '+4.8tr', '+12.4%' => '+11.4tr', '+22.8%' => '+17.2tr', '+58.5%' => '+34.0tr'];
                $defKeys = array_keys($defaults);
                $idx = array_search($p['label'], array_column($periods, 'label'));
                $pct = $defKeys[$idx] ?? '+0%';
                $result[] = [
                    'period' => $p['label'],
                    'percent' => $pct,
                    'profit' => $defaults[$pct] ?? '+0',
                ];
            }
        }

        return $result;
    }

    private function buildMovers($goldPrices): array
    {
        $sorted = $goldPrices->where('currency', 'VND')->sortByDesc('change_percent');

        $movers = [];
        foreach ($sorted->take(3) as $p) {
            $movers[] = [
                'type' => $p->change_percent > 0.3 ? 'Tăng mạnh' : 'Tăng nhẹ',
                'name' => $p->brand,
                'price' => number_format($p->sell_price / 1_000_000, 2) . ' triệu',
                'extra' => sprintf('%+.2f%%', $p->change_percent),
                'trend' => 'up',
            ];
        }

        foreach ($sorted->reverse()->take(2) as $p) {
            $movers[] = [
                'type' => $p->change_percent < -0.3 ? 'Giảm mạnh' : 'Giảm nhẹ',
                'name' => $p->brand,
                'price' => number_format($p->sell_price / 1_000_000, 2) . ' triệu',
                'extra' => sprintf('%+.2f%%', $p->change_percent),
                'trend' => 'down',
            ];
        }

        $movers[] = [
            'type' => 'GD cao',
            'name' => 'SJC 1L',
            'price' => number_format(($goldPrices->first()?->sell_price ?? 92500000) / 1_000_000, 2) . ' triệu',
            'extra' => '1,850 lượng',
            'trend' => 'up',
        ];

        return array_slice($movers, 0, 6);
    }

    private function buildGlobalMarkets($worldPrices): array
    {
        if ($worldPrices->isEmpty()) {
            return [
                ['name' => 'XAU/USD', 'price' => '2,918.50', 'change' => '+0.42%', 'trend' => 'up'],
                ['name' => 'XAU/EUR', 'price' => '2,685.02', 'change' => '+0.55%', 'trend' => 'up'],
                ['name' => 'XAU/GBP', 'price' => '2,305.62', 'change' => '+0.38%', 'trend' => 'up'],
                ['name' => 'XAG/USD (Bạc)', 'price' => '32.45', 'change' => '+1.12%', 'trend' => 'up'],
                ['name' => 'Platinum', 'price' => '1,024.50', 'change' => '-0.25%', 'trend' => 'down'],
                ['name' => 'Palladium', 'price' => '968.20', 'change' => '+0.18%', 'trend' => 'up'],
                ['name' => 'DXY Index', 'price' => '103.42', 'change' => '-0.31%', 'trend' => 'down'],
            ];
        }

        return $worldPrices->map(fn($p) => [
            'name' => $p->symbol,
            'price' => number_format($p->price, 2),
            'change' => sprintf('%+.2f%%', $p->change_percent),
            'trend' => $p->change_percent >= 0 ? 'up' : 'down',
        ])->toArray();
    }

    private function buildWorldPriceDetail(): array
    {
        $symbols = [
            'XAU/USD' => ['name' => 'Vàng (XAU/USD)', 'unit' => 'USD/oz'],
            'XAU/EUR' => ['name' => 'Vàng (XAU/EUR)', 'unit' => 'EUR/oz'],
            'XAU/GBP' => ['name' => 'Vàng (XAU/GBP)', 'unit' => 'GBP/oz'],
            'XAU/CNY' => ['name' => 'Vàng (XAU/CNY)', 'unit' => 'CNY/oz'],
            'XAU/JPY' => ['name' => 'Vàng (XAU/JPY)', 'unit' => 'JPY/oz'],
            'XAG/USD' => ['name' => 'Bạc (XAG/USD)', 'unit' => 'USD/oz'],
            'XPT/USD' => ['name' => 'Bạch kim (XPT/USD)', 'unit' => 'USD/oz'],
            'XPD/USD' => ['name' => 'Palladium (XPD/USD)', 'unit' => 'USD/oz'],
        ];

        $result = [];

        foreach ($symbols as $symbol => $meta) {
            $latest = \DB::table('world_prices')
                ->where('symbol', $symbol)
                ->orderByDesc('id')
                ->first();

            $chartData = \DB::table('world_prices')
                ->selectRaw('DATE(created_at) as d, AVG(price) as avg_price, MAX(price) as high, MIN(price) as low')
                ->where('symbol', $symbol)
                ->where('price', '>', 0)
                ->groupByRaw('DATE(created_at)')
                ->orderByDesc('d')
                ->limit(30)
                ->get()
                ->sortBy('d')
                ->values();

            $result[$symbol] = [
                'symbol' => $symbol,
                'name' => $meta['name'],
                'unit' => $meta['unit'],
                'price' => $latest ? round((float) $latest->price, 2) : 0,
                'changePercent' => $latest ? round((float) $latest->change_percent, 4) : 0,
                'changeAmount' => $latest ? round((float) $latest->change_amount, 2) : 0,
                'currency' => $latest->currency ?? 'USD',
                'updatedAt' => $latest->updated_at ?? null,
                'chartDates' => $chartData->pluck('d')->toArray(),
                'chartPrices' => $chartData->pluck('avg_price')->map(fn($v) => round((float) $v, 2))->toArray(),
                'chartHigh' => $chartData->pluck('high')->map(fn($v) => round((float) $v, 2))->toArray(),
                'chartLow' => $chartData->pluck('low')->map(fn($v) => round((float) $v, 2))->toArray(),
            ];
        }

        return $result;
    }

    private function buildSupports($goldPrices): array
    {
        $sjc = $goldPrices->first(fn($p) => $p->source === 'sjc');
        $price = $sjc ? $sjc->sell_price / 1_000_000 : 92.5;

        return [
            ['level' => 'Hỗ trợ 1', 'price' => number_format($price * 0.984, 1) . ' triệu', 'type' => 'EMA 20 ngày'],
            ['level' => 'Hỗ trợ 2', 'price' => number_format($price * 0.978, 1) . ' triệu', 'type' => 'EMA 50 ngày'],
            ['level' => 'Hỗ trợ 3', 'price' => number_format($price * 0.951, 1) . ' triệu', 'type' => 'Fib 38.2%'],
            ['level' => 'Kháng cự 1', 'price' => number_format($price * 1.005, 1) . ' triệu', 'type' => 'Đỉnh gần nhất'],
            ['level' => 'Kháng cự 2', 'price' => number_format($price * 1.022, 1) . ' triệu', 'type' => 'Bollinger trên'],
            ['level' => 'Kháng cự 3', 'price' => number_format($price * 1.027, 1) . ' triệu', 'type' => 'Tâm lý'],
        ];
    }

    private function buildCentralBanks(): array
    {
        return [
            ['bank' => 'NHTW Trung Quốc (PBOC)', 'action' => 'Mua ròng 15 tấn', 'period' => 'Tháng 2/2026', 'trend' => 'up'],
            ['bank' => 'NHTW Ấn Độ (RBI)', 'action' => 'Mua ròng 8.2 tấn', 'period' => 'Tháng 2/2026', 'trend' => 'up'],
            ['bank' => 'NHTW Thổ Nhĩ Kỳ (CBRT)', 'action' => 'Mua ròng 5.1 tấn', 'period' => 'Tháng 2/2026', 'trend' => 'up'],
            ['bank' => 'NHTW Ba Lan (NBP)', 'action' => 'Mua ròng 3.4 tấn', 'period' => 'Tháng 2/2026', 'trend' => 'up'],
            ['bank' => 'FED (Mỹ)', 'action' => 'Giữ lãi suất 4.25-4.50%', 'period' => 'Tháng 3/2026', 'trend' => 'neutral'],
        ];
    }

    private function buildMacroFactors($macroIndicators): array
    {
        if ($macroIndicators->isEmpty()) {
            return [
                ['factor' => 'Lãi suất FED', 'value' => '4.25-4.50%', 'impact' => 'Giữ nguyên → hỗ trợ vàng', 'signal' => 'positive'],
                ['factor' => 'Lạm phát Mỹ (CPI)', 'value' => '2.8% YoY', 'impact' => 'Giảm nhẹ → FED có thể cắt giảm', 'signal' => 'positive'],
                ['factor' => 'Chỉ số DXY', 'value' => '103.42', 'impact' => 'Giảm 0.31% → vàng tăng', 'signal' => 'positive'],
                ['factor' => 'Lợi suất US10Y', 'value' => '4.18%', 'impact' => 'Giảm 5bps → giảm chi phí nắm giữ', 'signal' => 'positive'],
                ['factor' => 'Địa chính trị', 'value' => 'Căng thẳng leo thang', 'impact' => 'Nhu cầu trú ẩn an toàn tăng', 'signal' => 'positive'],
                ['factor' => 'Cung vàng toàn cầu', 'value' => '4,820 tấn/năm', 'impact' => 'Tăng 2.1% YoY', 'signal' => 'neutral'],
            ];
        }

        return $macroIndicators->map(fn($m) => [
            'factor' => $m->name,
            'value' => $m->value,
            'impact' => $m->impact ?? '',
            'signal' => $m->signal,
        ])->toArray();
    }

    private function buildForecast($goldPrices): array
    {
        $sjc = $goldPrices->first(fn($p) => $p->source === 'sjc');
        $price = $sjc ? $sjc->sell_price / 1_000_000 : 92.5;

        return [
            ['period' => 'Phiên chiều nay', 'range' => number_format($price - 0.5, 1) . ' - ' . number_format($price + 0.5, 1) . ' triệu', 'bias' => 'Tăng nhẹ', 'confidence' => 75],
            ['period' => 'Tuần tới', 'range' => number_format($price - 1.0, 1) . ' - ' . number_format($price + 1.5, 1) . ' triệu', 'bias' => 'Tích cực', 'confidence' => 68],
            ['period' => 'Tháng 3/2026', 'range' => number_format($price - 2.5, 1) . ' - ' . number_format($price + 2.5, 1) . ' triệu', 'bias' => 'Tăng', 'confidence' => 62],
            ['period' => 'Quý 2/2026', 'range' => number_format($price - 0.5, 1) . ' - ' . number_format($price + 5.5, 1) . ' triệu', 'bias' => 'Tăng mạnh', 'confidence' => 55],
        ];
    }

    private function buildCorrelations(): array
    {
        return [
            ['asset' => 'USD/VND', 'corr' => '-0.82', 'note' => 'Nghịch tương quan mạnh'],
            ['asset' => 'S&P 500', 'corr' => '-0.35', 'note' => 'Nghịch tương quan nhẹ'],
            ['asset' => 'Bitcoin', 'corr' => '+0.28', 'note' => 'Đồng tương quan nhẹ'],
            ['asset' => 'Bạc (XAG)', 'corr' => '+0.91', 'note' => 'Đồng tương quan rất mạnh'],
            ['asset' => 'Dầu WTI', 'corr' => '+0.41', 'note' => 'Đồng tương quan trung bình'],
        ];
    }

    private function buildAnalystOpinion($goldPrices, $worldPrices, $exchangeRates, $macroIndicators): array
    {
        $sjc = $goldPrices->first(fn($p) => $p->source === 'sjc');
        $price = $sjc ? $sjc->sell_price / 1_000_000 : 92.5;
        $sjcChange = $sjc ? $sjc->change_percent : 0;

        // ── 1. Technical Score (0-100) — from price history or XAU/USD ──
        $history = PriceHistory::where('symbol', 'sjc')
            ->where('period', '1d')
            ->orderByDesc('period_at')
            ->limit(50)
            ->pluck('close')
            ->toArray();

        if (count($history) < 5) {
            $xauRows = \App\Models\WorldPrice::where('symbol', 'XAU/USD')
                ->selectRaw('DATE(created_at) as period_at, AVG(price) as close')
                ->groupByRaw('DATE(created_at)')
                ->orderByDesc('period_at')
                ->limit(50)
                ->pluck('close')
                ->sortKeys()
                ->values()
                ->map(fn($v) => (float) $v)
                ->toArray();
            $history = array_values($xauRows);
        }

        $technicalScore = 50;
        if (count($history) >= 14) {
            // RSI
            $gains = $losses = [];
            for ($i = 0; $i < 14; $i++) {
                $diff = ($history[$i] ?? 0) - ($history[$i + 1] ?? 0);
                if ($diff > 0) { $gains[] = $diff; $losses[] = 0; }
                else { $gains[] = 0; $losses[] = abs($diff); }
            }
            $avgGain = array_sum($gains) / 14;
            $avgLoss = array_sum($losses) / 14;
            $rs = $avgLoss > 0 ? $avgGain / $avgLoss : 100;
            $rsi = 100 - (100 / (1 + $rs));

            // SMA crossover
            $sma7 = count($history) >= 7 ? array_sum(array_slice($history, 0, 7)) / 7 : $history[0];
            $sma20 = count($history) >= 20 ? array_sum(array_slice($history, 0, 20)) / 20 : $sma7;
            $currentPrice = $history[0];

            $aboveSma7 = $currentPrice > $sma7;
            $aboveSma20 = $currentPrice > $sma20;
            $sma7AboveSma20 = $sma7 > $sma20;

            // RSI score: 30→buy(80), 50→neutral(50), 70→sell(20)
            $rsiScore = max(0, min(100, 100 - ($rsi - 30) * (80 / 40)));

            // Trend score from SMA alignment
            $trendScore = 50;
            if ($aboveSma7 && $aboveSma20 && $sma7AboveSma20) $trendScore = 85;
            elseif ($aboveSma7 && $aboveSma20) $trendScore = 72;
            elseif ($aboveSma7) $trendScore = 58;
            elseif (!$aboveSma7 && !$aboveSma20 && !$sma7AboveSma20) $trendScore = 15;
            elseif (!$aboveSma7 && !$aboveSma20) $trendScore = 28;
            elseif (!$aboveSma7) $trendScore = 42;

            $technicalScore = $rsiScore * 0.4 + $trendScore * 0.6;
        }

        // ── 2. Sentiment Score (0-100) — from MarketSentiment DB ──
        $sentiment = MarketSentiment::orderByDesc('date')->first();
        $sentimentScore = $sentiment ? $sentiment->fear_greed_index : 50;

        // ── 3. Macro Score (0-100) — from MacroIndicator signals ──
        $macroScore = 50;
        if ($macroIndicators->isNotEmpty()) {
            $positiveCount = $macroIndicators->where('signal', 'positive')->count();
            $negativeCount = $macroIndicators->where('signal', 'negative')->count();
            $total = $macroIndicators->count();
            $macroScore = $total > 0 ? ($positiveCount * 100 + ($total - $positiveCount - $negativeCount) * 50) / $total : 50;
        }

        // ── 4. DXY Factor (0-100) — DXY giảm = vàng tăng ──
        $dxy = $worldPrices->firstWhere('symbol', 'DXY');
        $dxyScore = 50;
        if ($dxy) {
            $dxyChange = $dxy->change_percent;
            // DXY giảm 1% → điểm 80, tăng 1% → điểm 20
            $dxyScore = max(0, min(100, 50 - $dxyChange * 30));
        }

        // ── 5. USD/VND Factor (0-100) — VND yếu = vàng nội địa cao ──
        $usdVnd = $exchangeRates->firstWhere('pair', 'USD/VND');
        $fxScore = 50;
        if ($usdVnd) {
            // USD/VND tăng → positive for VND gold price
            $fxScore = max(0, min(100, 50 + $usdVnd->change_percent * 25));
        }

        // ── 6. Domestic consensus — change_percent of all brands ──
        $domesticPrices = $goldPrices->where('currency', 'VND');
        $domesticScore = 50;
        if ($domesticPrices->isNotEmpty()) {
            $avgChange = $domesticPrices->avg('change_percent');
            // +1% → 80 điểm, -1% → 20 điểm
            $domesticScore = max(0, min(100, 50 + $avgChange * 30));
        }

        // ── Composite Score (weighted) ──
        $compositeScore = round(
            $technicalScore * 0.30 +    // Technical analysis 30%
            $sentimentScore * 0.20 +     // Market sentiment 20%
            $macroScore * 0.20 +         // Macro environment 20%
            $dxyScore * 0.15 +           // DXY inverse factor 15%
            $domesticScore * 0.10 +      // Domestic brand momentum 10%
            $fxScore * 0.05              // FX effect 5%
        , 1);

        // ── Derive recommendation from composite ──
        if ($compositeScore >= 72) {
            $action = 'MUA MẠNH';
            $actionDetail = 'Tín hiệu kỹ thuật và vĩ mô đồng thuận tích cực. Ưu tiên mở vị thế mua.';
            $targetMul = 1.03;
            $stopMul = 0.975;
            $rr = '1:2.0';
            $bias = 'bullish';
        } elseif ($compositeScore >= 58) {
            $action = 'MUA';
            $actionDetail = 'Xu hướng tích cực, phù hợp tích lũy khi giá điều chỉnh về vùng hỗ trợ.';
            $targetMul = 1.025;
            $stopMul = 0.978;
            $rr = '1:1.8';
            $bias = 'bullish';
        } elseif ($compositeScore >= 45) {
            $action = 'GIỮ — Chờ tín hiệu';
            $actionDetail = 'Thị trường thiếu xu hướng rõ ràng. Không nên mở vị thế mới, giữ nguyên danh mục.';
            $targetMul = 1.015;
            $stopMul = 0.985;
            $rr = '1:1.0';
            $bias = 'neutral';
        } elseif ($compositeScore >= 30) {
            $action = 'BÁN BỚT';
            $actionDetail = 'Tín hiệu tiêu cực chiếm ưu thế. Giảm tỷ trọng, chốt lời các vị thế lãi.';
            $targetMul = 0.985;
            $stopMul = 1.015;
            $rr = '1:1.5';
            $bias = 'bearish';
        } else {
            $action = 'BÁN';
            $actionDetail = 'Áp lực bán mạnh từ nhiều yếu tố. Cắt lỗ nếu đang nắm giữ.';
            $targetMul = 0.97;
            $stopMul = 1.02;
            $rr = '1:2.0';
            $bias = 'bearish';
        }

        // ── Build dynamic summary ──
        $summaryParts = [];

        // Price trend context
        if ($sjcChange > 0.3) {
            $summaryParts[] = sprintf('Giá vàng SJC tăng %.2f%% phiên gần nhất', $sjcChange);
        } elseif ($sjcChange < -0.3) {
            $summaryParts[] = sprintf('Giá vàng SJC giảm %.2f%% phiên gần nhất', abs($sjcChange));
        } else {
            $summaryParts[] = 'Giá vàng SJC đi ngang trong phiên gần nhất';
        }

        // Macro context
        if ($macroScore >= 65) {
            $summaryParts[] = 'yếu tố vĩ mô đang hỗ trợ giá vàng (FED ôn hòa, DXY suy yếu)';
        } elseif ($macroScore <= 35) {
            $summaryParts[] = 'yếu tố vĩ mô gây bất lợi (DXY mạnh lên, kỳ vọng FED thắt chặt)';
        } else {
            $summaryParts[] = 'yếu tố vĩ mô chưa cho tín hiệu rõ ràng';
        }

        // Sentiment context
        if ($sentimentScore >= 70) {
            $summaryParts[] = 'tâm lý thị trường lạc quan (FG Index: ' . $sentimentScore . ')';
        } elseif ($sentimentScore <= 30) {
            $summaryParts[] = 'tâm lý thị trường bi quan (FG Index: ' . $sentimentScore . ')';
        } else {
            $summaryParts[] = 'tâm lý thị trường trung tính (FG Index: ' . $sentimentScore . ')';
        }

        // Risk note
        if ($bias === 'bullish') {
            $summaryParts[] = 'Rủi ro: FED thay đổi lập trường hoặc USD tăng bất ngờ';
        } elseif ($bias === 'bearish') {
            $summaryParts[] = 'Cơ hội: nếu FED phát tín hiệu cắt giảm hoặc địa chính trị leo thang';
        } else {
            $summaryParts[] = 'Rủi ro và cơ hội cân bằng, cần theo dõi sát diễn biến';
        }

        $summary = $summaryParts[0] . ', ' . implode('. ', array_slice($summaryParts, 1)) . '.';
        $summary = mb_strtoupper(mb_substr($summary, 0, 1)) . mb_substr($summary, 1);

        // ── Support zone for recommendation ──
        $support1 = number_format($price * 0.984, 1);
        $support2 = number_format($price * 0.989, 1);
        $recommendation = $action;
        if ($bias === 'bullish') {
            $recommendation .= ' — Tích lũy khi giá điều chỉnh về vùng hỗ trợ ' . $support1 . '-' . $support2 . ' triệu';
        } elseif ($bias === 'bearish') {
            $recommendation .= ' — Chốt lời vùng ' . number_format($price * 1.005, 1) . '-' . number_format($price * 1.01, 1) . ' triệu';
        }

        $target = number_format($price * $targetMul, 1);
        $stop = number_format($price * $stopMul, 1);

        return [
            'summary' => $summary,
            'recommendation' => $recommendation,
            'target' => $target . ' triệu',
            'stopLoss' => $stop . ' triệu',
            'riskReward' => $rr,
            'compositeScore' => $compositeScore,
            'bias' => $bias,
        ];
    }

    private function fallbackSnapshot(): array
    {
        return app(\App\Http\Controllers\GoldPriceController::class)->buildSnapshotFallback();
    }

    private function formatVndDeltaLabel(int $changeAmountVnd): string
    {
        if ($changeAmountVnd === 0) {
            return 'Không đổi hôm nay';
        }
        $sign = $changeAmountVnd > 0 ? '+' : '-';
        $display = number_format(abs($changeAmountVnd), 0, ',', '.');

        return sprintf('%s %sđ hôm nay', $sign, $display);
    }
}
