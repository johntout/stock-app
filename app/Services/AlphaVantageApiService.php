<?php

namespace App\Services;

use App\Exceptions\AlphaVantageApiException;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class AlphaVantageApiService
{
    public string $url;

    public array $params;

    public array $data;

    /**
     * @throws \Exception
     */
    public function __construct(array $config)
    {
        if (empty($config['api_key'])) {
            throw new \Exception(message: 'Alpha Vantage api key is not defined!');
        }

        $this->url = $config['url'];
        $this->params = [
            'function' => $config['function'],
            'interval' => $config['interval'],
            'apikey' => $config['api_key'],
        ];
    }

    /**
     * @throws AlphaVantageApiException
     */
    public function getSingleStockData(string $stockSymbol, array $params = []): Response
    {
        $queryParams = array_replace_recursive($this->params, $params, ['symbol' => $stockSymbol]);

        try {
            $response = Http::get(
                $this->url.'?'.http_build_query($queryParams)
            );

            $response->throw();

            return $response;

        } catch (\Throwable $e) {
            throw new AlphaVantageApiException(previous: $e);
        }
    }

    /**
     * @throws AlphaVantageApiException
     */
    public function getMultipleStockData(array $stocksSymbols, array $params = []): array
    {
        $queryParams = array_replace_recursive($this->params, $params);
        $query = http_build_query($queryParams);

        try {
            return Http::pool(function (Pool $pool) use ($query, $stocksSymbols) {
                foreach ($stocksSymbols as $stocksSymbol) {
                    $pool->get($this->url.'?'.$query.'&symbol='.$stocksSymbol);
                }
            });
        } catch (\Throwable $e) {
            throw new AlphaVantageApiException(previous: $e);
        }
    }
}
