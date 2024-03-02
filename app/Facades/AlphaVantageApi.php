<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static getSingleStockData(string $stockSymbol, array $params = [])
 * @method static getMultipleStockData(array $stockSymbols, array $params = [])
 */
class AlphaVantageApi extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \App\Services\AlphaVantageApiService::class;
    }
}
