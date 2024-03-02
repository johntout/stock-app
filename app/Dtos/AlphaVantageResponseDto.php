<?php

namespace App\Dtos;

class AlphaVantageResponseDto
{
    private string $symbol;

    private array $timestamps;

    public function __construct(array $data)
    {
        $this->symbol = $data['Meta Data']['2. Symbol'];
        $this->timestamps = $data['Time Series ('.config('alpha-vantage-api.interval').')'];
    }

    public function getSymbol()
    {
        return $this->symbol;
    }

    public function getTimestamps()
    {
        return array_keys($this->timestamps);
    }

    public function getTimestampData(string $timestamp)
    {
        return [
            'open' => $this->timestamps[$timestamp]['1. open'],
            'high' => $this->timestamps[$timestamp]['2. high'],
            'low' => $this->timestamps[$timestamp]['3. low'],
            'close' => $this->timestamps[$timestamp]['4. close'],
            'volume' => $this->timestamps[$timestamp]['5. volume'],
        ];
    }
}
