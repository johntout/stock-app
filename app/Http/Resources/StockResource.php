<?php

namespace App\Http\Resources;

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
                    'open' => $latestTimeSeries?->cache?->open,
                    'high' => $latestTimeSeries?->cache?->high,
                    'low' => $latestTimeSeries?->cache?->low,
                    'close' => $latestTimeSeries?->cache?->close,
                ], null),
                'previous' => $this->when($previousTimeSeries, [
                    'timestamp' => $previousTimeSeries?->timestamp,
                    'open' => $previousTimeSeries?->cache?->open,
                    'high' => $previousTimeSeries?->cache?->high,
                    'low' => $previousTimeSeries?->cache?->low,
                    'close' => $previousTimeSeries?->cache?->close,
                ], null),
            ],
            'percentage_data' => $this->percentage_data,
        ];
    }
}
