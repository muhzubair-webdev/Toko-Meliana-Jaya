<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleDetail extends Model
{
    protected $fillable = [
        'sale_id',
        'stock_unit_id',
        'final_price',
    ];

    protected $casts = [
        'final_price' => 'decimal:2',
    ];

    /**
     * Parent sale transaction.
     */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * The specific stock unit that was sold.
     */
    public function stockUnit(): BelongsTo
    {
        return $this->belongsTo(StockUnit::class);
    }

    /**
     * Profit on this specific item.
     */
    public function getProfitAttribute(): float
    {
        return $this->final_price - ($this->stockUnit->purchase_price ?? 0);
    }
}
