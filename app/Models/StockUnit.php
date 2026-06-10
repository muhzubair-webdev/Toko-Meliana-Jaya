<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class StockUnit extends Model
{
    protected $fillable = [
        'product_id',
        'qr_code',
        'purchase_price',
        'status',
        'received_date',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'received_date' => 'date',
    ];

    // Status constants
    const STATUS_TERSEDIA = 'tersedia';
    const STATUS_TERJUAL = 'terjual';
    const STATUS_RUSAK = 'rusak';
    const STATUS_HILANG = 'hilang';

    /**
     * Product this unit belongs to.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Sale detail if this unit was sold.
     */
    public function saleDetail(): HasOne
    {
        return $this->hasOne(SaleDetail::class);
    }

    /**
     * Stock adjustment if this unit was marked damaged/lost.
     */
    public function stockAdjustment(): HasOne
    {
        return $this->hasOne(StockAdjustment::class);
    }

    /**
     * Check if unit is available for sale.
     */
    public function isAvailable(): bool
    {
        return $this->status === self::STATUS_TERSEDIA;
    }

    /**
     * Generate a unique QR code for this stock unit.
     * Format: [KODE_TOKO]-[KATEGORI]-[TAHUN/BULAN]-[NOMOR_URUT]
     * Example: MJ-LST-2605-001
     */
    public static function generateQrCode(Product $product, ?string $date = null): string
    {
        $storeCode = 'MJ'; // Meliana Jaya
        $categoryCode = match (strtolower($product->category->name)) {
            'listrik' => 'LST',
            'plastik' => 'PLK',
            default => strtoupper(substr($product->category->name, 0, 3)),
        };

        $dateObj = $date ? \Carbon\Carbon::parse($date) : now();
        $dateCode = $dateObj->format('ym'); // e.g. 2605 = May 2026

        // Find next sequence number for this category+month combo
        $prefix = "{$storeCode}-{$categoryCode}-{$dateCode}-";
        $lastUnit = self::where('qr_code', 'like', "{$prefix}%")
            ->orderByDesc('qr_code')
            ->first();

        if ($lastUnit) {
            $lastNumber = (int) substr($lastUnit->qr_code, strlen($prefix));
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}
