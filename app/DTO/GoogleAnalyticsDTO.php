<?php

namespace App\DTO;

use App\Constants\AppConstants;
use Illuminate\Http\Request;

class GoogleAnalyticsDTO
{
    public string $startDate;
    public string $endDate;

    public function __construct(
        string $startDate = AppConstants::DEFAULT_ANALYTICS_START_DATE,
        string $endDate = AppConstants::DEFAULT_ANALYTICS_END_DATE
    ) {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public static function fromRequest(Request $request): self
    {
        $startDate = $request->query('start_date')
            ?? $request->input('start_date')
            ?? AppConstants::DEFAULT_ANALYTICS_START_DATE;

        $endDate = $request->query('end_date')
            ?? $request->input('end_date')
            ?? AppConstants::DEFAULT_ANALYTICS_END_DATE;

        return new self($startDate, $endDate);
    }
}
