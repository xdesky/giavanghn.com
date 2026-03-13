<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    protected $fillable = ['pair', 'rate', 'change_percent', 'source'];

    protected function casts(): array
    {
        return [
            'rate' => 'float',
            'change_percent' => 'float',
        ];
    }

    public function scopeLatestByPair($query)
    {
        return $query->whereIn('id', function ($sub) {
            $sub->selectRaw('MAX(id)')
                ->from('exchange_rates')
                ->groupBy('pair');
        });
    }
}
