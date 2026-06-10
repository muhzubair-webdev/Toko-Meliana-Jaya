<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\StockAdjustment;
use App\Models\StockUnit;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Display the reports page with filters and data.
     */
    public function index(Request $request)
    {
        $reportType = $request->get('type', 'penjualan');
        $month = $request->get('month', now()->format('Y-m'));
        $date = Carbon::createFromFormat('Y-m', $month);

        $data = match ($reportType) {
            'penjualan' => $this->salesReport($date),
            'stok' => $this->stockReport(),
            'adjustment' => $this->adjustmentReport($date),
            'masuk' => $this->entryReport($date),
            default => $this->salesReport($date),
        };

        return view('reports.index', array_merge($data, [
            'reportType' => $reportType,
            'month' => $month,
            'monthLabel' => $date->translatedFormat('F Y'),
        ]));
    }

    /**
     * Sales / Profit & Loss report.
     */
    private function salesReport(Carbon $date): array
    {
        $sales = Sale::with(['saleDetails.stockUnit.product', 'user'])
            ->whereMonth('sale_date', $date->month)
            ->whereYear('sale_date', $date->year)
            ->orderBy('sale_date')
            ->get();

        $totalRevenue = 0;
        $totalCost = 0;
        foreach ($sales as $sale) {
            foreach ($sale->saleDetails as $detail) {
                $totalRevenue += $detail->final_price;
                $totalCost += $detail->stockUnit->purchase_price ?? 0;
            }
        }

        $monthlyLoss = StockAdjustment::with('stockUnit')
            ->whereMonth('date', $date->month)
            ->whereYear('date', $date->year)
            ->get()
            ->sum(fn($adj) => $adj->stockUnit->purchase_price ?? 0);

        return [
            'sales' => $sales,
            'totalRevenue' => $totalRevenue,
            'totalCost' => $totalCost,
            'totalProfit' => $totalRevenue - $totalCost,
            'monthlyLoss' => $monthlyLoss,
        ];
    }

    /**
     * Current stock value report.
     */
    private function stockReport(): array
    {
        $stockUnits = StockUnit::with('product.category')
            ->where('status', 'tersedia')
            ->orderBy('product_id')
            ->get();

        $totalStockValue = $stockUnits->sum('purchase_price');

        return [
            'stockUnits' => $stockUnits,
            'totalStockValue' => $totalStockValue,
            'sales' => collect(),
            'totalRevenue' => 0,
            'totalCost' => 0,
            'totalProfit' => 0,
            'monthlyLoss' => 0,
        ];
    }

    /**
     * Adjustments (loss) report.
     */
    private function adjustmentReport(Carbon $date): array
    {
        $adjustments = StockAdjustment::with(['stockUnit.product', 'user'])
            ->whereMonth('date', $date->month)
            ->whereYear('date', $date->year)
            ->orderBy('date')
            ->get();

        $totalLoss = $adjustments->sum(fn($adj) => $adj->stockUnit->purchase_price ?? 0);

        return [
            'adjustments' => $adjustments,
            'totalLoss' => $totalLoss,
            'sales' => collect(),
            'totalRevenue' => 0,
            'totalCost' => 0,
            'totalProfit' => 0,
            'monthlyLoss' => $totalLoss,
        ];
    }

    /**
     * Goods entry report.
     */
    private function entryReport(Carbon $date): array
    {
        $entries = StockUnit::with('product.category')
            ->selectRaw('received_date, product_id, notes, purchase_price, COUNT(id) as total_units, SUM(purchase_price) as total_value')
            ->whereMonth('received_date', $date->month)
            ->whereYear('received_date', $date->year)
            ->groupBy('received_date', 'product_id', 'notes', 'purchase_price')
            ->orderBy('received_date', 'desc')
            ->get();

        $totalEntryValue = $entries->sum('total_value');

        return [
            'entries' => $entries,
            'totalEntryValue' => $totalEntryValue,
            'sales' => collect(),
            'totalRevenue' => 0,
            'totalCost' => 0,
            'totalProfit' => 0,
            'monthlyLoss' => 0,
        ];
    }

    /**
     * Export report as PDF.
     */
    public function exportPdf(Request $request)
    {
        // PDF export will be implemented with dompdf package in a later step
        return back()->with('info', 'Fitur export PDF akan segera tersedia.');
    }
}
