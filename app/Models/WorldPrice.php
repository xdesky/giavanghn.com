<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorldPrice extends Model
{
    protected $fillable = [
        'symbol', 'name', 'price', 'change_percent', 'change_amount', 'currency',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'float',
            'change_percent' => 'float',
            'change_amount' => 'float',
        ];
    }

    public function scopeLatestBySymbol($query)
    {
        return $query->whereIn('id', function ($sub) {
            $sub->selectRaw('MAX(id)')
                ->from('world_prices')
                ->groupBy('symbol');
        });
    }
}
