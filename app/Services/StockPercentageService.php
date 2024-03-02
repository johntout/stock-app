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
        $percentage = ($this->latestTimeSeries->close - $this->previousTimeSeries->close) * 100 / $this->previousTimeSeries->close;

        $this->percentageData = [
            'percentage' => $percentage,
            'direction' => $percentage < 0 ? 'down' : 'up'
        ];

        return $this;
    }

    public function getPercentageData(): array
    {
        return $this->percentageData;
    }
}
