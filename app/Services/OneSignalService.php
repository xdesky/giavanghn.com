<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OneSignalService
{
    protected string $appId;
    protected string $apiKey;
    protected string $apiUrl = 'https://onesignal.com/api/v1';

    public function __construct()
    {
        $this->appId = config('services.onesignal.app_id');
        $this->apiKey = config('services.onesignal.api_key');
    }

    /**
     * Send notification to specific players (users)
     */
    public function sendToPlayers(array $playerIds, string $title, string $message, array $data = []): bool
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Key ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->apiUrl}/notifications", [
                'app_id' => $this->appId,
                'include_player_ids' => $playerIds,
                'headings' => ['en' => $title, 'vi' => $title],
                'contents' => ['en' => $message, 'vi' => $message],
                'data' => $data,
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('OneSignal notification failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send notification to all subscribed users
     */
    public function sendToAll(string $title, string $message, array $data = []): bool
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Key ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->apiUrl}/notifications", [
                'app_id' => $this->appId,
                'included_segments' => ['Subscribed Users'],
                'headings' => ['en' => $title, 'vi' => $title],
                'contents' => ['en' => $message, 'vi' => $message],
                'data' => $data,
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('OneSignal broadcast failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send price alert notification
     */
    public function sendPriceAlert(array $priceData): void
    {
        $users = User::whereHas('subscription', function($q) {
            $q->where('push_price_alerts', true);
        })
        ->whereNotNull('onesignal_player_id')
        ->where('is_active', true)
        ->get();

        if ($users->isEmpty()) {
            return;
        }

        $playerIds = $users->pluck('onesignal_player_id')->filter()->toArray();

        $title = '🔔 Cảnh báo biến động giá vàng';
        $message = $priceData['message'] ?? 'Giá vàng có biến động đáng chú ý!';

        $this->sendToPlayers($playerIds, $title, $message, [
            'type' => 'price_alert',
            'data' => $priceData,
        ]);
    }

    /**
     * Send daily report notification
     */
    public function sendDailyReport(string $articleTitle, string $articleUrl): void
    {
        $users = User::whereHas('subscription', function($q) {
            $q->where('push_daily_report', true);
        })
        ->whereNotNull('onesignal_player_id')
        ->where('is_active', true)
        ->get();

        if ($users->isEmpty()) {
            return;
        }

        $playerIds = $users->pluck('onesignal_player_id')->filter()->toArray();

        $title = '📊 Báo cáo giá vàng hôm nay';
        $message = 'Bản tin phân tích giá vàng mới nhất đã có!';

        $this->sendToPlayers($playerIds, $title, $message, [
            'type' => 'daily_report',
            'url' => $articleUrl,
            'article_title' => $articleTitle,
        ]);
    }

    /**
     * Send major event notification
     */
    public function sendMajorEvent(string $title, string $message, array $data = []): void
    {
        $users = User::whereHas('subscription', function($q) {
            $q->where('push_major_events', true);
        })
        ->whereNotNull('onesignal_player_id')
        ->where('is_active', true)
        ->get();

        if ($users->isEmpty()) {
            return;
        }

        $playerIds = $users->pluck('onesignal_player_id')->filter()->toArray();

        $this->sendToPlayers($playerIds, '⚡ ' . $title, $message, array_merge([
            'type' => 'major_event',
        ], $data));
    }

    /**
     * Update user's OneSignal player ID
     */
    public function updatePlayerID(User $user, string $playerId): bool
    {
        return $user->update(['onesignal_player_id' => $playerId]);
    }
}
