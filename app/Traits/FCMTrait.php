<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FcmNotification;

trait FCMTrait
{
    protected function sendFCMNotification(string $token, string $title, string $body): void
    {
        $credentials = config('services.fcm.server_key');

        if (empty($credentials)) {
            Log::warning('FCM credentials are not configured');
            return;
        }

        if (!str_starts_with($credentials, '{') && !file_exists($credentials)) {
            $credentials = base_path($credentials);
        }

        if (!file_exists($credentials) && !str_starts_with(trim($credentials), '{')) {
            Log::warning('FCM service account file not found or invalid credentials', ['path' => $credentials]);
            return;
        }

        try {
            $messaging = (new Factory())
                ->withServiceAccount($credentials)
                ->createMessaging();

            $payload = [
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'android' => [
                    'priority' => 'HIGH',
                    'notification' => [
                        'channel_id' => 'default',
                    ],
                ],
                'apns' => [
                    'headers' => [
                        'apns-priority' => '10',
                    ],
                    'payload' => [
                        'aps' => [
                            'alert' => [
                                'title' => $title,
                                'body' => $body,
                            ],
                            'sound' => 'default',
                            'badge' => 1,
                        ],
                    ],
                ],
            ];

            $message = CloudMessage::fromArray($payload);
            $report = $messaging->sendMulticast($message, [$token]);

            if ($report->hasFailures()) {
                foreach ($report->failures()->getItems() as $failure) {
                    Log::error('FCM notification failure', [
                        'token' => $failure->target()->value(),
                        'error' => $failure->error()?->getMessage(),
                    ]);
                }
            }
        } catch (\Throwable $e) {
            Log::error('FCM Error: ' . $e->getMessage());
        }
    }
}
