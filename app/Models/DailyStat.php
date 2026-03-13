<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyStat extends Model
{
    protected $fillable = [
        'date',
        'sjc_spread',
        'trading_volume',
        'volatility_24h',
        'volatility_trend',
        'usd_vnd_rate',
        'usd_vnd_change',
        'usd_vnd_trend',
        'dxy_value',
        'dxy_change',
        'dxy_trend',
        'cpi_value',
        'cpi_period',
        'cpi_delta',
    ];

    protected $casts = [
        'date' => 'date',
    ];
}
