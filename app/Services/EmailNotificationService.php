<?php

namespace App\Services;

use App\Models\User;
use App\Models\AnalysisArticle;
use Illuminate\Support\Facades\Mail;
use App\Mail\PriceAlertMail;
use App\Mail\DailyReportMail;
use App\Mail\WeeklyReportMail;

class EmailNotificationService
{
    /**
     * Send price alert to subscribed users
     */
    public function sendPriceAlert(array $priceData): void
    {
        $users = User::whereHas('subscription', function($q) {
            $q->where('email_price_alerts', true);
        })->get();

        foreach ($users as $user) {
            Mail::to($user->email)->queue(new PriceAlertMail($user, $priceData));
        }
    }

    /**
     * Send daily report to subscribed users
     */
    public function sendDailyReport(AnalysisArticle $article): void
    {
        $users = User::whereHas('subscription', function($q) {
            $q->where('email_daily_report', true);
        })->where('is_active', true)->get();

        foreach ($users as $user) {
            Mail::to($user->email)->queue(new DailyReportMail($user, $article));
        }
    }

    /**
     * Send weekly report to subscribed users
     */
    public function sendWeeklyReport(array $weeklyData): void
    {
        $users = User::whereHas('subscription', function($q) {
            $q->where('email_weekly_report', true);
        })->where('is_active', true)->get();

        foreach ($users as $user) {
            Mail::to($user->email)->queue(new WeeklyReportMail($user, $weeklyData));
        }
    }

    /**
     * Send market analysis article to subscribed users
     */
    public function sendMarketAnalysis(AnalysisArticle $article): void
    {
        $users = User::whereHas('subscription', function($q) {
            $q->where('email_market_analysis', true);
        })->where('is_active', true)->get();

        foreach ($users as $user) {
            Mail::to($user->email)->queue(new DailyReportMail($user, $article));
        }
    }

    /**
     * Send custom notification to specific users
     */
    public function sendCustomNotification(array $userIds, string $subject, string $message): void
    {
        $users = User::whereIn('id', $userIds)->where('is_active', true)->get();

        foreach ($users as $user) {
            Mail::raw($message, function ($mail) use ($user, $subject) {
                $mail->to($user->email)
                     ->subject($subject);
            });
        }
    }
}
