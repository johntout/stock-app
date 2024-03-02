<?php

namespace App\Services;

use App\Models\StockTimeSeries;

class StockPercentageService
{
    public StockTimeSeries $latestTimeSeries;

    public StockTimeSeries $previousTimeSeries;

    private array $percentageData = [];

    public function __construct(StockTimeSeries $latestTimeSeries, StockTimeSeries $previousTimeSeries)
    {
        $this->latestTimeSeries = $latestTimeSeries;
        $this->previousTimeSeries = $previousTimeSeries;
        $this->calculatePercentageData();
    }

    private function calculatePercentageData(): static
    {
        $direction = null;
        $percentage = ($this->latestTimeSeries->close - $this->previousTimeSeries->close) * 100 / $this->previousTimeSeries->close;

        if ($percentage < 0) {
            $direction = 'down';
        } elseif ($percentage > 0) {
            $direction = 'up';
        }

        $this->percentageData = [
            'percentage' => $percentage,
            'direction' => $direction,
        ];

        return $this;
    }

    public function getPercentageData(): array
    {
        return $this->percentageData;
    }
}
