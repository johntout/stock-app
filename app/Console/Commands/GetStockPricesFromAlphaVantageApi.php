<?php

namespace App\Console\Commands;

use App\Jobs\UpdateStockPrices;
use Illuminate\Console\Command;

class GetStockPricesFromAlphaVantageApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-stock-prices-from-alpha-vantage-api';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch stock prices from alpha vantage api';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        UpdateStockPrices::dispatch();
    }
}
