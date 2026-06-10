<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    protected $fillable = [
        'user_id',
        'invoice_number',
        'sale_date',
        'total_price',
    ];

    protected $casts = [
        'sale_date' => 'date',
        'total_price' => 'decimal:2',
    ];

    /**
     * User (staff/admin) who handled this sale.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Line items (sold units) in this sale.
     */
    public function saleDetails(): HasMany
    {
        return $this->hasMany(SaleDetail::class);
    }

    /**
     * Generate a unique invoice number.
     * Format: INV-YYYYMMDD-NNN
     */
    public static function generateInvoiceNumber(?string $date = null): string
    {
        $dateObj = $date ? \Carbon\Carbon::parse($date) : now();
        $dateCode = $dateObj->format('Ymd');
        $prefix = "INV-{$dateCode}-";

        $lastSale = self::where('invoice_number', 'like', "{$prefix}%")
            ->orderByDesc('invoice_number')
            ->first();

        if ($lastSale) {
            $lastNumber = (int) substr($lastSale->invoice_number, strlen($prefix));
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Calculate total profit for this sale.
     * Profit = SUM(final_price) - SUM(purchase_price of sold units)
     */
    public function getProfitAttribute(): float
    {
        $totalRevenue = $this->saleDetails->sum('final_price');
        $totalCost = $this->saleDetails->sum(function ($detail) {
            return $detail->stockUnit->purchase_price ?? 0;
        });

        return $totalRevenue - $totalCost;
    }
}
