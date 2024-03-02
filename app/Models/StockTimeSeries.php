<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Staudenmeir\EloquentEagerLimit\HasEagerLimit;

class StockTimeSeries extends Model
{
    use HasFactory, SoftDeletes, HasEagerLimit;

    protected $fillable = [
        'stock_id',
        'timestamp', 'open', 'high',
        'low', 'close', 'volume',
    ];

    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class);
    }
}
