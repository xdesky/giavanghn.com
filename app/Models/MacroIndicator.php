<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MacroIndicator extends Model
{
    protected $fillable = ['indicator', 'name', 'value', 'impact', 'signal', 'source'];

    public function scopeLatestByIndicator($query)
    {
        return $query->whereIn('id', function ($sub) {
            $sub->selectRaw('MAX(id)')
                ->from('macro_indicators')
                ->groupBy('indicator');
        });
    }
}
