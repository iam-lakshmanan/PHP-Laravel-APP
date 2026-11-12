<?php

namespace App\Services;

use App\DTO\UserAppDeviceDTO;
use App\Repositories\UserAppDeviceRepository;
use App\Models\UserAppDevice;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class UserAppDeviceService
{
    protected $repository;

    public function __construct(UserAppDeviceRepository $repository)
    {
        $this->repository = $repository;
    }

    public function storeOrUpdate(UserAppDeviceDTO $dto)
    {
        return $this->repository->updateOrCreate([
            'device_id' => $dto->device_id,
            'fcm_token' => $dto->fcm_token,
            'platform'  => $dto->platform,
        ]);
    }

    public function sendToAllUsers(string $title, string $message): void
    {
        try {
            $tokens = UserAppDevice::whereNotNull('fcm_token')
                ->pluck('fcm_token')
                ->toArray();

            if (empty($tokens)) {
                Log::info('No tokens found');
                return;
            }

            Log::info('Sending notifications to ' . count($tokens) . ' devices');
            foreach (array_chunk($tokens, 500) as $chunk) {
                $this->sendPushNotification($chunk, $title, $message);
            }

        } catch (\Exception $e) {
            Log::error('Error sending notifications: ' . $e->getMessage());
        }
    }

    private function sendPushNotification(array $tokens, string $title, string $message): void
    {
        try {
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

            $messaging = (new Factory())
                ->withServiceAccount($credentials)
                ->createMessaging();

            $message = CloudMessage::new()
                ->withNotification(Notification::fromArray([
                    'title' => $title,
                    'body' => $message,
                ]));

            $report = $messaging->sendMulticast($message, $tokens);

            Log::info('FCM send report', [
                'success' => $report->successes()->count(),
                'failure' => $report->failures()->count(),
                'invalid_tokens' => $report->invalidTokens(),
                'unknown_tokens' => $report->unknownTokens(),
            ]);

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
