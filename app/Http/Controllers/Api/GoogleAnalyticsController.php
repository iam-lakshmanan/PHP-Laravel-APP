<?php

namespace App\Http\Controllers\Api;

use App\DTO\GoogleAnalyticsDTO;
use App\Http\Controllers\Controller;
use App\Services\GoogleAnalyticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GoogleAnalyticsController extends Controller
{
    protected GoogleAnalyticsService $analyticsService;

    public function __construct(GoogleAnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    public function index(Request $request): JsonResponse
    {
        $dto = GoogleAnalyticsDTO::fromRequest($request);

        $data = $this->analyticsService->getAllAnalyticsData(
            $dto->startDate,
            $dto->endDate
        );

        return response()->json([
            'status' => true,
            'message' => 'Google Analytics full data fetched successfully',
            'data' => $data,
        ]);
    }
}