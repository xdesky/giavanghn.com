<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email_price_alerts',
        'email_daily_report',
        'email_weekly_report',
        'email_market_analysis',
        'push_price_alerts',
        'push_daily_report',
        'push_major_events',
        'price_alert_threshold',
        'preferred_brands',
    ];

    protected $casts = [
        'email_price_alerts' => 'boolean',
        'email_daily_report' => 'boolean',
        'email_weekly_report' => 'boolean',
        'email_market_analysis' => 'boolean',
        'push_price_alerts' => 'boolean',
        'push_daily_report' => 'boolean',
        'push_major_events' => 'boolean',
        'price_alert_threshold' => 'decimal:2',
        'preferred_brands' => 'array',
    ];

    /**
     * User who owns this subscription
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
