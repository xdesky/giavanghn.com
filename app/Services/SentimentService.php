<?php

namespace App\Services;

use App\Models\BaotinmanhhaiGoldPrice;
use App\Models\BtmcGoldPrice;
use App\Models\DojiGoldPrice;
use App\Models\GoldPrice;
use App\Models\MarketSentiment;
use App\Models\MihongGoldPrice;
use App\Models\NgocthamGoldPrice;
use App\Models\PhuquyGoldPrice;
use App\Models\PnjGoldPrice;
use App\Models\PriceHistory;
use App\Models\WorldPrice;
use Illuminate\Support\Carbon;

class SentimentService
{
    /**
     * Weight configuration for composite index.
     */
    private const W_TREND = 0.40;
    private const W_CONSENSUS = 0.25;
    private const W_MOMENTUM = 0.20;
    private const W_SPREAD = 0.15;

    /**
     * Calculate and store sentiment for given date.
     */
    public function calculate(?Carbon $date = null): MarketSentiment
    {
        $date = $date ?? now();
        $dateStr = $date->toDateString();

        $priceTrend = $this->calcPriceTrendScore($date);
        $consensus = $this->calcDomesticConsensusScore($dateStr);
        $momentum = $this->calcMomentumScore($date);
        $spread = $this->calcSpreadScore($dateStr);

        // Composite index (0-100)
        $composite = (int) round(
            $priceTrend * self::W_TREND
            + $consensus * self::W_CONSENSUS
            + $momentum * self::W_MOMENTUM
            + $spread * self::W_SPREAD
        );
        $composite = max(0, min(100, $composite));

        // Derive buy/neutral/sell from component scores
        $buyPercent = round(($priceTrend * 0.5 + $consensus * 0.3 + $momentum * 0.2) / 100 * 100, 1);
        $buyPercent = max(0, min(100, $buyPercent));
        // Neutral derived from spread tightness — tighter spread = more neutral confidence
        $neutralPercent = round(max(0, min(40, ($spread - 50) * 0.4 + 15)), 1);
        $sellPercent = round(max(0, 100 - $buyPercent - $neutralPercent), 1);

        // Normalize to sum 100
        $total = $buyPercent + $neutralPercent + $sellPercent;
        if ($total > 0) {
            $buyPercent = round($buyPercent / $total * 100, 1);
            $neutralPercent = round($neutralPercent / $total * 100, 1);
            $sellPercent = round(100 - $buyPercent - $neutralPercent, 1);
        }

        $trendLabel = match (true) {
            $composite >= 80 => 'Tích cực rất mạnh',
            $composite >= 65 => 'Tích cực mạnh',
            $composite >= 50 => 'Tích cực nhẹ',
            $composite >= 35 => 'Trung lập',
            $composite >= 20 => 'Tiêu cực nhẹ',
            default => 'Tiêu cực mạnh',
        };

        $trendDirection = match (true) {
            $composite >= 50 => 'up',
            $composite >= 35 => 'neutral',
            default => 'down',
        };

        return MarketSentiment::updateOrCreate(
            ['date' => $dateStr],
            [
                'fear_greed_index' => $composite,
                'price_trend_score' => round($priceTrend, 2),
                'domestic_consensus_score' => round($consensus, 2),
                'momentum_score' => round($momentum, 2),
                'spread_score' => round($spread, 2),
                'buy_percent' => $buyPercent,
                'neutral_percent' => $neutralPercent,
                'sell_percent' => $sellPercent,
                'trend_label' => $trendLabel,
                'trend_direction' => $trendDirection,
            ]
        );
    }

