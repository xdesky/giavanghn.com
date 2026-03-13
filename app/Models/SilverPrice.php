<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SilverPrice extends Model
{
    protected $fillable = ['source', 'brand', 'buy_price', 'sell_price', 'currency', 'change_percent'];

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
            $sub->selectRaw('MAX(id)')->from('silver_prices')->groupBy('source', 'brand');
        });
    }
}
