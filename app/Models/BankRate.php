<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankRate extends Model
{
    protected $fillable = ['bank', 'currency', 'buy_rate', 'sell_rate'];

    protected function casts(): array
    {
        return [
            'buy_rate' => 'float',
            'sell_rate' => 'float',
        ];
    }

    public function scopeLatestByBank($query)
    {
        return $query->whereIn('id', function ($sub) {
            $sub->selectRaw('MAX(id)')->from('bank_rates')->groupBy('bank', 'currency');
        });
    }
}
