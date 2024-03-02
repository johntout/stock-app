<?php

namespace Tests\Unit\Services;

use App\Models\StockTimeSeries;
use App\Services\StockPercentageService;

test('calculate percentage data', function () {
    $latestStockTimeSeries = StockTimeSeries::factory()->create([
        'close' => 1,
        'timestamp' => now(),
    ]);

    $previousStockTimeSeries = StockTimeSeries::factory()->create([
        'close' => 2,
        'timestamp' => now()->subHour(),
    ]);

    $stockPercentage = new StockPercentageService(
        latestTimeSeries: $latestStockTimeSeries,
        previousTimeSeries: $previousStockTimeSeries
    );

    expect($stockPercentage->getPercentageData())->toBe([
        'percentage' => -50,
        'direction' => 'down',
    ]);
});