    /**
     * 1) Price Trend Score (40%):
     * Compare current XAU/USD vs SMA-7 and SMA-30.
     * Price above both → bullish (high score).
     * Price below both → bearish (low score).
     */
    private function calcPriceTrendScore(Carbon $date): float
    {
        // Try price_histories first, fallback to world_prices
        $candles = PriceHistory::where('symbol', 'xau_usd')
            ->where('period', '1d')
            ->where('period_at', '<=', $date->toDateString())
            ->orderByDesc('period_at')
            ->limit(30)
            ->get(['close', 'period_at'])
            ->sortBy('period_at')
            ->values();

        if ($candles->count() < 3) {
            // Fallback: build daily prices from world_prices table
            $candles = $this->getDailyXauPrices($date, 30);
        }

        if ($candles->count() < 3) {
            return 50.0;
        }

        $current = $candles->last()->close;
        $sma7 = $candles->slice(-min(7, $candles->count()))->avg('close');
        $sma30 = $candles->avg('close');

        // Score based on position relative to SMAs
        $score = 50.0;

        // Price vs SMA-7 (short term): ±25 points
        if ($sma7 > 0) {
            $diffShort = ($current - $sma7) / $sma7 * 100;
            $score += max(-25, min(25, $diffShort * 10));
        }

        // Price vs SMA-30 (long term): ±25 points
        if ($sma30 > 0) {
            $diffLong = ($current - $sma30) / $sma30 * 100;
            $score += max(-25, min(25, $diffLong * 8));
        }

        return max(0, min(100, $score));
    }

    /**
     * 2) Domestic Market Consensus (25%):
     * Count how many of the 8 brands have buy prices going up vs down today.
     * High agreement in bullish direction → high score.
     */
    private function calcDomesticConsensusScore(string $dateStr): float
    {
        $tables = [
            ['model' => GoldPrice::class, 'group' => 'brand'],
            ['model' => DojiGoldPrice::class, 'group' => 'brand'],
            ['model' => BtmcGoldPrice::class, 'group' => 'brand'],
            ['model' => PnjGoldPrice::class, 'group' => 'brand'],
            ['model' => PhuquyGoldPrice::class, 'group' => 'brand'],
            ['model' => MihongGoldPrice::class, 'group' => 'code'],
            ['model' => BaotinmanhhaiGoldPrice::class, 'group' => 'brand'],
            ['model' => NgocthamGoldPrice::class, 'group' => 'brand'],
        ];

        $bullish = 0;
        $bearish = 0;
        $neutralCount = 0;
        $checked = 0;

        foreach ($tables as $t) {
            $model = $t['model'];
            $group = $t['group'];

            // Get today's average buy_price
            $todayAvg = $model::whereDate('created_at', $dateStr)->avg('buy_price');
            if (!$todayAvg) {
                continue;
            }

            // Get yesterday's average buy_price
            $yesterday = Carbon::parse($dateStr)->subDay()->toDateString();
            $yesterdayAvg = $model::whereDate('created_at', $yesterday)->avg('buy_price');

            if (!$yesterdayAvg) {
                // Try finding the most recent previous day with data
                $prev = $model::where('created_at', '<', $dateStr . ' 00:00:00')
                    ->orderByDesc('created_at')
                    ->value('created_at');
                if ($prev) {
                    $yesterdayAvg = $model::whereDate('created_at', Carbon::parse($prev)->toDateString())
                        ->avg('buy_price');
                }
            }

            if (!$yesterdayAvg || $yesterdayAvg == 0) {
                continue;
            }

            $checked++;
            $change = ($todayAvg - $yesterdayAvg) / $yesterdayAvg * 100;

            if ($change > 0.05) {
                $bullish++;
            } elseif ($change < -0.05) {
                $bearish++;
            } else {
                $neutralCount++;
            }
        }

        if ($checked === 0) {
            return 50.0;
        }

        // Score: all bullish = 100, all bearish = 0
        $score = ($bullish * 100 + $neutralCount * 50) / $checked;

        return max(0, min(100, $score));
    }

