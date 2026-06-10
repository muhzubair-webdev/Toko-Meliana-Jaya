<?php

namespace App\Http\Controllers;

use App\Models\StockAdjustment;
use App\Models\StockUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdjustmentController extends Controller
{
    /**
     * Show the adjustment creation form.
     */
    public function create()
    {
        return view('adjustments.create');
    }

    /**
     * Store a new stock adjustment (damaged/lost/expired).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'stock_unit_id' => 'required|exists:stock_units,id',
            'type' => 'required|in:rusak,hilang,expired',
            'notes' => 'nullable|string|max:500',
        ]);

        $unit = StockUnit::findOrFail($validated['stock_unit_id']);

        if (!$unit->isAvailable()) {
            return back()->withErrors([
                'stock_unit_id' => "Unit {$unit->qr_code} tidak tersedia (status: {$unit->status})."
            ]);
        }

        DB::transaction(function () use ($validated, $request, $unit) {
            StockAdjustment::create([
                'stock_unit_id' => $validated['stock_unit_id'],
                'user_id' => $request->user()->id,
                'type' => $validated['type'],
                'date' => now()->toDateString(),
                'notes' => $validated['notes'] ?? null,
            ]);

            // Update stock unit status to match adjustment type
            $unit->update(['status' => $validated['type']]);
        });

        return redirect()->route('adjustments.create')
            ->with('success', 'Penyesuaian stok berhasil dicatat.');
    }

    /**
     * Display adjustment history (admin only).
     */
    public function index(Request $request)
    {
        $query = StockAdjustment::with(['stockUnit.product', 'user'])
            ->orderByDesc('date')
            ->orderByDesc('id');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $adjustments = $query->paginate(20);

        return view('adjustments.index', compact('adjustments'));
    }
}
