<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

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
        ]);

        Product::create($validated);

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
        ]);

        $product->update($validated);

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

        $products = Product::with('category')
            ->where('product_name', 'like', "%{$term}%")
            ->limit(10)
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'product_name' => $p->product_name,
                    'category' => $p->category->name,
                    'unit' => $p->unit,
                    'suggested_price' => $p->suggested_price,
                ];
            });

        return response()->json($products);
    }
}
