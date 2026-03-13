<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    protected $fillable = ['email', 'name', 'markets', 'active'];

    protected function casts(): array
    {
        return [
            'markets' => 'array',
            'active' => 'boolean',
        ];
    }
}
