<?php

use App\Http\Controllers\AdjustmentController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\StockUnitController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

// ─── Authenticated Routes ─────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('verified')
        ->name('dashboard');

    // Profile (Breeze default)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ─── Products (Admin only) ───────────────────────────────────────
    Route::middleware('admin')->group(function () {
        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    });

    // ─── Stock Units (Admin only) ────────────────────────────────────
    Route::middleware('admin')->group(function () {
        Route::get('/stock', [StockUnitController::class, 'index'])->name('stock.index');
        Route::post('/stock', [StockUnitController::class, 'store'])->name('stock.store');
        Route::get('/stock/{stockUnit}/print-qr', [StockUnitController::class, 'printQr'])->name('stock.printQr');
        Route::post('/stock/{stockUnit}/adjust', [StockUnitController::class, 'adjust'])->name('stock.adjust');
    });

    // ─── Sales (All users can create sales) ──────────────────────────
    Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
    Route::get('/sales/create', [SaleController::class, 'create'])->name('sales.create');
    Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');

    // ─── Adjustments (Admin only for create, all can view history) ───
    Route::get('/adjustments', [AdjustmentController::class, 'index'])->name('adjustments.index')->middleware('admin');
    Route::get('/adjustments/create', [AdjustmentController::class, 'create'])->name('adjustments.create')->middleware('admin');
    Route::post('/adjustments', [AdjustmentController::class, 'store'])->name('adjustments.store')->middleware('admin');

    // ─── Categories (Admin only) ─────────────────────────────────────
    Route::middleware('admin')->group(function () {
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    });

    // ─── Reports (Admin: full access, Staff: limited) ────────────────
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index')->middleware('admin');
    Route::get('/reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.exportPdf')->middleware('admin');

    // ─── API Endpoints (JSON, for AJAX/Scanner) ──────────────────────
    Route::get('/api/stock-unit/find', [StockUnitController::class, 'findByQr'])->name('api.stockUnit.findByQr');
    Route::get('/api/products/search', [ProductController::class, 'search'])->name('api.products.search');
});

require __DIR__.'/auth.php';
