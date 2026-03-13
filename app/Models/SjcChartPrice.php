<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SjcChartPrice extends Model
{
    protected $fillable = [
        'price_date',
        'sell_million',
        'buy_million',
        'source',
    ];

    protected function casts(): array
    {
        return [
            'price_date' => 'date',
            'sell_million' => 'float',
            'buy_million' => 'float',
        ];
    }
}
