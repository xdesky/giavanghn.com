<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceHistory extends Model
{
    protected $fillable = [
        'symbol', 'open', 'high', 'low', 'close', 'volume', 'period', 'period_at',
    ];

    protected function casts(): array
    {
        return [
            'open' => 'float',
            'high' => 'float',
            'low' => 'float',
            'close' => 'float',
            'volume' => 'integer',
            'period_at' => 'datetime',
        ];
    }
}
