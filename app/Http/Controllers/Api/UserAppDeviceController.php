<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserAppDeviceRequest;
use App\Services\UserAppDeviceService;
use App\DTO\UserAppDeviceDTO;
use App\Jobs\SendAppUpdateNotificationJob;
use Illuminate\Http\Request;

class UserAppDeviceController extends Controller
{
    protected $service;

    public function __construct(UserAppDeviceService $service)
    {
        $this->service = $service;
    }

    public function init(UserAppDeviceRequest $request)
    {
        $dto = UserAppDeviceDTO::fromRequest($request);

        $device = $this->service->storeOrUpdate($dto);

        return response()->json([
            'status' => true,
            'message' => 'Device initialized successfully',
            'data' => $device
        ]);
    }

    public function sendUpdateNotification()
    {
        $title = 'App Update Available';
        $message = 'A new update for the app is available. Please update to the latest';
        SendAppUpdateNotificationJob::dispatch(
            $title,
            $message
        );


        return response()->json([
            'status' => true,
            'message' => 'Notification sent successfully'
        ]);
    }
}
