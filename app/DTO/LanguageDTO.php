<?php

namespace App\DTO;

use App\Constants\AppConstants;
use Illuminate\Http\Request;

class LanguageDTO
{
    public string $lang;
    public ?string $accent;

    public function __construct(string $lang, ?string $accent = null)
    {
        $this->lang = $lang;
        $this->accent = $accent;
    }

    public static function fromRequest(Request $request): self
    {
        // support both ?lang=en AND ?en
        $lang = $request->query('lang') ?? $request->input('lang');

        if (!$lang) {
            $lang = array_key_first($request->query()) ?? AppConstants::DEFAULT_LANGUAGE_ENGLISH;
        }

        $accent = $request->query('accent') ?? $request->input('accent');

        return new self($lang ?? AppConstants::DEFAULT_LANGUAGE_ENGLISH, $accent);
    }
}
