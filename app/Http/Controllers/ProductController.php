<?php

namespace App\Http\Controllers;

use App\Mail\LowStockAlert;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index(Request $request)
    {
        $query = Product::with('category')
            ->withCount(['stockUnits as available_count' => function ($q) {
                $q->where('status', 'tersedia');
            }]);

        // Search filter
        if ($request->filled('search')) {
            $query->where('product_name', 'like', '%' . $request->search . '%');
        }

        // Category filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->orderBy('product_name')->paginate(20);
        $categories = Category::all();

        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Store a newly created product.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'product_name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'min_stock' => 'required|integer|min:0',
            'suggested_price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($validated);

        // Check if the new product is immediately below min_stock (0 stock units exist)
        if ($product->min_stock > 0) {
            $product->loadCount(['stockUnits as available_count' => function ($query) {
                $query->where('status', 'tersedia');
            }]);
            $product->load('category');

            if ($product->available_count <= $product->min_stock) {
                $admins = User::where('role', User::ROLE_ADMIN)->get();
                foreach ($admins as $admin) {
                    try {
                        Mail::to($admin->email)->send(new LowStockAlert(collect([$product])));
                    } catch (\Exception $e) {
                        Log::error('Gagal mengirim email notifikasi stok menipis ke ' . $admin->email . ': ' . $e->getMessage());
                    }
                }
            }
        }

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    /**
     * Update the specified product.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'product_name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'min_stock' => 'required|integer|min:0',
            'suggested_price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        // Check if updated min_stock now puts the product below threshold
        if ($product->min_stock > 0) {
            $product->loadCount(['stockUnits as available_count' => function ($query) {
                $query->where('status', 'tersedia');
            }]);
            $product->load('category');

            if ($product->available_count <= $product->min_stock) {
                $admins = User::where('role', User::ROLE_ADMIN)->get();
                foreach ($admins as $admin) {
                    try {
                        Mail::to($admin->email)->send(new LowStockAlert(collect([$product])));
                    } catch (\Exception $e) {
                        Log::error('Gagal mengirim email notifikasi stok menipis ke ' . $admin->email . ': ' . $e->getMessage());
                    }
                }
            }
        }

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    /**
     * Remove the specified product (admin only).
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
    }

    /**
     * API: Search products for autocomplete.
     */
    public function search(Request $request)
    {
        $term = $request->get('q', '');
        $exclude = $request->get('exclude', '');
        $excludeIds = array_filter(explode(',', $exclude));

        $query = \App\Models\StockUnit::with('product.category')
            ->where('status', 'tersedia')
            ->where(function($q) use ($term) {
                $q->where('qr_code', 'like', "%{$term}%")
                  ->orWhereHas('product', function($pq) use ($term) {
                      $pq->where('product_name', 'like', "%{$term}%");
                  });
            });

        if (!empty($excludeIds)) {
            $query->whereNotIn('id', $excludeIds);
        }

        $units = $query->limit(10)->get()->map(function ($unit) {
            return [
                'stock_unit_id' => $unit->id,
                'qr_code' => $unit->qr_code,
                'product_id' => $unit->product_id,
                'product_name' => $unit->product->product_name,
                'category' => $unit->product->category->name,
                'suggested_price' => $unit->product->suggested_price,
            ];
        });

        return response()->json($units);
    }
}