    /**
     * 3) Momentum Score (20%):
     * Rate of price change — acceleration vs deceleration.
     * Uses XAU/USD recent daily changes.
     */
    private function calcMomentumScore(Carbon $date): float
    {
        $candles = PriceHistory::where('symbol', 'xau_usd')
            ->where('period', '1d')
            ->where('period_at', '<=', $date->toDateString())
            ->orderByDesc('period_at')
            ->limit(14)
            ->get(['close', 'period_at'])
            ->sortBy('period_at')
            ->values();

        if ($candles->count() < 3) {
            $candles = $this->getDailyXauPrices($date, 14);
        }

        if ($candles->count() < 3) {
            return 50.0;
        }

        // Calculate daily returns
        $returns = [];
        for ($i = 1; $i < $candles->count(); $i++) {
            $prev = $candles[$i - 1]->close;
            if ($prev > 0) {
                $returns[] = ($candles[$i]->close - $prev) / $prev * 100;
            }
        }

        if (empty($returns)) {
            return 50.0;
        }

        // Recent returns (last 3 days) vs older returns
        $recentCount = min(3, count($returns));
        $recent = array_slice($returns, -$recentCount);
        $avgRecent = array_sum($recent) / count($recent);

        // RSI-like calculation: avg gains vs avg losses over full period
        $gains = array_filter($returns, fn($r) => $r > 0);
        $losses = array_filter($returns, fn($r) => $r < 0);

        $avgGain = !empty($gains) ? array_sum($gains) / count($returns) : 0;
        $avgLoss = !empty($losses) ? abs(array_sum($losses)) / count($returns) : 0;

        // RSI formula
        if ($avgLoss == 0) {
            $rsi = 100;
        } else {
            $rs = $avgGain / $avgLoss;
            $rsi = 100 - (100 / (1 + $rs));
        }

        // Combine RSI with recent momentum
        $momentumBoost = max(-20, min(20, $avgRecent * 10));
        $score = $rsi + $momentumBoost;

        return max(0, min(100, $score));
    }

    /**
     * 4) Spread Score (15%):
     * Tighter buy-sell spreads across brands = more market confidence = higher score.
     * Wide spreads = uncertainty = lower score.
     */
    private function calcSpreadScore(string $dateStr): float
    {
        $tables = [
            GoldPrice::class,
            DojiGoldPrice::class,
            BtmcGoldPrice::class,
            PnjGoldPrice::class,
            PhuquyGoldPrice::class,
            MihongGoldPrice::class,
            BaotinmanhhaiGoldPrice::class,
            NgocthamGoldPrice::class,
        ];

        $spreads = [];

        foreach ($tables as $model) {
            $records = $model::whereDate('created_at', $dateStr)
                ->where('buy_price', '>', 0)
                ->where('sell_price', '>', 0)
                ->get(['buy_price', 'sell_price']);

            foreach ($records as $r) {
                $mid = ($r->buy_price + $r->sell_price) / 2;
                if ($mid > 0) {
                    // Spread as % of mid price
                    $spreads[] = abs($r->sell_price - $r->buy_price) / $mid * 100;
                }
            }
        }

        if (empty($spreads)) {
            return 50.0;
        }

        $avgSpread = array_sum($spreads) / count($spreads);

        // Vietnamese domestic gold typical spread: 1% - 5%
        // Tight (< 1.5%) → 85+, Normal (1.5-3%) → 50-75, Wide (> 4%) → 20-
        if ($avgSpread <= 1.0) {
            $score = 95;
        } elseif ($avgSpread <= 5.0) {
            // Linear interpolation: 1% → 90, 5% → 20
            $score = 90 - ($avgSpread - 1.0) * (70 / 4.0);
        } else {
            $score = max(5, 20 - ($avgSpread - 5.0) * 5);
        }

        return max(0, min(100, $score));
    }

    /**
     * Get the latest sentiment for display.
     */
    public static function latest(): ?MarketSentiment
    {
        return MarketSentiment::orderByDesc('date')->first();
    }

    /**
     * Get sentiment for a specific date.
     */
    public static function forDate(string $date): ?MarketSentiment
    {
        return MarketSentiment::where('date', $date)->first();
    }

    /**
     * Build daily XAU/USD prices from world_prices table.
     * Returns a collection of objects with ->close and ->period_at.
     */
    private function getDailyXauPrices(Carbon $date, int $days): \Illuminate\Support\Collection
    {
        $rows = WorldPrice::where('symbol', 'XAU/USD')
            ->whereDate('created_at', '<=', $date->toDateString())
            ->selectRaw('DATE(created_at) as period_at, AVG(price) as close')
            ->groupByRaw('DATE(created_at)')
            ->orderByDesc('period_at')
            ->limit($days)
            ->get()
            ->sortBy('period_at')
            ->values();

        // Cast to objects with float close
        return $rows->map(fn($r) => (object) [
            'close' => (float) $r->close,
            'period_at' => $r->period_at,
        ]);
    }
}
