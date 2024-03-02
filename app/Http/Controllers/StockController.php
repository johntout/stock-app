<?php

namespace App\Http\Controllers;

use App\Http\Resources\StockResource;
use App\Services\StocksService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class StockController extends Controller
{
    public function __invoke(Request $request, StocksService $stockService): AnonymousResourceCollection
    {
        return StockResource::collection($stockService->getStocks());
    }
}
