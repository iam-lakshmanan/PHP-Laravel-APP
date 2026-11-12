<?php

namespace App\Jobs;

use App\Models\UserAppDevice;
use App\Traits\FCMTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendAppUpdateNotificationJob implements ShouldQueue
{
    use FCMTrait;
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $title;
    protected $body;

    /**
     * Create a new job instance.
     */
    public function __construct($title, $body)
    {
        $this->title = $title;
        $this->body = $body;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('SendNotificationJob started', [
            'title' => $this->title,
            'body' => $this->body,
        ]);

        $tokens = UserAppDevice::whereNotNull('fcm_token')
            ->pluck('fcm_token')
            ->toArray();

        Log::info('FCM Tokens Retrieved', ['tokens' => $tokens]);

        foreach ($tokens as $token) {
            Log::info('Sending notification to token:', ['token' => $token]);
            $this->sendFCMNotification($token, $this->title, $this->body);
        }

        Log::info('Notifications sent successfully to all users');
    }
}
