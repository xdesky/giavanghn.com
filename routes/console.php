<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// All-in-one: check gaps → recover stale → crawl all → sync histories
Schedule::command('crawl:run')->everyFifteenMinutes()->withoutOverlapping();

// Auto-generate long-form analysis article daily and on detected price changes
Schedule::command('generate:analysis-article --trigger=auto')->everyFifteenMinutes();

// Daily summary article: comprehensive end-of-day gold price report at 20:30
Schedule::command('generate:analysis-article --trigger=summary')->dailyAt('20:30');

// Sync SJC chart price from today's latest gold_prices record (updates when price changes)
Schedule::command('sync:sjc-chart-prices')->everyFifteenMinutes()->withoutOverlapping();
