<?php

namespace App\Models;

use App\Services\StockPercentageService;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Livewire\Features\SupportFormObjects\HandlesFormObjects;
use Staudenmeir\EloquentEagerLimit\HasEagerLimit;

class Stock extends Model
{
    use HandlesFormObjects, HasEagerLimit, HasFactory;

    protected $fillable = [
        'title',
        'symbol',
    ];

    public function timeSeries(): HasMany
    {
        return $this->hasMany(StockTimeSeries::class);
    }

    public function percentageData(): Attribute
    {
        return Attribute::make(
            get: function () {
                $latestTimeSeries = $this->timeSeries->get(0);
                $previousTimeSeries = $this->timeSeries->get(1);

                if (! $latestTimeSeries || ! $previousTimeSeries) {
                    return null;
                }

                $stockPercentage = new StockPercentageService($latestTimeSeries, $previousTimeSeries);

                return $stockPercentage->getPercentageData();
            }
        );
    }
}
