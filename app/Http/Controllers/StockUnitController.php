<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\StockUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockUnitController extends Controller
{
    /**
     * Display the stock management page.
     */
    public function index(Request $request)
    {
        $query = StockUnit::with(['product.category', 'stockAdjustment.user']);

        // Filter by product
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by QR code
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('qr_code', 'like', '%' . $request->search . '%')
                  ->orWhereHas('product', function ($pq) use ($request) {
                      $pq->where('product_name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        $stockUnits = $query->orderByDesc('created_at')->paginate(25);
        $products = Product::orderBy('product_name')->get();

        // Status counts for summary bar
        $statusCounts = StockUnit::selectRaw("status, COUNT(*) as count")
            ->groupBy('status')
            ->pluck('count', 'status');

        return view('stock.index', compact('stockUnits', 'products', 'statusCounts'));
    }

    /**
     * Store new inbound stock units (barang masuk).
     * Creates multiple StockUnit records with auto-generated QR codes.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:100',
            'purchase_price' => 'required|numeric|min:0',
            'received_date' => 'required|date',
            'notes' => 'nullable|string|max:255',
        ]);

        $product = Product::with('category')->findOrFail($validated['product_id']);
        $createdUnits = [];

        for ($i = 0; $i < $validated['quantity']; $i++) {
            $qrCode = StockUnit::generateQrCode($product, $validated['received_date']);

            $unit = StockUnit::create([
                'product_id' => $validated['product_id'],
                'qr_code' => $qrCode,
                'purchase_price' => $validated['purchase_price'],
                'status' => StockUnit::STATUS_TERSEDIA,
                'received_date' => $validated['received_date'],
                'notes' => $validated['notes'] ?? null,
            ]);

            $createdUnits[] = $unit;
        }

        return redirect()->route('stock.index')
            ->with('success', count($createdUnits) . ' unit berhasil ditambahkan dengan QR Code.');
    }

    /**
     * API: Find a stock unit by QR code (for scanner).
     */
    public function findByQr(Request $request)
    {
        $qrCode = $request->get('code', '');
        $productId = $request->get('product_id', '');

        if ($productId) {
            // Manual search: find first available unit for this product
            $unit = StockUnit::with('product.category')
                ->where('product_id', $productId)
                ->where('status', 'tersedia')
                ->first();
        } else {
            // QR scan: find by exact QR code
            $unit = StockUnit::with('product.category')
                ->where('qr_code', $qrCode)
                ->first();
        }

        if (!$unit) {
            return response()->json(['error' => 'Unit tidak ditemukan atau tidak tersedia.'], 404);
        }

        return response()->json([
            'id' => $unit->id,
            'qr_code' => $unit->qr_code,
            'product_name' => $unit->product->product_name,
            'category' => $unit->product->category->name,
            'unit' => $unit->product->unit,
            'purchase_price' => $unit->purchase_price,
            'suggested_price' => $unit->product->suggested_price,
            'status' => $unit->status,
            'received_date' => $unit->received_date->format('Y-m-d'),
        ]);
    }

    /**
     * Show printable QR code for a unit.
     */
    public function printQr(StockUnit $stockUnit)
    {
        $stockUnit->load('product.category');
        return view('stock.print-qr', compact('stockUnit'));
    }

    /**
     * Mark a stock unit as damaged/lost/expired (inline adjustment from stock page).
     */
    public function adjust(Request $request, StockUnit $stockUnit)
    {
        $validated = $request->validate([
            'type' => 'required|in:rusak,hilang,expired',
            'notes' => 'nullable|string|max:500',
        ]);

        if (!$stockUnit->isAvailable()) {
            return back()->withErrors([
                'adjust' => "Unit {$stockUnit->qr_code} tidak tersedia (status: {$stockUnit->status})."
            ]);
        }

        DB::transaction(function () use ($validated, $request, $stockUnit) {
            StockAdjustment::create([
                'stock_unit_id' => $stockUnit->id,
                'user_id' => $request->user()->id,
                'type' => $validated['type'],
                'date' => now()->toDateString(),
                'notes' => $validated['notes'] ?? null,
            ]);

            $stockUnit->update(['status' => $validated['type']]);
        });

        $typeLabel = match($validated['type']) {
            'rusak' => 'Rusak',
            'hilang' => 'Hilang',
            'expired' => 'Expired',
        };

        return redirect()->route('stock.index', $request->only(['search', 'status', 'product_id', 'page']))
            ->with('success', "Unit {$stockUnit->qr_code} berhasil ditandai sebagai {$typeLabel}.");
    }
}
