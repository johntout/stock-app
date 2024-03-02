<?php

namespace App\Jobs;

use App\Dtos\AlphaVantageResponseDto;
use App\Exceptions\AlphaVantageApiException;
use App\Facades\AlphaVantageApi;
use App\Models\StockTimeSeries;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateStockPrices implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::table('stocks')
            ->select(['id', 'symbol'])
            ->orderBy('id')
            ->chunk(50, function (Collection $stocks) {

                try {
                    $stockData = AlphaVantageApi::getMultipleStockData($stocks->pluck('symbol')->toArray());
                } catch (AlphaVantageApiException $e) {
                    // TODO:: send error to reporting tool
                    Log::error($e->getMessage());

                    return;
                }

                foreach ($stockData as $data) {
                    try {
                        $alphaVantageResponse = new AlphaVantageResponseDto($data->json());
                    } catch (\Throwable $e) {
                        // TODO:: send error to reporting tool
                        Log::error($e->getMessage());

                        continue;
                    }

                    $stock = $stocks->firstWhere('symbol', '=', $alphaVantageResponse->getSymbol());

                    foreach ($alphaVantageResponse->getTimestamps() as $timestamp) {
                        $timestampData = $alphaVantageResponse->getTimestampData($timestamp);

                        StockTimeSeries::query()->updateOrCreate(
                            [
                                'stock_id' => $stock->id,
                                'timestamp' => $timestamp,
                            ],
                            [
                                'open' => $timestampData['open'],
                                'high' => $timestampData['high'],
                                'low' => $timestampData['low'],
                                'close' => $timestampData['close'],
                                'volume' => $timestampData['volume'],
                            ]
                        );
                    }
                }
            });
    }
}
