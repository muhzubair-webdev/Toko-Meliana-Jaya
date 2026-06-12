<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\StockAdjustment;
use App\Models\StockUnit;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();

        // Laba bulan ini: SUM(final_price) - SUM(purchase_price) for sold units this month
        $monthlySales = Sale::with('saleDetails.stockUnit')
            ->whereMonth('sale_date', $now->month)
            ->whereYear('sale_date', $now->year)
            ->get();

        $monthlyRevenue = 0;
        $monthlyCost = 0;
        foreach ($monthlySales as $sale) {
            foreach ($sale->saleDetails as $detail) {
                $monthlyRevenue += $detail->final_price;
                $monthlyCost += $detail->stockUnit->purchase_price ?? 0;
            }
        }
        $monthlyProfit = $monthlyRevenue - $monthlyCost;

        // Penjualan hari ini
        $todaySales = Sale::whereDate('sale_date', $now->toDateString())->get();
        $todayTransactionCount = $todaySales->count();
        $todayRevenue = $todaySales->sum('total_price');

        // Kerugian bulan ini (adjustments)
        $monthlyLoss = StockAdjustment::with('stockUnit')
            ->whereMonth('date', $now->month)
            ->whereYear('date', $now->year)
            ->get()
            ->sum(function ($adj) {
                return $adj->stockUnit->purchase_price ?? 0;
            });

        // Produk dengan stok menipis
        $lowStockProducts = Product::with('category')
            ->withCount(['stockUnits as available_count' => function ($query) {
                $query->where('status', 'tersedia');
            }])
            ->get()
            ->filter(function ($product) {
                return $product->available_count <= $product->min_stock && $product->min_stock > 0;
            })
            ->sortBy('available_count');

        // All products for listing
        $products = Product::with('category')
            ->withCount(['stockUnits as available_count' => function ($query) {
                $query->where('status', 'tersedia');
            }])
            ->orderBy('product_name')
            ->paginate(15);

        return view('dashboard', compact(
            'monthlyProfit',
            'monthlyRevenue',
            'monthlyCost',
            'monthlyLoss',
            'todayTransactionCount',
            'todayRevenue',
            'lowStockProducts',
            'products',
            'now'
        ));
    }
}
