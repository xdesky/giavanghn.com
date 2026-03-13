<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MihongGoldPrice extends Model
{
    protected $fillable = [
        'brand', 'code', 'karat',
        'buy_price', 'sell_price', 'currency',
        'buy_change', 'sell_change', 'change_percent',
    ];

    protected function casts(): array
    {
        return [
            'buy_price' => 'integer',
            'sell_price' => 'integer',
            'buy_change' => 'float',
            'sell_change' => 'float',
            'change_percent' => 'float',
        ];
    }

    public function scopeLatest($query)
    {
        return $query->whereIn('id', function ($sub) {
            $sub->selectRaw('MAX(id)')
                ->from('mihong_gold_prices')
                ->groupBy('code');
        });
    }
}
