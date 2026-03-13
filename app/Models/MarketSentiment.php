<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketSentiment extends Model
{
    protected $fillable = [
        'date',
        'fear_greed_index',
        'price_trend_score',
        'domestic_consensus_score',
        'momentum_score',
        'spread_score',
        'buy_percent',
        'neutral_percent',
        'sell_percent',
        'trend_label',
        'trend_direction',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'fear_greed_index' => 'integer',
            'price_trend_score' => 'float',
            'domestic_consensus_score' => 'float',
            'momentum_score' => 'float',
            'spread_score' => 'float',
            'buy_percent' => 'float',
            'neutral_percent' => 'float',
            'sell_percent' => 'float',
        ];
    }
}
