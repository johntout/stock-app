<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Staudenmeir\EloquentEagerLimit\HasEagerLimit;

class StockTimeSeries extends Model
{
    use HasEagerLimit, HasFactory, SoftDeletes;

    protected $fillable = [
        'stock_id',
        'timestamp', 'open', 'high',
        'low', 'close', 'volume',
    ];

    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class);
    }

    public function cache(): Attribute
    {
        return Attribute::make(
            get: function () {
                $cachedStocksTimeSeries = Cache::get('stocks-timeseries', []);
                $cachedTimeSeries = Arr::get($cachedStocksTimeSeries, $this->id);

                if (empty($cachedTimeSeries)) {
                    $cachedTimeSeries = $this;
                    Arr::set($cachedStocksTimeSeries, $this->id, $cachedTimeSeries);
                    Cache::put('stocks-timeseries', $cachedStocksTimeSeries, 60);
                }

                return $cachedTimeSeries;
            }
        );
    }

    public function refreshCache(): static
    {
        $cachedStocksTimeSeries = Cache::get('stocks-timeseries', []);

        Arr::set($cachedStocksTimeSeries, $this->id, $this);

        Cache::put('stocks-timeseries', $cachedStocksTimeSeries, 60);

        return $this;
    }
}
