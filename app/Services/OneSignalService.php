<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OneSignalService
{
    /**
     * Send a web push notification to all subscribed users.
     *
     * @param string $title
     * @param string $message
     * @param string|null $url
     * @return bool
     */
    public static function sendNotification(string $title, string $message, string $url = null)
    {
        $appId = env('ONESIGNAL_APP_ID');
        $restKey = env('ONESIGNAL_REST_API_KEY');

        if (!$appId || !$restKey) {
            Log::warning('OneSignal App ID or REST API Key not configured.');
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $restKey,
                'Content-Type' => 'application/json',
            ])->post('https://onesignal.com/api/v1/notifications', [
                'app_id' => $appId,
                'included_segments' => ['All'], // Or specify 'Subscribed Users'
                'headings' => ['en' => $title],
                'contents' => ['en' => $message],
                'url' => $url,
                'chrome_web_icon' => asset('img/logo.png'), // Optional
            ]);

            if ($response->failed()) {
                Log::error('OneSignal API Error: ' . $response->body());
            }

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('OneSignal push failed: ' . $e->getMessage());
            return false;
        }
    }
}
