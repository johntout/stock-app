<?php

namespace App\Http\Controllers;

use App\Http\Resources\StockResource;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class StockController extends Controller
{
    public function __invoke(Request $request): AnonymousResourceCollection
    {
        $cache = Cache::get('stock-prices');
        $cacheKey = 'stock-prices.page.'.$request->get('page', 1);
        $stockPrices = Arr::get($cache, $cacheKey);

        if (empty($stockPrices)) {
            $stockPrices = Stock::query()->with([
                'timeSeries' => function ($query) {
                    $query->orderByDesc('timestamp')->limit(2);
                },
            ])->paginate(20);

            Arr::set($cache, $cacheKey, $stockPrices);
            Cache::put('stock-prices', $cache, 60);
        }

        return StockResource::collection($stockPrices);
    }
}
