<?php

namespace Tests\Unit\Services;

use App\Models\Stock;
use App\Services\StocksService;

test('get stocks', function () {

    $stocksService = new StocksService();

    expect($stocksService->getStocks()->count())
        ->toBe(Stock::query()->paginate(20)->count());
});

