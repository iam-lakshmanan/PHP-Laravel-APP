<?php

namespace App\Services;

use App\Constants\AppConstants;


class LanguageService
{
    protected array $allowed = [AppConstants::DEFAULT_LANGUAGE_ENGLISH, AppConstants::LANGUAGE_NAME_GERMAN, AppConstants::LANGUAGE_NAME_SPANISH, AppConstants::LANGUAGE_NAME_FRENCH];

    public function getLanguage(string $lang): array
    {
        // Always fallback to English if not allowed
        if (!in_array($lang, $this->allowed)) {
            $lang = AppConstants::DEFAULT_LANGUAGE_ENGLISH;
        }

        $path = public_path("lang/{$lang}.json");

        // Fallback if file not exists
        if (!file_exists($path)) {
            $lang = AppConstants::DEFAULT_LANGUAGE_ENGLISH;
            $path = public_path("lang/" . AppConstants::DEFAULT_LANGUAGE_ENGLISH . ".json");
        }

        $data = json_decode(file_get_contents($path), true);

        // Handle invalid JSON
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'status' => config('app_constants.STATUS.ERROR'),
                'message' => 'Invalid JSON file'
            ];
        }

        /**
         * ADD IMAGE LOGIC HERE
         */
        $files = glob(public_path("images/{$lang}/*.png"));

        $images = [];

        foreach ($files as $index => $file) {
            $images[] = [
                'page' => $index + 1,
                'url' => url("images/{$lang}/" . basename($file)),
                'path' => $file
            ];
        }


        return [
            'status' => config('app_constants.STATUS.SUCCESS'),
            'lang' => $lang,
            'data' => $data,
        ];
    }


    public function getImages(string $lang): array
    {
        $files = glob(public_path("images/{$lang}/*.png"));

        // Sort files by page number in filename
        usort($files, function ($a, $b) {
            preg_match('/page_(\d+)_image\.png$/', basename($a), $matchA);
            preg_match('/page_(\d+)_image\.png$/', basename($b), $matchB);

            $pageA = isset($matchA[1]) ? (int) $matchA[1] : 0;
            $pageB = isset($matchB[1]) ? (int) $matchB[1] : 0;

            return $pageA <=> $pageB;
        });

        $images = [];

        foreach ($files as $file) {
            preg_match('/page_(\d+)_image\.png$/', basename($file), $match);

            $pageNumber = isset($match[1]) ? (int) $match[1] : 0;

            $images[] = [
                'page' => $pageNumber,
                'url'  => url("images/{$lang}/" . basename($file)),
            ];
        }

        return $images;
    }


    public function getAudioList(string $lang, ?string $accent = null): array
    {
        $path = $accent
            ? public_path("audio/{$lang}/{$accent}")
            : public_path("audio/{$lang}");

        if (!file_exists($path)) {
            return [
                'status' => config('app_constants.STATUS.ERROR'),
                'data' => [],
                'message' => 'Audio folder not found'
            ];
        }

        $audioFiles = glob($path . '/*.mp3') ?: [];
        $allFiles   = glob($path . '/*') ?: [];

        $captionsMap = [];
        $result = [];

        // Find caption files (with or without extension)
        foreach ($allFiles as $file) {
            $fileName = basename($file);

            if (preg_match('/page_(\d+)_captions(\..+)?$/i', $fileName, $matches)) {
                $page = (int) $matches[1];

                $baseUrl = $accent
                    ? "audio/{$lang}/{$accent}"
                    : "audio/{$lang}";

                $captionsMap[$page] = asset($baseUrl . '/' . $fileName);
            }
        }

        foreach ($audioFiles as $audio) {
            $fileName = basename($audio);

            if (!preg_match('/Page_(\d+)_.*\.mp3$/i', $fileName, $matches)) {
                continue;
            }

            $page = (int) $matches[1];

            $baseUrl = $accent
                ? "audio/{$lang}/{$accent}"
                : "audio/{$lang}";

            $result[] = [
                'page' => $page,
                'audio' => asset($baseUrl . '/' . $fileName),
                'captions' => $captionsMap[$page] ?? null,
            ];
        }

        usort($result, fn($a, $b) => $a['page'] <=> $b['page']);

        return [
            'status' => config('app_constants.STATUS.SUCCESS'),
            'data' => $result
        ];
    }
}
