<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'product_name',
        'unit',
        'min_stock',
        'suggested_price',
        'image',
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

    /**
     * Get full URL for the product image, or a placeholder.
     */
    public function getImageUrlAttribute(): string
    {
        if ($this->image && Storage::disk('public')->exists($this->image)) {
            return asset('storage/' . $this->image);
        }

        // SVG placeholder as data URI
        return 'data:image/svg+xml,' . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 80 80"><rect width="80" height="80" fill="#e5e7eb"/><text x="50%" y="54%" dominant-baseline="middle" text-anchor="middle" font-family="sans-serif" font-size="11" fill="#9ca3af">No Image</text></svg>');
    }
}
