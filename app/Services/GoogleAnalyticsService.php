<?php

namespace App\Services;

use App\Constants\AppConstants;
use Google\Analytics\Data\V1beta\Client\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\RunReportRequest;
use RuntimeException;

class GoogleAnalyticsService
{
    protected BetaAnalyticsDataClient $client;
    protected string $propertyId;

    public function __construct()
    {
        $credentialsPath = storage_path(AppConstants::GA4_CREDENTIALS_PATH);
        $propertyId = env('GA4_PROPERTY_ID');

        if (!$propertyId) {
            throw new RuntimeException('GA4_PROPERTY_ID is not configured in .env file.');
        }

        if (!file_exists($credentialsPath)) {
            throw new RuntimeException('Google Analytics key file not found at: ' . $credentialsPath);
        }

        $this->propertyId = $propertyId;

        $this->client = new BetaAnalyticsDataClient([
            'credentials' => $credentialsPath,
        ]);
    }

    protected function runCustomReport(
        array $dimensions,
        array $metrics,
        string $startDate = AppConstants::DEFAULT_ANALYTICS_START_DATE,
        string $endDate = AppConstants::DEFAULT_ANALYTICS_END_DATE,
        int $limit = AppConstants::DEFAULT_ANALYTICS_LIMIT
    ): array {
        $request = new RunReportRequest([
            'property' => AppConstants::GA4_PROPERTY_PREFIX . $this->propertyId,
            'date_ranges' => [
                new DateRange([
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ]),
            ],
            'dimensions' => array_map(
                fn (string $dimension): Dimension => new Dimension(['name' => $dimension]),
                $dimensions
            ),
            'metrics' => array_map(
                fn (string $metric): Metric => new Metric(['name' => $metric]),
                $metrics
            ),
            'limit' => $limit,
        ]);

        $response = $this->client->runReport($request);

        $rows = [];

        foreach ($response->getRows() as $row) {
            $item = [];

            foreach ($dimensions as $index => $dimensionName) {
                $item[$dimensionName] = $row->getDimensionValues()[$index]->getValue() ?? null;
            }

            foreach ($metrics as $index => $metricName) {
                $item[$metricName] = $row->getMetricValues()[$index]->getValue() ?? null;
            }

            $rows[] = $item;
        }

        return $rows;
    }

    public function getAllAnalyticsData(
        string $startDate = AppConstants::DEFAULT_ANALYTICS_START_DATE,
        string $endDate = AppConstants::DEFAULT_ANALYTICS_END_DATE
    ): array {
        $limit = AppConstants::DEFAULT_ANALYTICS_LIMIT;

        return [
            'overview' => $this->runCustomReport(
                ['date'],
                ['activeUsers', 'newUsers', 'sessions', 'engagedSessions', 'screenPageViews', 'eventCount'],
                $startDate,
                $endDate,
                $limit
            ),

            'languages' => $this->runCustomReport(
                ['languageCode'],
                ['activeUsers', 'newUsers', 'sessions'],
                $startDate,
                $endDate,
                $limit
            ),

            'countries' => $this->runCustomReport(
                ['country'],
                ['activeUsers', 'newUsers', 'sessions'],
                $startDate,
                $endDate,
                $limit
            ),

            'devices' => $this->runCustomReport(
                ['deviceCategory', 'operatingSystem', 'browser'],
                ['activeUsers', 'sessions', 'screenPageViews'],
                $startDate,
                $endDate,
                $limit
            ),

            'pages' => $this->runCustomReport(
                ['pageTitle', 'pagePath'],
                ['screenPageViews', 'activeUsers', 'sessions'],
                $startDate,
                $endDate,
                $limit
            ),

            'events' => $this->runCustomReport(
                ['eventName'],
                ['eventCount', 'activeUsers'],
                $startDate,
                $endDate,
                $limit
            ),

            'traffic_sources' => $this->runCustomReport(
                ['sessionSource', 'sessionMedium'],
                ['sessions', 'activeUsers', 'newUsers'],
                $startDate,
                $endDate,
                $limit
            ),
        ];
    }
}
