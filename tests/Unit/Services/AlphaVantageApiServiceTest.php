<?php

namespace Tests\Unit\Services;

use App\Facades\AlphaVantageApi;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    Http::fake();

    $this->params = [
        'function' => config('alpha-vantage-api.function'),
        'interval' => config('alpha-vantage-api.interval'),
        'apikey' => config('alpha-vantage-api.api_key'),
    ];
});

test('get single stock data', function () {
    AlphaVantageApi::getSingleStockData('IBM');

    Http::assertSent(function (Request $request) {
        return $request->url() == config('alpha-vantage-api.url').'?'.http_build_query($this->params).'&symbol=IBM';
    });

    Http::assertSentCount(1);
});

test('get multiple stock data', function () {
    AlphaVantageApi::getMultipleStockData(['IBMs', 'MSFTs']);

    Http::assertSentCount(2);
});
