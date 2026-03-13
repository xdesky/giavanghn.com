<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalysisArticle extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'trigger_type',
        'analysis_date',
        'price_signature',
        'word_count',
        'thumbnail_path',
        'summary',
        'content',
        'meta',
        'tags',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'analysis_date' => 'date',
            'meta' => 'array',
            'tags' => 'array',
            'published_at' => 'datetime',
        ];
    }
}
