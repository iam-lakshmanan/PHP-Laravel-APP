<?php

namespace App\Repositories;

use App\Models\UserAppDevice;

class UserAppDeviceRepository
{
    public function updateOrCreate(array $data)
    {
        return UserAppDevice::updateOrCreate(
            ['device_id' => $data['device_id']],
            [
                'fcm_token' => $data['fcm_token'] ?? null,
                'platform' => $data['platform'],
                'last_active_at' => now()
            ]
        );
    }
}
