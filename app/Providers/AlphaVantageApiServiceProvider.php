<?php

namespace App\Providers;

use App\Services\AlphaVantageApiService;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class AlphaVantageApiServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(AlphaVantageApiService::class, function ($app) {
            return new AlphaVantageApiService($app['config']['alpha-vantage-api']);
        });
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [AlphaVantageApiService::class];
    }
}
