<?php

namespace App\Http\Controllers;

use App\Models\AnalysisArticle;
use App\Models\BaotinmanhhaiGoldPrice;
use App\Models\BtmcGoldPrice;
use App\Models\DojiGoldPrice;
use App\Models\GoldPrice;
use App\Models\MihongGoldPrice;
use App\Models\NgocthamGoldPrice;
use App\Models\PhuquyGoldPrice;
use App\Models\PnjGoldPrice;
use App\Models\Subscriber;
use App\Models\WorldPrice;
use App\Services\DashboardService;
use App\Services\SjcChartPriceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class GoldPriceController extends Controller
{
    public function __construct(private DashboardService $dashboard) {}

    public function index(): View
    {
        $snapshot = $this->dashboard->buildSnapshot();

        return view('gold.home', [
            'snapshot' => $snapshot,
            'updatedAt' => now()->format('H:i:s d/m/Y'),
            'menuTree' => config('gold_sitemap', []),
        ]);
    }

    public function showAnalysis(string $slug): View
    {
        $article = AnalysisArticle::where('slug', $slug)
            ->whereNotNull('published_at')
            ->firstOrFail();

        $recentArticles = AnalysisArticle::whereNotNull('published_at')
            ->orderByDesc('published_at')
            ->limit(10)
            ->get(['id', 'title', 'slug', 'published_at']);

        $relatedArticles = AnalysisArticle::whereNotNull('published_at')
            ->where('id', '!=', $article->id)
            ->orderByDesc('published_at')
            ->limit(6)
            ->get(['id', 'title', 'slug', 'thumbnail_path', 'word_count', 'published_at']);

        return view('gold.analysis-show', [
            'article' => $article,
            'recentArticles' => $recentArticles,
            'relatedArticles' => $relatedArticles,
            'snapshot' => $this->dashboard->buildSnapshot(),
            'title' => $article->title,
            'description' => $article->summary ?? 'Phân tích giá vàng chi tiết',
            'path' => 'tin-tuc-gia-vang/trong-nuoc/' . $article->slug,
            'breadcrumbs' => [
                ['path' => 'tin-tuc-gia-vang', 'title' => 'Tin tức giá vàng'],
                ['path' => 'tin-tuc-gia-vang/trong-nuoc', 'title' => 'Tin tức giá vàng trong nước'],
                ['path' => 'tin-tuc-gia-vang/trong-nuoc/' . $article->slug, 'title' => $article->title],
            ],
            'menuTree' => config('gold_sitemap', []),
            'currentPath' => '',
            'children' => [],
        ]);
    }

    public function analysisByTag(string $tagSlug): View
    {
        $tagMap = [
            'gia-vang' => 'giá vàng',
            'phan-tich-gia-vang' => 'phân tích giá vàng',
            'ban-tin-gia-vang' => 'bản tin giá vàng',
            'bien-dong-gia-vang' => 'biến động giá vàng',
            'gia-vang-sjc' => 'giá vàng SJC',
            'gia-vang-doji' => 'giá vàng DOJI',
            'gia-vang-pnj' => 'giá vàng PNJ',
            'gia-vang-phu-quy' => 'giá vàng Phú Quý',
            'gia-vang-mi-hong' => 'giá vàng Mi Hồng',
            'gia-vang-btmc' => 'giá vàng BTMC',
            'gia-vang-the-gioi' => 'giá vàng thế giới',
            'xau-usd' => 'XAU/USD',
            'xauusd' => 'XAU/USD',
            'trung-lap' => 'trung lập',
        ];

        $tagName = $tagMap[$tagSlug] ?? str_replace('-', ' ', $tagSlug);

        $articles = AnalysisArticle::whereNotNull('published_at')
            ->whereRaw('JSON_CONTAINS(tags, ?)', [json_encode($tagName)])
            ->orderByDesc('published_at')
            ->paginate(12);

        return view('gold.analysis-tag', [
            'tag' => $tagName,
            'tagSlug' => $tagSlug,
            'articles' => $articles,
            'snapshot' => $this->dashboard->buildSnapshot(),
            'title' => "Bài viết về: {$tagName}",
            'description' => "Tổng hợp bài phân tích giá vàng theo chủ đề {$tagName}",
            'path' => 'tin-tuc-gia-vang/trong-nuoc/tag/' . $tagSlug,
            'breadcrumbs' => [
                ['path' => 'tin-tuc-gia-vang', 'title' => 'Tin tức giá vàng'],
                ['path' => 'tin-tuc-gia-vang/trong-nuoc', 'title' => 'Tin tức giá vàng trong nước'],
                ['path' => 'tin-tuc-gia-vang/trong-nuoc/tag/' . $tagSlug, 'title' => $tagName],
            ],
            'menuTree' => config('gold_sitemap', []),
            'currentPath' => '',
            'children' => [],
        ]);
    }

    public function snapshot(): JsonResponse
    {
        return response()->json([
            'snapshot' => $this->dashboard->buildSnapshot(),
            'updatedAt' => now()->format('H:i:s d/m/Y'),
        ]);
    }

    public function subscribe(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:120'],
            'name' => ['nullable', 'string', 'max:80'],
            'markets' => ['nullable', 'array'],
            'markets.*' => ['string', 'in:us,sjc,24k,9999'],
        ]);

        Subscriber::updateOrCreate(
            ['email' => $validated['email']],
            [
                'name' => $validated['name'] ?? 'Khách hàng',
                'markets' => $validated['markets'] ?? ['us', 'sjc'],
                'active' => true,
            ]
        );

        return response()->json([
            'message' => 'Đăng ký nhận báo giá thành công.',
            'subscription' => [
                'email' => $validated['email'],
                'name' => $validated['name'] ?? 'Khách hàng',
                'markets' => $validated['markets'] ?? ['us', 'sjc'],
            ],
        ]);
    }

    public function sjcChart(Request $request, SjcChartPriceService $sjcService): JsonResponse
    {
        $period = $request->query('period', '1y');

        $days = match ($period) {
            '7d'  => 7,
            '1m'  => 30,
            '3m'  => 90,
            '6m'  => 180,
            'ytd' => (int) now()->diffInDays(now()->startOfYear()),
            '1y'  => 365,
            'all' => 99999,
            default => 365,
        };

        return response()->json($sjcService->getChartData($days));
    }

    public function worldChart(Request $request): JsonResponse
    {
        $allowedSymbols = ['XAU/USD','XAU/EUR','XAU/GBP','XAU/CNY','XAU/JPY','XAG/USD','XPT/USD','XPD/USD'];
        $symbol = $request->query('symbol', 'XAU/USD');
        if (!in_array($symbol, $allowedSymbols, true)) {
            return response()->json(['error' => 'Invalid symbol'], 422);
        }

        $period = $request->query('period', '1m');
        $days = match ($period) {
            '7d'  => 7,
            '1m'  => 30,
            '3m'  => 90,
            '6m'  => 180,
            '1y'  => 365,
            'all' => 99999,
            default => 30,
        };

        $rows = DB::table('world_prices')
            ->selectRaw('DATE(created_at) as d, AVG(price) as avg_price, MAX(price) as high, MIN(price) as low')
            ->where('symbol', $symbol)
            ->where('price', '>', 0)
            ->where('created_at', '>=', now()->subDays($days))
            ->groupByRaw('DATE(created_at)')
            ->orderBy('d')
            ->get();

        return response()->json($rows->map(fn($r) => [
            'date' => $r->d,
            'avg'  => round((float) $r->avg_price, 2),
            'high' => round((float) $r->high, 2),
            'low'  => round((float) $r->low, 2),
        ]));
    }

    public function priceFeed(): JsonResponse
    {
        return response()->json($this->dashboard->buildPriceFeed());
    }

    public function brandChart(Request $request): JsonResponse
    {
        $allowedBrands = ['sjc', 'doji', 'pnj', 'btmc', 'phuquy', 'mihong', 'btmh', 'ngoctham'];
        $brand = $request->query('brand', 'sjc');
        if (!in_array($brand, $allowedBrands, true)) {
            return response()->json(['error' => 'Invalid brand'], 422);
        }

        $period = $request->query('period', '1m');
        $days = match ($period) {
            '7d'  => 7,
            '1m'  => 30,
            '3m'  => 90,
            '6m'  => 180,
            default => 30,
        };

        $configs = [
            'sjc'      => ['table' => 'gold_prices',               'where' => ['brand', 'like', 'Vàng SJC 1L%']],
            'doji'     => ['table' => 'doji_gold_prices',           'where' => ['brand', 'like', 'SJC%Bán Lẻ%']],
            'pnj'      => ['table' => 'pnj_gold_prices',           'where' => ['brand', '=', 'Vàng miếng SJC 999.9']],
            'btmc'     => ['table' => 'btmc_gold_prices',           'where' => ['brand', '=', 'Vàng Miếng SJC']],
            'phuquy'   => ['table' => 'phuquy_gold_prices',        'where' => ['brand', '=', 'Vàng miếng SJC']],
            'mihong'   => ['table' => 'mihong_gold_prices',         'where' => ['brand', '=', 'SJC']],
            'btmh'     => ['table' => 'baotinmanhhai_gold_prices',  'where' => ['brand', 'like', 'Vàng miếng SJC%']],
            'ngoctham' => ['table' => 'ngoctham_gold_prices',       'where' => ['brand', 'like', 'Vàng Miếng SJC%']],
        ];

        $cfg = $configs[$brand];
        $startDate = now()->subDays($days)->startOfDay();

        // Get all rows ordered by updated_at, pick last buy+sell per day
        $rows = DB::table($cfg['table'])
            ->where('sell_price', '>', 0)
            ->where('updated_at', '>=', $startDate)
            ->where($cfg['where'][0], $cfg['where'][1], $cfg['where'][2])
            ->orderBy('updated_at')
            ->get(['buy_price', 'sell_price', 'updated_at']);

        $daily = [];
        foreach ($rows as $row) {
            $date = substr($row->updated_at, 0, 10);
            $buy  = (float) $row->buy_price;
            $sell = (float) $row->sell_price;

            // Normalize per-chỉ to per-lượng
            if ($buy > 0 && $buy < 100_000_000) $buy *= 10;
            if ($sell > 0 && $sell < 100_000_000) $sell *= 10;

            // Overwrite with latest row each day
            $daily[$date] = ['date' => $date, 'buy' => $buy, 'sell' => $sell];
        }

        $result = [];
        foreach ($daily as $item) {
            $result[] = [
                'date' => $item['date'],
                'buy'  => round($item['buy'] / 1_000_000, 2),
                'sell' => round($item['sell'] / 1_000_000, 2),
            ];
        }

        return response()->json($result);
    }

    public function allBrandsChart(Request $request): JsonResponse
    {
        $period = $request->query('period', '30d');
        $isToday = $period === 'today';

        $days = match ($period) {
            'today' => 1,
            '7d'    => 7,
            '30d'   => 30,
            '1y'    => 365,
            '10y'   => 3650,
            default => 30,
        };

        $configs = [
            'SJC'       => ['table' => 'gold_prices',               'where' => ['brand', 'like', 'Vàng SJC 1L%']],
            'DOJI'      => ['table' => 'doji_gold_prices',          'where' => ['brand', 'like', 'SJC%Bán Lẻ%']],
            'PNJ'       => ['table' => 'pnj_gold_prices',           'where' => ['brand', '=', 'Vàng miếng SJC 999.9']],
            'BTMC'      => ['table' => 'btmc_gold_prices',          'where' => ['brand', '=', 'Vàng Miếng SJC']],
            'Phú Quý'   => ['table' => 'phuquy_gold_prices',       'where' => ['brand', '=', 'Vàng miếng SJC']],
            'Mi Hồng'   => ['table' => 'mihong_gold_prices',        'where' => ['brand', '=', 'SJC']],
            'Bảo Tín MH' => ['table' => 'baotinmanhhai_gold_prices', 'where' => ['brand', 'like', 'Vàng miếng SJC%']],
            'Ngọc Thẩm' => ['table' => 'ngoctham_gold_prices',      'where' => ['brand', 'like', 'Vàng Miếng SJC%']],
        ];

        $startDate = $isToday ? now()->startOfDay() : now()->subDays($days)->startOfDay();
        $brandLabels = array_keys($configs);

        // XAU/USD → VND/lượng conversion rate
        $usdVnd = DB::table('exchange_rates')
            ->where('pair', 'USD/VND')
            ->orderByDesc('updated_at')
            ->value('rate') ?? 26300;

        $xauLabel = 'XAU quy đổi';

        if ($isToday) {
            // Intraday: each crawl timestamp as a data point
            $timeData = []; // [timestamp_str][brandLabel] = sell (triệu/lượng)

            foreach ($configs as $label => $cfg) {
                $rows = DB::table($cfg['table'])
                    ->where($cfg['where'][0], $cfg['where'][1], $cfg['where'][2])
                    ->where('sell_price', '>', 0)
                    ->where('updated_at', '>=', $startDate)
                    ->select('buy_price', 'sell_price', 'updated_at')
                    ->orderBy('updated_at')
                    ->get()
                    ->unique(fn($r) => substr($r->updated_at, 0, 16)); // unique per minute

                foreach ($rows as $row) {
                    $buy  = (float) $row->buy_price;
                    $sell = (float) $row->sell_price;
                    if ($buy > 0 && $buy < 100_000_000) $buy *= 10;
                    if ($sell > 0 && $sell < 100_000_000) $sell *= 10;
                    $ts = substr($row->updated_at, 0, 16); // "Y-m-d H:i"
                    $timeData[$ts][$label] = round($sell / 1_000_000, 2);
                    $timeData[$ts][$label . '_buy'] = round($buy / 1_000_000, 2);
                }
            }

            // XAU/USD intraday data
            $worldRows = DB::table('world_prices')
                ->where('symbol', 'XAU/USD')
                ->where('created_at', '>=', $startDate)
                ->orderBy('created_at')
                ->select('price', 'created_at')
                ->get();
            foreach ($worldRows as $row) {
                $ts = substr($row->created_at, 0, 16);
                $vndPerLuong = (float) $row->price * 37.5 / 31.1035 * $usdVnd;
                $timeData[$ts][$xauLabel] = round($vndPerLuong / 1_000_000, 2);
            }

            if (empty($timeData)) {
                return response()->json([]);
            }

            ksort($timeData);
            $allKeys = [];
            foreach ($brandLabels as $label) {
                $allKeys[] = $label;
                $allKeys[] = $label . '_buy';
            }
            $allKeys[] = $xauLabel;
            $lastKnown = array_fill_keys($allKeys, null);
            $result = [];

            foreach ($timeData as $ts => $values) {
                foreach ($allKeys as $k) {
                    if (isset($values[$k])) {
                        $lastKnown[$k] = $values[$k];
                    }
                }
                $entry = ['date' => $ts . ':00'];
                foreach ($allKeys as $k) {
                    $entry[$k] = $lastKnown[$k];
                }
                $result[] = $entry;
            }

            return response()->json($result);
        }

        // Daily: use the latest buy+sell price per day
        $dailyData = [];

        foreach ($configs as $label => $cfg) {
            $rows = DB::table($cfg['table'])
                ->where($cfg['where'][0], $cfg['where'][1], $cfg['where'][2])
                ->where('sell_price', '>', 0)
                ->where('updated_at', '>=', $startDate)
                ->select('buy_price', 'sell_price', 'updated_at')
                ->orderBy('updated_at')
                ->get();

            foreach ($rows as $row) {
                $date = substr($row->updated_at, 0, 10);
                $buy  = (float) $row->buy_price;
                $sell = (float) $row->sell_price;
                if ($buy > 0 && $buy < 100_000_000) $buy *= 10;
                if ($sell > 0 && $sell < 100_000_000) $sell *= 10;
                // Overwrite with latest value for that day
                $dailyData[$date][$label] = round($sell / 1_000_000, 2);
                $dailyData[$date][$label . '_buy'] = round($buy / 1_000_000, 2);
            }
        }

        // XAU/USD daily data
        $worldRows = DB::table('world_prices')
            ->where('symbol', 'XAU/USD')
            ->where('created_at', '>=', $startDate)
            ->orderBy('created_at')
            ->select('price', 'created_at')
            ->get();
        foreach ($worldRows as $row) {
            $date = substr($row->created_at, 0, 10);
            $vndPerLuong = (float) $row->price * 37.5 / 31.1035 * $usdVnd;
            $dailyData[$date][$xauLabel] = round($vndPerLuong / 1_000_000, 2);
        }

        if (empty($dailyData)) {
            return response()->json([]);
        }

        ksort($dailyData);
        $allKeys = [];
        foreach ($brandLabels as $label) {
            $allKeys[] = $label;
            $allKeys[] = $label . '_buy';
        }
        $allKeys[] = $xauLabel;
        $lastKnown = array_fill_keys($allKeys, null);
        $result = [];

        foreach ($dailyData as $date => $values) {
            foreach ($allKeys as $k) {
                if (isset($values[$k])) {
                    $lastKnown[$k] = $values[$k];
                }
            }
            $entry = ['date' => $date];
            foreach ($allKeys as $k) {
                $entry[$k] = $lastKnown[$k];
            }
            $result[] = $entry;
        }

        return response()->json($result);
    }

    public function pricesByDate(Request $request): JsonResponse
    {
        $date = $request->query('date', now()->toDateString());

        // Validate date format
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return response()->json(['error' => 'Invalid date format'], 422);
        }

        // World prices for the date (fallback to nearest previous day within 3 days)
        $world = collect();
        for ($i = 0; $i <= 3 && $world->isEmpty(); $i++) {
            $lookupDate = date('Y-m-d', strtotime($date . " -{$i} days"));
            $world = WorldPrice::whereDate('created_at', $lookupDate)
                ->get()
                ->sortByDesc('id')
                ->unique('symbol')
                ->map(fn($w) => [
                    'symbol' => $w->symbol,
                    'price' => (float) $w->price,
                    'change_percent' => (float) ($w->change_percent ?? 0),
                ])
                ->values();
        }

        // Brand data loaders
        $brandConfigs = [
            'sjc' => ['model' => GoldPrice::class, 'table' => 'gold_prices', 'scope' => fn($q) => $q->where('source', 'sjc'), 'fields' => ['brand', 'region', 'buy_price', 'sell_price', 'change_percent']],
            'btmc' => ['model' => BtmcGoldPrice::class, 'table' => 'btmc_gold_prices', 'fields' => ['brand', 'buy_price', 'sell_price', 'change_percent']],
            'pnj' => ['model' => PnjGoldPrice::class, 'table' => 'pnj_gold_prices', 'fields' => ['brand', 'zone', 'buy_price', 'sell_price', 'change_percent']],
            'doji' => ['model' => DojiGoldPrice::class, 'table' => 'doji_gold_prices', 'fields' => ['brand', 'buy_price', 'sell_price', 'change_percent']],
            'phuquy' => ['model' => PhuquyGoldPrice::class, 'table' => 'phuquy_gold_prices', 'fields' => ['brand', 'buy_price', 'sell_price', 'change_percent']],
            'mihong' => ['model' => MihongGoldPrice::class, 'table' => 'mihong_gold_prices', 'fields' => ['brand', 'buy_price', 'sell_price', 'change_percent']],
            'baotinmanhhai' => ['model' => BaotinmanhhaiGoldPrice::class, 'table' => 'baotinmanhhai_gold_prices', 'fields' => ['brand', 'buy_price', 'sell_price', 'change_percent']],
            'ngoctham' => ['model' => NgocthamGoldPrice::class, 'table' => 'ngoctham_gold_prices', 'fields' => ['brand', 'buy_price', 'sell_price', 'change_percent']],
        ];

        $brands = [];
        foreach ($brandConfigs as $key => $config) {
            // Try requested date first, fallback to nearest previous day within 3 days
            $unique = collect();
            for ($i = 0; $i <= 3 && $unique->isEmpty(); $i++) {
                $lookupDate = date('Y-m-d', strtotime($date . " -{$i} days"));
                $query = $config['model']::whereDate('created_at', $lookupDate);
                if (isset($config['scope'])) {
                    $config['scope']($query);
                }
                $items = $query->get()->sortByDesc('id');
                $unique = $items->unique('brand')->filter(fn($p) => $p->sell_price > 0)->values();
            }

            $brands[$key] = $unique->map(function ($item) use ($config) {
                $row = [];
                foreach ($config['fields'] as $f) {
                    $row[$f] = $item->$f;
                }
                return $row;
            })->values();
        }

        // Week series helper (7 days ending on $date)
        $weekSeriesForDate = function (string $table, string $column, string $targetDate, ?callable $scope = null, float $divisor = 1_000_000) {
            $query = DB::table($table)
                ->selectRaw("DATE(created_at) as d, MAX({$column}) as p")
                ->whereDate('created_at', '<=', $targetDate)
                ->whereDate('created_at', '>=', date('Y-m-d', strtotime($targetDate . ' -6 days')));
            if ($scope) $scope($query);
            return $query->groupByRaw('DATE(created_at)')
                ->orderBy('d')
                ->pluck('p', 'd')
                ->values()
                ->map(fn($v) => round((float) $v / $divisor, 2))
                ->toArray();
        };

        $weekSeriesBuy = function (string $table, string $targetDate, ?callable $scope = null) use ($weekSeriesForDate) {
            return $weekSeriesForDate($table, 'buy_price', $targetDate, $scope);
        };

        $result = [
            'date' => $date,
            'world' => $world,
            'brands' => $brands,
            'usWeekPoints' => $weekSeriesForDate('world_prices', 'price', $date, fn($q) => $q->where('symbol', 'XAU/USD'), 1),
            'sjcWeekSellPoints' => $weekSeriesForDate('gold_prices', 'sell_price', $date, fn($q) => $q->where('source', 'sjc')),
            'sjcWeekBuyPoints' => $weekSeriesBuy('gold_prices', $date, fn($q) => $q->where('source', 'sjc')),
            'sjcWeekPoints' => $weekSeriesForDate('gold_prices', 'sell_price', $date, fn($q) => $q->where('source', 'sjc')),
            'btmcWeekSellPoints' => $weekSeriesForDate('btmc_gold_prices', 'sell_price', $date),
            'btmcWeekBuyPoints' => $weekSeriesBuy('btmc_gold_prices', $date),
            'btmcWeekPoints' => $weekSeriesForDate('btmc_gold_prices', 'sell_price', $date),
            'pnjWeekSellPoints' => $weekSeriesForDate('pnj_gold_prices', 'sell_price', $date),
            'pnjWeekBuyPoints' => $weekSeriesBuy('pnj_gold_prices', $date),
            'pnjWeekPoints' => $weekSeriesForDate('pnj_gold_prices', 'sell_price', $date),
            'dojiWeekSellPoints' => $weekSeriesForDate('doji_gold_prices', 'sell_price', $date),
            'dojiWeekBuyPoints' => $weekSeriesBuy('doji_gold_prices', $date),
            'phuquyWeekSellPoints' => $weekSeriesForDate('phuquy_gold_prices', 'sell_price', $date),
            'phuquyWeekBuyPoints' => $weekSeriesBuy('phuquy_gold_prices', $date),
            'mihongWeekSellPoints' => $weekSeriesForDate('mihong_gold_prices', 'sell_price', $date),
            'mihongWeekBuyPoints' => $weekSeriesBuy('mihong_gold_prices', $date),
            'btmhWeekSellPoints' => $weekSeriesForDate('baotinmanhhai_gold_prices', 'sell_price', $date),
            'btmhWeekBuyPoints' => $weekSeriesBuy('baotinmanhhai_gold_prices', $date),
            'ngocthamWeekSellPoints' => $weekSeriesForDate('ngoctham_gold_prices', 'sell_price', $date),
            'ngocthamWeekBuyPoints' => $weekSeriesBuy('ngoctham_gold_prices', $date),
        ];

        return response()->json($result);
    }

    public function buildSnapshotFallback(): array
    {
        $usBase = 2918.5 + $this->jitter(15) / 10;
        $sjcBase = 92.2 + $this->jitter(8) / 10;

        $topBrands = [
            ['brand' => 'SJC 1L', 'buy' => 91500000, 'sell' => 92500000, 'change' => 0.54],
            ['brand' => 'DOJI 9999', 'buy' => 91400000, 'sell' => 92400000, 'change' => 0.47],
            ['brand' => 'PNJ 9999', 'buy' => 91300000, 'sell' => 92300000, 'change' => 0.35],
            ['brand' => 'Bảo Tín Minh Châu', 'buy' => 91350000, 'sell' => 92350000, 'change' => 0.42],
            ['brand' => 'Phú Quý 9999', 'buy' => 91200000, 'sell' => 92200000, 'change' => -0.12],
            ['brand' => 'Mi Hong', 'buy' => 91100000, 'sell' => 92100000, 'change' => 0.18],
            ['brand' => 'Nhan tron 9999', 'buy' => 82500000, 'sell' => 83600000, 'change' => 0.31],
            ['brand' => 'Vàng 24K', 'buy' => 82300000, 'sell' => 83400000, 'change' => 0.25],
        ];

        foreach ($topBrands as &$item) {
            $delta = $this->jitter(7) * 10000;
            $item['buy'] += $delta;
            $item['sell'] += $delta;
            $item['change'] = round($item['change'] + $this->jitter(6) / 100, 2);
        }

        return [
            'usCard' => [
                'title' => 'Giá Vàng Thế Giới (XAU/USD)',
                'trendPercent' => round(0.42 + $this->jitter(15) / 100, 2),
                'variants' => [
                    'spot' => [
                        'label' => 'Giá Spot',
                        'price' => $usBase,
                        'unit' => 'USD/Ounce',
                        'dayChangeLabel' => sprintf('%+.2f hôm nay', 12.30 + $this->jitter(12) / 10),
                    ],
                    'future' => [
                        'label' => 'Giá Future (COMEX)',
                        'price' => $usBase + 4.35,
                        'unit' => 'USD/Ounce',
                        'dayChangeLabel' => sprintf('%+.2f hôm nay', 14.10 + $this->jitter(10) / 10),
                    ],
                    'london' => [
                        'label' => 'London Fix PM',
                        'price' => $usBase - 3.5,
                        'unit' => 'USD/Ounce',
                        'dayChangeLabel' => sprintf('%+.2f hôm nay', 10.50 + $this->jitter(9) / 10),
                    ],
                ],
                'selected' => 'spot',
                'weekPoints' => [2895, 2901, 2908, 2898, 2912, 2906, (int) $usBase],
            ],
            'sjcCard' => [
                'title' => 'Giá Vàng SJC',
                'trendPercent' => round(0.54 + $this->jitter(10) / 100, 2),
                'variants' => [
                    'hn' => [
                        'label' => 'SJC Hà Nội',
                        'price' => $sjcBase,
                        'buy' => 91.50 + $this->jitter(9) / 100,
                        'sell' => 92.50 + $this->jitter(9) / 100,
                        'unit' => 'Triệu đồng/Lượng',
                        'dayChangeLabel' => sprintf('%+dk hôm nay', 500 + $this->jitter(30) * 10),
                    ],
                    'hcm' => [
                        'label' => 'SJC TP.HCM',
                        'price' => $sjcBase + 0.08,
                        'buy' => 91.55 + $this->jitter(9) / 100,
                        'sell' => 92.55 + $this->jitter(9) / 100,
                        'unit' => 'Triệu đồng/Lượng',
                        'dayChangeLabel' => sprintf('%+dk hôm nay', 520 + $this->jitter(30) * 10),
                    ],
                    'dn' => [
                        'label' => 'SJC Đà Nẵng',
                        'price' => $sjcBase - 0.05,
                        'buy' => 91.45 + $this->jitter(9) / 100,
                        'sell' => 92.45 + $this->jitter(9) / 100,
                        'unit' => 'Triệu đồng/Lượng',
                        'dayChangeLabel' => sprintf('%+dk hôm nay', 480 + $this->jitter(30) * 10),
                    ],
                ],
                'selected' => 'hn',
                'weekPoints' => [91.2, 91.5, 91.8, 91.4, 92.0, 91.9, $sjcBase],
            ],
            'btmcCard' => [
                'title' => 'Giá Vàng Bảo Tín Minh Châu',
                'trendPercent' => round(0.42 + $this->jitter(10) / 100, 2),
                'variants' => [
                    'p1' => [
                        'label' => 'Vang mieng SJC',
                        'price' => $sjcBase - 0.1,
                        'buy' => 91.40 + $this->jitter(9) / 100,
                        'sell' => 92.40 + $this->jitter(9) / 100,
                        'unit' => 'Triệu đồng/Lượng',
                        'dayChangeLabel' => sprintf('%+dk hôm nay', 350 + $this->jitter(25) * 10),
                    ],
                    'p2' => [
                        'label' => 'Vang nhan 9999',
                        'price' => $sjcBase - 10.0,
                        'buy' => 81.50 + $this->jitter(9) / 100,
                        'sell' => 82.50 + $this->jitter(9) / 100,
                        'unit' => 'Triệu đồng/Lượng',
                        'dayChangeLabel' => sprintf('%+dk hôm nay', 200 + $this->jitter(20) * 10),
                    ],
                    'p3' => [
                        'label' => 'Vàng 24K',
                        'price' => $sjcBase - 10.5,
                        'buy' => 81.00 + $this->jitter(9) / 100,
                        'sell' => 82.00 + $this->jitter(9) / 100,
                        'unit' => 'Triệu đồng/Lượng',
                        'dayChangeLabel' => sprintf('%+dk hôm nay', 180 + $this->jitter(20) * 10),
                    ],
                ],
                'selected' => 'p1',
                'weekPoints' => [91.5, 91.7, 91.9, 91.6, 92.0, 91.8, round($sjcBase - 0.1, 1)],
            ],
            'pnjCard' => [
                'title' => 'Giá Vàng PNJ',
                'trendPercent' => round(0.35 + $this->jitter(10) / 100, 2),
                'variants' => [
                    'hn' => [
                        'label' => 'PNJ Hà Nội',
                        'price' => $sjcBase - 0.2,
                        'buy' => 91.30 + $this->jitter(9) / 100,
                        'sell' => 92.30 + $this->jitter(9) / 100,
                        'unit' => 'Triệu đồng/Lượng',
                        'dayChangeLabel' => sprintf('%+dk hôm nay', 300 + $this->jitter(25) * 10),
                    ],
                    'hcm' => [
                        'label' => 'PNJ TP.HCM',
                        'price' => $sjcBase - 0.15,
                        'buy' => 91.35 + $this->jitter(9) / 100,
                        'sell' => 92.35 + $this->jitter(9) / 100,
                        'unit' => 'Triệu đồng/Lượng',
                        'dayChangeLabel' => sprintf('%+dk hôm nay', 320 + $this->jitter(25) * 10),
                    ],
                    'dn' => [
                        'label' => 'PNJ Đà Nẵng',
                        'price' => $sjcBase - 0.25,
                        'buy' => 91.25 + $this->jitter(9) / 100,
                        'sell' => 92.25 + $this->jitter(9) / 100,
                        'unit' => 'Triệu đồng/Lượng',
                        'dayChangeLabel' => sprintf('%+dk hôm nay', 280 + $this->jitter(25) * 10),
                    ],
                ],
                'selected' => 'hn',
                'weekPoints' => [91.3, 91.5, 91.7, 91.4, 91.8, 91.7, round($sjcBase - 0.2, 1)],
            ],
            'dojiCard' => [
                'title' => 'Giá Vàng DOJI',
                'trendPercent' => round(0.47 + $this->jitter(10) / 100, 2),
                'variants' => ['p0' => ['label' => 'DOJI 9999', 'price' => $sjcBase - 0.1, 'buy' => $sjcBase - 1.1, 'sell' => $sjcBase - 0.1, 'unit' => 'Triệu đồng/Lượng', 'dayChangeLabel' => '+350k hôm nay']],
                'selected' => 'p0',
                'weekPoints' => [91.2, 91.4, 91.6, 91.3, 91.7, 91.6, round($sjcBase - 0.1, 1)],
            ],
            'phuquyCard' => [
                'title' => 'Giá Vàng Phú Quý',
                'trendPercent' => round(-0.12 + $this->jitter(10) / 100, 2),
                'variants' => ['p0' => ['label' => 'Phú Quý 9999', 'price' => $sjcBase - 0.3, 'buy' => $sjcBase - 1.3, 'sell' => $sjcBase - 0.3, 'unit' => 'Triệu đồng/Lượng', 'dayChangeLabel' => '+200k hôm nay']],
                'selected' => 'p0',
                'weekPoints' => [91.0, 91.2, 91.4, 91.1, 91.5, 91.4, round($sjcBase - 0.3, 1)],
            ],
            'mihongCard' => [
                'title' => 'Giá Vàng Mi Hồng',
                'trendPercent' => round(0.18 + $this->jitter(10) / 100, 2),
                'variants' => ['p0' => ['label' => 'Mi Hồng 9999', 'price' => $sjcBase - 0.4, 'buy' => $sjcBase - 1.4, 'sell' => $sjcBase - 0.4, 'unit' => 'Triệu đồng/Lượng', 'dayChangeLabel' => '+150k hôm nay']],
                'selected' => 'p0',
                'weekPoints' => [90.9, 91.1, 91.3, 91.0, 91.4, 91.3, round($sjcBase - 0.4, 1)],
            ],
            'btmhCard' => [
                'title' => 'Giá Vàng Bảo Tín Mạnh Hải',
                'trendPercent' => round(0.30 + $this->jitter(10) / 100, 2),
                'variants' => ['p0' => ['label' => 'Bảo Tín Mạnh Hải 9999', 'price' => $sjcBase - 0.2, 'buy' => $sjcBase - 1.2, 'sell' => $sjcBase - 0.2, 'unit' => 'Triệu đồng/Lượng', 'dayChangeLabel' => '+250k hôm nay']],
                'selected' => 'p0',
                'weekPoints' => [91.1, 91.3, 91.5, 91.2, 91.6, 91.5, round($sjcBase - 0.2, 1)],
            ],
            'ngocthamCard' => [
                'title' => 'Giá Vàng Ngọc Thẩm',
                'trendPercent' => round(0.22 + $this->jitter(10) / 100, 2),
                'variants' => ['p0' => ['label' => 'Ngọc Thẩm 9999', 'price' => $sjcBase - 0.5, 'buy' => $sjcBase - 1.5, 'sell' => $sjcBase - 0.5, 'unit' => 'Triệu đồng/Lượng', 'dayChangeLabel' => '+100k hôm nay']],
                'selected' => 'p0',
                'weekPoints' => [90.8, 91.0, 91.2, 90.9, 91.3, 91.2, round($sjcBase - 0.5, 1)],
            ],
            'statCards' => [
                ['title' => 'Chênh lệch Mua-Bán SJC', 'value' => '1.0tr', 'unit' => 'VND/Lượng', 'delta' => '+100k vs hôm qua', 'trend' => 'up'],
                ['title' => 'Khối lượng GD trong nước', 'value' => '3,250', 'unit' => 'Lượng/ngày', 'delta' => '+15.2%', 'trend' => 'up'],
                ['title' => 'Biến động 24h', 'value' => '0.54%', 'unit' => 'Tăng', 'delta' => 'Cao hơn trung bình', 'trend' => 'up'],
                ['title' => 'Tỷ giá USD/VND', 'value' => '25,450', 'unit' => 'VND', 'delta' => '+0.08%', 'trend' => 'up'],
                ['title' => 'Chỉ số DXY', 'value' => '103.42', 'unit' => 'USD Index', 'delta' => '-0.31%', 'trend' => 'down'],
                ['title' => 'Lạm phát Mỹ (CPI)', 'value' => '2.8%', 'unit' => 'YoY tháng 2/2026', 'delta' => '-0.1% vs tháng trước', 'trend' => 'down'],
            ],
            'sentiment' => [
                'fearGreedIndex' => 68,
                'fearGreedLabel' => 'Tham lam',
                'buyPercent' => 68.0,
                'neutralPercent' => 12.0,
                'sellPercent' => 20.0,
                'trendLabel' => 'Tích cực mạnh',
                'trendDirection' => 'up',
                'scores' => [
                    'priceTrend' => 72.5,
                    'consensus' => 75.0,
                    'momentum' => 62.3,
                    'spread' => 58.0,
                ],
            ],
            'technical' => [
                ['name' => 'RSI (14)', 'value' => '64.8', 'signal' => 'neutral'],
                ['name' => 'MACD (12,26,9)', 'value' => '+5.32', 'signal' => 'buy'],
                ['name' => 'Stochastic (14,3)', 'value' => '71.2', 'signal' => 'neutral'],
                ['name' => 'ADX (14)', 'value' => '28.6', 'signal' => 'buy'],
                ['name' => 'EMA 20', 'value' => '91.8tr', 'signal' => 'buy'],
                ['name' => 'EMA 50', 'value' => '90.5tr', 'signal' => 'buy'],
                ['name' => 'SMA 200', 'value' => '85.2tr', 'signal' => 'buy'],
                ['name' => 'Bollinger (20,2)', 'value' => '89.5-93.8', 'signal' => 'neutral'],
                ['name' => 'Fibonacci 61.8%', 'value' => '91.2tr', 'signal' => 'buy'],
                ['name' => 'Kết luận', 'value' => 'MUA MANH', 'signal' => 'buy'],
            ],
            'actions' => [
                'Xem phân tích chi tiết',
                'Công cụ quy đổi',
                'Dự báo giá vàng',
                'Thiết lập cảnh báo',
            ],
            'topBrands' => $topBrands,
            'chart24h' => [
                'labels' => ['00:00', '04:00', '08:00', '10:00', '12:00', '14:00', '16:00', '18:00', '20:00', '22:00'],
                'sjc' => [183.5, 183.6, 183.8, 184.0, 184.1, 184.3, 184.2, 184.4, 184.5, 184.3],
                'v24k' => [132.3, 132.4, 132.5, 132.8, 132.9, 133.0, 132.9, 133.2, 133.4, 133.3],
                'v18k' => [99.5, 99.6, 99.8, 99.9, 100.0, 100.1, 100.0, 100.2, 100.3, 100.2],
            ],
            'news' => [
                ['tag' => 'Nóng', 'title' => 'FED giữ nguyên lãi suất 4.25-4.50%, vàng tăng mạnh phiên chiều', 'time' => '5 phút trước', 'impact' => 'positive'],
                ['tag' => 'Phân tích', 'title' => 'Chỉ số DXY giảm mạnh xuống 103.4, hỗ trợ giá vàng vượt đỉnh', 'time' => '18 phút trước', 'impact' => 'positive'],
                ['tag' => 'Quốc tế', 'title' => 'NHTW Trung Quốc mua ròng 15 tấn vàng trong tháng 2/2026', 'time' => '45 phút trước', 'impact' => 'positive'],
                ['tag' => 'Dự báo', 'title' => 'Goldman Sachs nâng mục tiêu XAU/USD lên 3,100 cuối 2026', 'time' => '1 giờ trước', 'impact' => 'positive'],
                ['tag' => 'Trong nước', 'title' => 'NHNN điều chỉnh giá bán vàng SJC tăng 500.000đ/lượng', 'time' => '2 giờ trước', 'impact' => 'neutral'],
                ['tag' => 'Vĩ mô', 'title' => 'Lạm phát Eurozone tăng 2.4% bất ngờ, nhà đầu tư tìm đến vàng', 'time' => '3 giờ trước', 'impact' => 'positive'],
            ],
            'comparisons' => [
                ['title' => 'Vàng SJC vs Thế giới', 'subtitle' => 'Chênh lệch quy đổi', 'value' => '+3.8tr', 'note' => 'cao hơn'],
                ['title' => 'SJC vs DOJI', 'subtitle' => 'Chênh lệch giá bán', 'value' => '+100k', 'note' => 'cao hơn'],
                ['title' => 'SJC vs PNJ', 'subtitle' => 'Chênh lệch giá bán', 'value' => '+200k', 'note' => 'cao hơn'],
                ['title' => 'Vàng 24K vs 18K', 'subtitle' => 'Tỷ lệ giá', 'value' => '1.34x', 'note' => 'chênh lệch'],
                ['title' => 'Vàng vs BĐS', 'subtitle' => 'Hiệu suất 1 năm', 'value' => '+18.5% vs +5.2%', 'note' => 'vàng vượt trội'],
            ],
            'performance' => [
                ['period' => '1 ngày', 'percent' => '+0.54%', 'profit' => '+500k'],
                ['period' => '7 ngày', 'percent' => '+1.8%', 'profit' => '+1.66tr'],
                ['period' => '30 ngày', 'percent' => '+5.2%', 'profit' => '+4.8tr'],
                ['period' => '90 ngày', 'percent' => '+12.4%', 'profit' => '+11.4tr'],
                ['period' => '1 năm', 'percent' => '+22.8%', 'profit' => '+17.2tr'],
                ['period' => '3 năm', 'percent' => '+58.5%', 'profit' => '+34.0tr'],
            ],
            'movers' => [
                ['type' => 'Tăng mạnh', 'name' => 'SJC 1L - Hà Nội', 'price' => '92.50 trieu', 'extra' => '+0.54%', 'trend' => 'up'],
                ['type' => 'Tăng mạnh', 'name' => 'DOJI 9999', 'price' => '92.40 trieu', 'extra' => '+0.47%', 'trend' => 'up'],
                ['type' => 'Tăng mạnh', 'name' => 'Bảo Tín Minh Châu', 'price' => '92.35 trieu', 'extra' => '+0.42%', 'trend' => 'up'],
                ['type' => 'Giảm nhẹ', 'name' => 'Phú Quý 9999', 'price' => '92.20 trieu', 'extra' => '-0.12%', 'trend' => 'down'],
                ['type' => 'Ổn định', 'name' => 'Vàng 18K Ý', 'price' => '62.30 trieu', 'extra' => '+0.05%', 'trend' => 'flat'],
                ['type' => 'GD cao', 'name' => 'SJC 1L', 'price' => '92.50 trieu', 'extra' => '1.850 lượng', 'trend' => 'up'],
            ],
            'knowledge' => [
                ['title' => 'Vàng 9999 là gì?', 'desc' => 'Tìm hiểu về vàng 24K nguyên chất'],
                ['title' => 'Vàng SJC là gì?', 'desc' => 'Thương hiệu vàng quốc gia Việt Nam'],
                ['title' => 'Nên mua vàng nào?', 'desc' => 'So sánh SJC, DOJI, PNJ cho người mới'],
                ['title' => 'Cách đầu tư vàng', 'desc' => 'Chiến lược đầu tư hiệu quả 2026'],
            ],
            'globalMarkets' => [
                ['name' => 'XAU/USD', 'price' => number_format($usBase, 2), 'change' => '+0.42%', 'trend' => 'up'],
                ['name' => 'XAU/EUR', 'price' => number_format($usBase * 0.92, 2), 'change' => '+0.55%', 'trend' => 'up'],
                ['name' => 'XAU/GBP', 'price' => number_format($usBase * 0.79, 2), 'change' => '+0.38%', 'trend' => 'up'],
                ['name' => 'XAU/CNY', 'price' => number_format($usBase * 7.24, 2), 'change' => '+0.61%', 'trend' => 'up'],
                ['name' => 'XAG/USD (Bac)', 'price' => '32.45', 'change' => '+1.12%', 'trend' => 'up'],
                ['name' => 'Platinum', 'price' => '1,024.50', 'change' => '-0.25%', 'trend' => 'down'],
                ['name' => 'Palladium', 'price' => '968.20', 'change' => '+0.18%', 'trend' => 'up'],
                ['name' => 'DXY Index', 'price' => '103.42', 'change' => '-0.31%', 'trend' => 'down'],
            ],
            'supports' => [
                ['level' => 'Hỗ trợ 1', 'price' => '91.0 trieu', 'type' => 'EMA 20 ngày'],
                ['level' => 'Hỗ trợ 2', 'price' => '90.5 trieu', 'type' => 'EMA 50 ngày'],
                ['level' => 'Hỗ trợ 3', 'price' => '88.0 trieu', 'type' => 'Fib 38.2%'],
                ['level' => 'Kháng cự 1', 'price' => '93.0 trieu', 'type' => 'Đỉnh gần nhất'],
                ['level' => 'Kháng cự 2', 'price' => '94.5 trieu', 'type' => 'Bollinger trên'],
                ['level' => 'Kháng cự 3', 'price' => '95.0 trieu', 'type' => 'Tâm lý'],
            ],
            'centralBanks' => [
                ['bank' => 'NHTW Trung Quốc (PBOC)', 'action' => 'Mua ròng 15 tấn', 'period' => 'Tháng 2/2026', 'trend' => 'up'],
                ['bank' => 'NHTW Ấn Độ (RBI)', 'action' => 'Mua ròng 8.2 tấn', 'period' => 'Tháng 2/2026', 'trend' => 'up'],
                ['bank' => 'NHTW Thổ Nhĩ Kỳ (CBRT)', 'action' => 'Mua ròng 5.1 tấn', 'period' => 'Tháng 2/2026', 'trend' => 'up'],
                ['bank' => 'NHTW Ba Lan (NBP)', 'action' => 'Mua ròng 3.4 tấn', 'period' => 'Tháng 2/2026', 'trend' => 'up'],
                ['bank' => 'FED (Mỹ)', 'action' => 'Giữ lãi suất 4.25-4.50%', 'period' => 'Tháng 3/2026', 'trend' => 'neutral'],
            ],
            'macroFactors' => [
                ['factor' => 'Lãi suất FED', 'value' => '4.25-4.50%', 'impact' => 'Giữ nguyên → hỗ trợ vàng', 'signal' => 'positive'],
                ['factor' => 'Lạm phát Mỹ (CPI)', 'value' => '2.8% YoY', 'impact' => 'Giảm nhẹ → FED có thể cắt giảm', 'signal' => 'positive'],
                ['factor' => 'Chỉ số DXY', 'value' => '103.42', 'impact' => 'Giảm 0.31% → vàng tăng', 'signal' => 'positive'],
                ['factor' => 'Lợi suất US10Y', 'value' => '4.18%', 'impact' => 'Giảm 5bps → giảm chi phí nắm giữ', 'signal' => 'positive'],
                ['factor' => 'Địa chính trị', 'value' => 'Căng thẳng leo thang', 'impact' => 'Nhu cầu trú ẩn an toàn tăng', 'signal' => 'positive'],
                ['factor' => 'Cung vàng toàn cầu', 'value' => '4,820 tan/nam', 'impact' => 'Tăng 2.1% YoY', 'signal' => 'neutral'],
            ],
            'forecast' => [
                ['period' => 'Phiên chiều nay', 'range' => '92.0 - 93.0 trieu', 'bias' => 'Tăng nhẹ', 'confidence' => 75],
                ['period' => 'Tuần tới (10-14/3)', 'range' => '91.5 - 94.0 trieu', 'bias' => 'Tích cực', 'confidence' => 68],
                ['period' => 'Tháng 3/2026', 'range' => '90.0 - 95.0 trieu', 'bias' => 'Tăng', 'confidence' => 62],
                ['period' => 'Quy 2/2026', 'range' => '92.0 - 98.0 trieu', 'bias' => 'Tăng mạnh', 'confidence' => 55],
            ],
            'correlations' => [
                ['asset' => 'USD/VND', 'corr' => '-0.82', 'note' => 'Nghịch tương quan mạnh'],
                ['asset' => 'S&P 500', 'corr' => '-0.35', 'note' => 'Nghịch tương quan nhẹ'],
                ['asset' => 'Bitcoin', 'corr' => '+0.28', 'note' => 'Đồng tương quan nhẹ'],
                ['asset' => 'Bac (XAG)', 'corr' => '+0.91', 'note' => 'Đồng tương quan rất mạnh'],
                ['asset' => 'Dau WTI', 'corr' => '+0.41', 'note' => 'Đồng tương quan trung bình'],
            ],
            'analystOpinion' => [
                'summary' => 'Dữ liệu tạm thời không khả dụng. Vui lòng tải lại trang.',
                'recommendation' => 'GIỮ — Chờ tín hiệu',
                'target' => '—',
                'stopLoss' => '—',
                'riskReward' => '—',
                'compositeScore' => 50,
                'bias' => 'neutral',
            ],
            'footer' => [
                'about' => ['Giới thiệu', 'Liên hệ', 'API'],
                'market' => ['Giá xăng', 'Tỷ giá ngoại tệ', 'Giá bạc', 'Giá kim loại'],
                'knowledge' => ['Vàng 9999 là gì?', 'Vàng SJC là gì?', 'Nên mua vàng nào?', 'Cách đầu tư vàng'],
                'history' => ['Giá vàng 2026', 'Giá vàng 2025', 'Giá vàng 2024', 'Giá vàng 2023'],
            ],
        ];
    }

    private function jitter(int $max): int
    {
        return mt_rand(-$max, $max);
    }
}
