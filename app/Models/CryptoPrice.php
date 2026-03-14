<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CryptoPrice extends Model
{
    protected $fillable = ['symbol', 'price', 'change_24h'];

    protected function casts(): array
    {
        return [
            'price' => 'float',
            'change_24h' => 'float',
        ];
    }

    public function scopeLatestBySymbol($query)
    {
        return $query->whereIn('id', function ($sub) {
            $sub->selectRaw('MAX(id)')->from('crypto_prices')->groupBy('symbol');
        });
    }
}
