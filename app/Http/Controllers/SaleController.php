<?php

namespace App\Http\Controllers;

use App\Mail\LowStockAlert;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\StockUnit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SaleController extends Controller
{
    /**
     * Show the POS / sale creation form.
     */
    public function create()
    {
        return view('sales.create');
    }

    /**
     * Store a new sale transaction.
     * Expects JSON array of items: [{stock_unit_id, final_price}]
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.stock_unit_id' => 'required|exists:stock_units,id',
            'items.*.final_price' => 'required|numeric|min:0',
        ]);

        // Verify all units are available
        $unitIds = collect($validated['items'])->pluck('stock_unit_id');
        $units = StockUnit::whereIn('id', $unitIds)->get();

        foreach ($units as $unit) {
            if (!$unit->isAvailable()) {
                return back()->withErrors([
                    'items' => "Unit {$unit->qr_code} tidak tersedia (status: {$unit->status})."
                ]);
            }
        }

        DB::transaction(function () use ($validated, $request) {
            $totalPrice = collect($validated['items'])->sum('final_price');

            $sale = Sale::create([
                'user_id' => $request->user()->id,
                'invoice_number' => Sale::generateInvoiceNumber(),
                'sale_date' => now()->toDateString(),
                'total_price' => $totalPrice,
            ]);

            foreach ($validated['items'] as $item) {
                SaleDetail::create([
                    'sale_id' => $sale->id,
                    'stock_unit_id' => $item['stock_unit_id'],
                    'final_price' => $item['final_price'],
                ]);

                // Mark the stock unit as sold
                StockUnit::where('id', $item['stock_unit_id'])
                    ->update(['status' => StockUnit::STATUS_TERJUAL]);
            }
        });

        // Check for low stock on the products that were just sold
        $affectedProductIds = $units->pluck('product_id')->unique();
        $lowStockProducts = Product::with('category')
            ->whereIn('id', $affectedProductIds)
            ->where('min_stock', '>', 0)
            ->withCount(['stockUnits as available_count' => function ($query) {
                $query->where('status', 'tersedia');
            }])
            ->get()
            ->filter(fn($p) => $p->available_count <= $p->min_stock);

        if ($lowStockProducts->isNotEmpty()) {
            $admins = User::where('role', User::ROLE_ADMIN)->get();
            foreach ($admins as $admin) {
                try {
                    Mail::to($admin->email)->send(new LowStockAlert($lowStockProducts));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Gagal mengirim email notifikasi stok menipis ke ' . $admin->email . ': ' . $e->getMessage());
                }
            }
        }

        return redirect()->route('sales.create')
            ->with('success', 'Transaksi berhasil disimpan!');
    }

    /**
     * Display sales history.
     */
    public function index(Request $request)
    {
        $query = Sale::with(['saleDetails.stockUnit.product', 'user'])
            ->orderByDesc('sale_date')
            ->orderByDesc('id');

        if ($request->filled('date')) {
            $query->whereDate('sale_date', $request->date);
        }

        $sales = $query->paginate(20);

        return view('sales.index', compact('sales'));
    }
}
