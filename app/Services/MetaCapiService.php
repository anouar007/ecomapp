<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MetaCapiService
{
    protected $accessToken;
    protected $pixelId;
    protected $testEventCode;

    public function __construct()
    {
        $this->accessToken = setting('meta_capi_access_token');
        $this->pixelId = setting('facebook_pixel_id');
        $this->testEventCode = setting('meta_capi_test_event_code');
    }

    /**
     * Dispatch an event to Meta CAPI.
     */
    public function track(string $eventName, array $customData = [], array $userData = [])
    {
        if (!$this->accessToken || !$this->pixelId) {
            return;
        }

        $payload = [
            'data' => [
                [
                    'event_name' => $eventName,
                    'event_time' => time(),
                    'action_source' => 'website',
                    'event_source_url' => request()->fullUrl(),
                    'user_data' => $this->prepareUserData($userData),
                    'custom_data' => $customData,
                ]
            ]
        ];

        if ($this->testEventCode) {
            $payload['test_event_code'] = $this->testEventCode;
        }

        try {
            $response = Http::post("https://graph.facebook.com/v19.0/{$this->pixelId}/events?access_token={$this->accessToken}", $payload);

            if ($response->failed()) {
                Log::error('[Meta CAPI] Event dispatch failed', [
                    'event' => $eventName,
                    'error' => $response->json()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('[Meta CAPI] Exception during dispatch', [
                'event' => $eventName,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Prepare and hash user data for CAPI matching.
     */
    protected function prepareUserData(array $userData): array
    {
        $data = [
            'client_ip_address' => request()->ip(),
            'client_user_agent' => request()->userAgent(),
        ];

        // Hashing logic for PII (SHA256)
        if (isset($userData['email'])) {
            $data['em'] = hash('sha256', strtolower(trim($userData['email'])));
        }
        if (isset($userData['phone'])) {
            $data['ph'] = hash('sha256', preg_replace('/\D/', '', $userData['phone']));
        }
        if (isset($userData['fn'])) {
            $data['fn'] = hash('sha256', strtolower(trim($userData['fn'])));
        }
        if (isset($userData['ln'])) {
            $data['ln'] = hash('sha256', strtolower(trim($userData['ln'])));
        }

        return $data;
    }
}
