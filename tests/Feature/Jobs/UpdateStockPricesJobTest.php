<?php

namespace Tests\Feature\Jobs;

use App\Exceptions\AlphaVantageApiException;
use App\Jobs\UpdateStockPrices;
use App\Models\Stock;
use App\Models\StockTimeSeries;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->apiUrl = Str::of(config('alpha-vantage-api.url'))->remove('/query')->value();
    $this->params = [
        'function' => config('alpha-vantage-api.function'),
        'interval' => config('alpha-vantage-api.interval'),
        'apikey' => config('alpha-vantage-api.api_key'),
    ];

    $this->ibmStock = Stock::query()->firstWhere(['symbol' => 'IBM']);
    $this->microsoftStock = Stock::query()->firstWhere(['symbol' => 'MSFT']);

    StockTimeSeries::factory()->create([
        'stock_id' => $this->ibmStock->id,
        'timestamp' => '2024-02-29 18:55:00',
        'open' => 112
    ]);
});

test('update stock prices handle method with successful call', function () {
    Http::fake([
        $this->apiUrl.'/*' => Http::sequence()
            ->push([
                'Meta Data' => [
                    '2. Symbol' => 'IBM',
                ],
                'Time Series ('.config('alpha-vantage-api.interval').')' => [
                    '2024-02-29 19:55:00' => [
                        '1. open' => '185.3000',
                        '2. high' => '185.3000',
                        '3. low' => '185.0000',
                        '4. close' => '185.2000',
                        '5. volume' => '32',
                    ],
                    '2024-02-27 20:00:00' => [
                        '1. open' => '187.3000',
                        '2. high' => '188.3000',
                        '3. low' => '181.0000',
                        '4. close' => '186.2000',
                        '5. volume' => '42',
                    ],
                ],
            ])
            ->whenEmpty(Http::response(
                [
                    'Meta Data' => [
                        '2. Symbol' => 'MSFT',
                    ],
                    'Time Series ('.config('alpha-vantage-api.interval').')' => [
                        '2024-02-29 19:55:00' => [
                            '1. open' => '185.3000',
                            '2. high' => '185.3000',
                            '3. low' => '185.0000',
                            '4. close' => '185.2000',
                            '5. volume' => '32',
                        ],
                        '2024-02-27 20:00:00' => [
                            '1. open' => '187.3000',
                            '2. high' => '188.3000',
                            '3. low' => '181.0000',
                            '4. close' => '186.2000',
                            '5. volume' => '42',
                        ],
                    ],
                ]
            )),
    ]);

    (new UpdateStockPrices)->handle();

    Http::assertSentCount(11);

    $this->assertDatabaseHas('stock_time_series', [
        'stock_id' => $this->ibmStock->id,
        'timestamp' => '2024-02-29 19:55:00',
        'open' => '185.3000',
        'high' => '185.3000',
        'low' => '185.0000',
        'close' => '185.2000',
        'volume' => '32',
    ]);

    $this->assertDatabaseHas('stock_time_series', [
        'stock_id' => $this->ibmStock->id,
        'timestamp' => '2024-02-27 20:00:00',
        'open' => '187.3000',
        'high' => '188.3000',
        'low' => '181.0000',
        'close' => '186.2000',
        'volume' => '42',
    ]);

    $this->assertDatabaseHas('stock_time_series', [
        'stock_id' => $this->microsoftStock->id,
        'timestamp' => '2024-02-29 19:55:00',
        'open' => '185.3000',
        'high' => '185.3000',
        'low' => '185.0000',
        'close' => '185.2000',
        'volume' => '32',
    ]);

    $this->assertDatabaseHas('stock_time_series', [
        'stock_id' => $this->microsoftStock->id,
        'timestamp' => '2024-02-27 20:00:00',
        'open' => '187.3000',
        'high' => '188.3000',
        'low' => '181.0000',
        'close' => '186.2000',
        'volume' => '42',
    ]);

    $microsoftFirstTimeSeries = $this->microsoftStock->timeSeries()->orderByDesc('timestamp')->first();
    $imbFirstTimeSeries = $this->ibmStock->timeSeries()->orderByDesc('timestamp')->first();

    expect($this->ibmStock->timeSeries()->count())->toBe(3)
        ->and($this->microsoftStock->timeSeries()->count())->toBe(2)
        ->and($microsoftFirstTimeSeries->cache)->not->toBeEmpty()
        ->and($imbFirstTimeSeries->cache)->not->toBeEmpty()
        ->and($imbFirstTimeSeries->cache->open)->toBe(185.3)
        ->and($imbFirstTimeSeries->cache->close)->toBe(185.2000);
});

test('update stock prices handle method with unsuccessful call', function () {

    Http::fake([
        $this->apiUrl.'/*' => new AlphaVantageApiException,
    ]);

    (new UpdateStockPrices)->handle();

    Http::assertSentCount(0);

    expect($this->ibmStock->timeSeries()->count())->toBe(1)
        ->and($this->microsoftStock->timeSeries()->count())->toBe(0);
});
