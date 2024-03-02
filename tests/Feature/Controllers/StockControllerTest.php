<?php

namespace Tests\Feature\Jobs;

use Illuminate\Support\Facades\Cache;

test('stock controller', function () {
    Cache::shouldReceive('get')
        ->with('stock-prices')
        ->once()
        ->andReturnNull();

    Cache::shouldReceive('put')
        ->withSomeOfArgs('stock-prices', 60)
        ->once();

    $this
        ->get('/stocks')
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                [
                    'id',
                    'title',
                    'symbol',
                    'price_data' => [
                        'current',
                        'previous',
                    ],
                    'percentage_data',
                ],
            ],
            'links' => [
                'first',
                'last',
                'prev',
                'next',
            ],
            'meta' => [
                'current_page',
                'from',
                'last_page',
                'links' => [
                    [
                        'url',
                        'label',
                        'active',
                    ],
                ],
                'path',
                'per_page',
                'to',
                'total',
            ],
        ]);
});
