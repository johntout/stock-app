<?php

namespace App\Services;

use App\Models\Stock;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class StocksService
{
    private mixed $stocks;

    public function __construct()
    {
        $cache = Cache::get('stock-prices');
        $cacheKey = 'stock-prices.page.'.request()->get('page', 1);
        $this->stocks = Arr::get($cache, $cacheKey);

        if (empty($this->stocks)) {
            $this->stocks = Stock::query()->with([
                'timeSeries' => function ($query) {
                    $query->orderByDesc('timestamp')->limit(2);
                },
            ])->paginate(20);

            Arr::set($cache, $cacheKey, $this->stocks);
            Cache::put('stock-prices', $cache, 60);
        }
    }

    public function getStocks(): LengthAwarePaginator
    {
        return $this->stocks;
    }
}
