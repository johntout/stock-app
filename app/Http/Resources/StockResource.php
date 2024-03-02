<?php

namespace App\Http\Resources;

use App\Services\StockPercentageService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $latestTimeSeries = $this->timeSeries->get(0);
        $previousTimeSeries = $this->timeSeries->get(1);

        return [
            'id' => $this->id,
            'title' => $this->title,
            'symbol' => $this->symbol,
            'price_data' => [
                'current' => $this->when($latestTimeSeries, [
                    'timestamp' => $latestTimeSeries?->timestamp,
                    'open' => $latestTimeSeries?->open,
                    'high' => $latestTimeSeries?->high,
                    'low' => $latestTimeSeries?->low,
                    'close' => $latestTimeSeries?->close,
                ], null),
                'previous' => $this->when($previousTimeSeries, [
                    'timestamp' => $previousTimeSeries?->timestamp,
                    'open' => $previousTimeSeries?->open,
                    'high' => $previousTimeSeries?->high,
                    'low' => $previousTimeSeries?->low,
                    'close' => $previousTimeSeries?->close,
                ], null)
            ],
            'percentage_data' => $this->percentageData($latestTimeSeries, $previousTimeSeries)
        ];
    }

    private function percentageData($latestTimeSeries, $previousTimeSeries): ?array
    {
        if (! $latestTimeSeries || ! $previousTimeSeries) {
            return null;
        }

        $stockPercentage = new StockPercentageService($latestTimeSeries, $previousTimeSeries);

        return $stockPercentage->getPercentageData();
    }
}
