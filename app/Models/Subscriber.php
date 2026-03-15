<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    protected $fillable = ['email', 'name', 'markets', 'active', 'unsubscribe_token', 'last_notified_at'];

    protected function casts(): array
    {
        return [
            'markets' => 'array',
            'active' => 'boolean',
            'last_notified_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Subscriber $subscriber) {
            if (empty($subscriber->unsubscribe_token)) {
                $subscriber->unsubscribe_token = bin2hex(random_bytes(32));
            }
        });
    }
}
