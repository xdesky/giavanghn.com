<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class NewsArticle extends Model
{
    protected $fillable = [
        'tag', 'title', 'summary', 'url', 'image_url', 'source', 'impact', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    public function scopeGoldRelated(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->where('title', 'like', '%vàng%')
              ->orWhere('title', 'like', '%gold%')
              ->orWhere('title', 'like', '%XAU%')
              ->orWhere('title', 'like', '%SJC%')
              ->orWhere('title', 'like', '%DOJI%')
              ->orWhere('title', 'like', '%PNJ%')
              ->orWhere('title', 'like', '%kim loại quý%')
              ->orWhere('title', 'like', '%giá vàng%');
        });
    }
}
