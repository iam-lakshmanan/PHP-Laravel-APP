<?php

namespace App\Http\Controllers\Api;

use App\DTO\LanguageDTO;
use App\Http\Controllers\Controller;
use App\Services\LanguageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    protected LanguageService $languageService;

    public function __construct(LanguageService $languageService)
    {
        $this->languageService = $languageService;
    }

    public function index(Request $request): JsonResponse
    {
        $dto = LanguageDTO::fromRequest($request);

        $result = $this->languageService->getLanguage($dto->lang);

        if (!$result['status']) {
            return response()->json([
                'error' => $result['message'],
            ], 400);
        }

        return response()->json([
            'lang' => $result['lang'],
            'data' => $result['data'],
        ]);
    }

    public function getImages(Request $request): JsonResponse
    {
        $dto = LanguageDTO::fromRequest($request);

        $result = $this->languageService->getImages($dto->lang);

        return response()->json($result);
    }

    public function getAudio(Request $request): JsonResponse
    {
        $dto = LanguageDTO::fromRequest($request);

        $result = $this->languageService->getAudioList(
            $dto->lang,
            $dto->accent
        );

        return response()->json($result['data']);
    }
}