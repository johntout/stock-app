<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Livewire\Features\SupportFormObjects\HandlesFormObjects;
use Staudenmeir\EloquentEagerLimit\HasEagerLimit;

class Stock extends Model
{
    use HandlesFormObjects, HasFactory, HasEagerLimit;

    protected $fillable = [
        'title',
        'symbol',
    ];

    public function timeSeries(): HasMany
    {
        return $this->hasMany(StockTimeSeries::class);
    }
}
