<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrawlLog extends Model
{
    protected $fillable = ['crawler', 'status', 'records_count', 'error_message', 'duration_ms'];

    protected function casts(): array
    {
        return [
            'records_count' => 'integer',
            'duration_ms' => 'integer',
        ];
    }
}
