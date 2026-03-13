<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoldPrice extends Model
{
    protected $fillable = [
        'source', 'brand', 'karat', 'region',
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

    public function scopeLatestByBrand($query)
    {
        return $query->whereIn('id', function ($sub) {
            $sub->selectRaw('MAX(id)')
                ->from('gold_prices')
                ->groupBy('source', 'brand', 'region');
        });
    }

    public function scopeSource($query, string $source)
    {
        return $query->where('source', $source);
    }
}
