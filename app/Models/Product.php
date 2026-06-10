<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'product_name',
        'unit',
        'min_stock',
        'suggested_price',
    ];

    protected $casts = [
        'suggested_price' => 'decimal:2',
        'min_stock' => 'integer',
    ];

    /**
     * Category this product belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * All physical stock units of this product.
     */
    public function stockUnits(): HasMany
    {
        return $this->hasMany(StockUnit::class);
    }

    /**
     * Count of available (unsold, undamaged) units.
     */
    public function getAvailableStockCountAttribute(): int
    {
        return $this->stockUnits()->where('status', 'tersedia')->count();
    }

    /**
     * Check if stock is at or below the minimum threshold.
     */
    public function getIsLowStockAttribute(): bool
    {
        return $this->available_stock_count <= $this->min_stock;
    }
}
