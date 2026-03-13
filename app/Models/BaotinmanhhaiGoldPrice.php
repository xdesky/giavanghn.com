<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaotinmanhhaiGoldPrice extends Model
{
    protected $fillable = [
        'brand', 'karat',
        'buy_price', 'sell_price', 'currency', 'change_percent',
    ];

    protected function casts(): array
    {
        return [
            'buy_price' => 'integer',
            'sell_price' => 'integer',
            'change_percent' => 'float',
        ];
    }

    public function scopeLatest($query)
    {
        return $query->whereIn('id', function ($sub) {
            $sub->selectRaw('MAX(id)')
                ->from('baotinmanhhai_gold_prices')
                ->groupBy('brand');
        });
    }
}
