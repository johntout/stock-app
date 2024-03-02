<?php

namespace App\Http\Controllers;

use App\Http\Resources\StockResource;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;

class StockPriceDataController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): AnonymousResourceCollection
    {
        $currentPage = $request->input('page', 1);

        $stocks = Cache::tags(['stock-price-data'])->remember('page-'.$currentPage, 60, function () {
            return Stock::query()->with([
                'timeSeries' => function ($query) {
                    $query->orderByDesc('timestamp')->limit(2);
                }
            ])->paginate(20);
        });

        return StockResource::collection($stocks);
    }
}
