<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockAdjustment extends Model
{
    protected $fillable = [
        'stock_unit_id',
        'user_id',
        'type',
        'date',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    // Type constants
    const TYPE_RUSAK = 'rusak';
    const TYPE_HILANG = 'hilang';
    const TYPE_EXPIRED = 'expired';

    /**
     * The stock unit that was adjusted.
     */
    public function stockUnit(): BelongsTo
    {
        return $this->belongsTo(StockUnit::class);
    }

    /**
     * User who reported this adjustment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Financial loss from this adjustment (based on purchase price).
     */
    public function getLossAmountAttribute(): float
    {
        return $this->stockUnit->purchase_price ?? 0;
    }
}
