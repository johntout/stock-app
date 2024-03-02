<?php

namespace Tests\Unit\Commands;

use App\Console\Commands\GetStockPricesFromAlphaVantageApi;
use App\Jobs\UpdateStockPrices;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Bus;

test('command dispatches job', function () {
    Bus::fake();

    Artisan::call(GetStockPricesFromAlphaVantageApi::class);

    Bus::assertDispatched(UpdateStockPrices::class);
});
