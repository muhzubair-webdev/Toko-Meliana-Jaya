<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Katalog Produk') }}</h2>
            @if(auth()->user()->isAdmin())
                <button onclick="document.getElementById('addProductModal').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 bg-brand-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-brand-500">Tambah Produk</button>
            @endif
        </div>
    </x-slot>

    <div class="py-6 md:py-12 pb-24 md:pb-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 mx-4 sm:mx-0 p-4 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 rounded-lg text-sm">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="mb-4 mx-4 sm:mx-0 p-4 bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 rounded-lg text-sm">
                    @foreach($errors->all() as $error) <p>{{ $error }}</p> @endforeach
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <form method="GET" action="{{ route('products.index') }}" class="flex flex-col sm:flex-row gap-2">
                        <input type="text" name="search" value="{{ request('search') }}" class="flex-1 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm" placeholder="Cari nama produk...">
                        <select name="category_id" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm sm:text-sm">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="px-4 py-2 bg-gray-800 dark:bg-gray-200 text-white dark:text-gray-800 rounded-md text-sm font-medium">Cari</button>
                    </form>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 border-b bg-gray-50 dark:bg-gray-900 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Nama Produk</th>
                                <th class="px-4 py-3 border-b bg-gray-50 dark:bg-gray-900 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Kategori</th>
                                <th class="px-4 py-3 border-b bg-gray-50 dark:bg-gray-900 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Harga Saran</th>
                                <th class="px-4 py-3 border-b bg-gray-50 dark:bg-gray-900 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Stok</th>
                                @if(auth()->user()->isAdmin())
                                <th class="px-4 py-3 border-b bg-gray-50 dark:bg-gray-900 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase text-right">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($products as $product)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-4 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ $product->product_name }} ({{ $product->unit }})</td>
                                <td class="px-4 py-4 text-sm text-gray-500">{{ $product->category->name }}</td>
                                <td class="px-4 py-4 text-sm font-semibold text-gray-900 dark:text-white">Rp {{ number_format($product->suggested_price, 0, ',', '.') }}</td>
                                <td class="px-4 py-4 text-sm">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $product->available_count <= $product->min_stock && $product->min_stock > 0 ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">{{ $product->available_count }}</span>
                                </td>
                                @if(auth()->user()->isAdmin())
                                <td class="px-4 py-4 text-sm text-right space-x-2">
                                    <button onclick="openEditModal({{ $product->id }},'{{ addslashes($product->product_name) }}',{{ $product->category_id }},'{{ $product->unit }}',{{ $product->min_stock }},{{ $product->suggested_price }})" class="text-brand-600 hover:text-brand-900">Edit</button>
                                    <form method="POST" action="{{ route('products.destroy', $product) }}" class="inline" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button type="submit" class="text-red-600 hover:text-red-900">Hapus</button></form>
                                </td>
                                @endif
                            </tr>
                            @empty
                            <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">Belum ada produk.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($products->hasPages())
                <div class="p-4 border-t border-gray-200 dark:border-gray-700">{{ $products->withQueryString()->links() }}</div>
                @endif
            </div>
        </div>
    </div>

    {{-- Add Modal --}}
    <div id="addProductModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75" onclick="document.getElementById('addProductModal').classList.add('hidden')"></div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md relative z-10 p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Tambah Produk</h3>
                <form method="POST" action="{{ route('products.store') }}">@csrf
                    <div class="space-y-4">
                        <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kategori</label><select name="category_id" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 sm:text-sm">@foreach($categories as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div>
                        <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Produk</label><input type="text" name="product_name" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 sm:text-sm"></div>
                        <div class="grid grid-cols-2 gap-4">
                            <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Satuan</label><input type="text" name="unit" required placeholder="Roll, Pcs" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 sm:text-sm"></div>
                            <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Min Stok</label><input type="number" name="min_stock" value="0" min="0" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 sm:text-sm"></div>
                        </div>
                        <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Harga Jual Saran (Rp)</label><input type="number" name="suggested_price" value="0" min="0" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 sm:text-sm"></div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-2">
                        <button type="button" onclick="document.getElementById('addProductModal').classList.add('hidden')" class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300 rounded-md">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-brand-600 text-white text-sm rounded-md hover:bg-brand-500">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Modal --}}
    <div id="editProductModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75" onclick="document.getElementById('editProductModal').classList.add('hidden')"></div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md relative z-10 p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Edit Produk</h3>
                <form method="POST" id="editProductForm">@csrf @method('PUT')
                    <div class="space-y-4">
                        <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kategori</label><select name="category_id" id="edit_category_id" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 sm:text-sm">@foreach($categories as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div>
                        <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Produk</label><input type="text" name="product_name" id="edit_product_name" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 sm:text-sm"></div>
                        <div class="grid grid-cols-2 gap-4">
                            <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Satuan</label><input type="text" name="unit" id="edit_unit" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 sm:text-sm"></div>
                            <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Min Stok</label><input type="number" name="min_stock" id="edit_min_stock" min="0" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 sm:text-sm"></div>
                        </div>
                        <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Harga Saran (Rp)</label><input type="number" name="suggested_price" id="edit_suggested_price" min="0" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 sm:text-sm"></div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-2">
                        <button type="button" onclick="document.getElementById('editProductModal').classList.add('hidden')" class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300 rounded-md">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-brand-600 text-white text-sm rounded-md hover:bg-brand-500">Perbarui</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    function openEditModal(id,name,catId,unit,minStock,price){
        document.getElementById('editProductForm').action='/products/'+id;
        document.getElementById('edit_product_name').value=name;
        document.getElementById('edit_category_id').value=catId;
        document.getElementById('edit_unit').value=unit;
        document.getElementById('edit_min_stock').value=minStock;
        document.getElementById('edit_suggested_price').value=price;
        document.getElementById('editProductModal').classList.remove('hidden');
    }
    </script>
    @endpush
</x-app-layout>
