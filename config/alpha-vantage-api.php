<?php

return [
    'url' => env('STOCK_API_URL'),
    'api_key' => env('STOCK_API_KEY', 'demo'),
    'function' => env('STOCK_API_FUNCTION', 'TIME_SERIES_INTRADAY'),
    'interval' => env('STOCK_API_INTERVAL', '1min'),
];
