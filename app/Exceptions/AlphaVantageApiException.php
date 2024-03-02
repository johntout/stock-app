<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class AlphaVantageApiException extends Exception
{
    public function __construct(string $message = 'Issue while communicating with stock api', int $code = 500, ?Throwable $previous = null)
    {
        parent::__construct($message.' with error: '.$previous?->getMessage(), $code, $previous);
    }
}
