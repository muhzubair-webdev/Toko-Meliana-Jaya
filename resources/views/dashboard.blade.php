<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12 pb-24 md:pb-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Flash Messages --}}
            @if(session('error'))
                <div class="mb-4 mx-4 sm:mx-0 p-4 bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 rounded-lg text-sm">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8 px-4 sm:px-0">
                <!-- Laba / Rugi Card -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-brand-500">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Laba Bulan {{ $now->translatedFormat('F Y') }}</div>
                    <div class="text-3xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($monthlyProfit, 0, ',', '.') }}</div>
                    <div class="text-sm text-gray-500 mt-2">
                        Pendapatan: Rp {{ number_format($monthlyRevenue, 0, ',', '.') }} &mdash; HPP: Rp {{ number_format($monthlyCost, 0, ',', '.') }}
                    </div>
                    @if($monthlyLoss > 0)
                        <div class="text-sm text-red-500 mt-1">Kerugian (Hilang/Rusak): Rp {{ number_format($monthlyLoss, 0, ',', '.') }}</div>
                    @endif
                </div>

                <!-- Total Penjualan Hari Ini -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Total Penjualan Hari Ini</div>
                    <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $todayTransactionCount }} Transaksi</div>
                    <div class="text-sm text-gray-500 mt-2">Nilai: Rp {{ number_format($todayRevenue, 0, ',', '.') }}</div>
                </div>
            </div>

            <!-- Low Stock Alerts -->
            <div class="px-4 sm:px-0">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Peringatan Stok Menipis</h3>
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                    @if($lowStockProducts->count() > 0)
                        <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($lowStockProducts as $product)
                                <li class="p-4 flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 rounded-full {{ $product->available_count == 0 ? 'bg-red-100 text-red-600' : 'bg-yellow-100 text-yellow-600' }} flex items-center justify-center">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $product->product_name }} ({{ $product->unit }})</div>
                                            <div class="text-sm text-gray-500">Sisa: {{ $product->available_count }} {{ $product->unit }} (Min: {{ $product->min_stock }} {{ $product->unit }})</div>
                                        </div>
                                    </div>
                                    @if(auth()->user()->isAdmin())
                                    <a href="{{ route('stock.index', ['product_id' => $product->id]) }}" class="text-brand-600 hover:text-brand-900 text-sm font-medium">Restock</a>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="p-6 text-center text-gray-500 dark:text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Semua stok aman. Tidak ada peringatan.
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- All Products List -->
            <div class="px-4 sm:px-0 mt-8">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Semua Produk</h3>
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 border-b bg-gray-50 dark:bg-gray-900 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase w-16">Foto</th>
                                    <th class="px-4 py-3 border-b bg-gray-50 dark:bg-gray-900 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Nama Produk</th>
                                    <th class="px-4 py-3 border-b bg-gray-50 dark:bg-gray-900 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Kategori</th>
                                    <th class="px-4 py-3 border-b bg-gray-50 dark:bg-gray-900 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Harga Saran</th>
                                    <th class="px-4 py-3 border-b bg-gray-50 dark:bg-gray-900 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Stok</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($products as $product)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-4 py-3">
                                        <img src="{{ $product->image_url }}" alt="{{ $product->product_name }}" class="w-10 h-10 rounded-lg object-cover border border-gray-200 dark:border-gray-700">
                                    </td>
                                    <td class="px-4 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ $product->product_name }} ({{ $product->unit }})</td>
                                    <td class="px-4 py-4 text-sm text-gray-500">{{ $product->category->name }}</td>
                                    <td class="px-4 py-4 text-sm font-semibold text-gray-900 dark:text-white">Rp {{ number_format($product->suggested_price, 0, ',', '.') }}</td>
                                    <td class="px-4 py-4 text-sm">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $product->available_count <= $product->min_stock && $product->min_stock > 0 ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">{{ $product->available_count }}</span>
                                    </td>
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
    </div>
</x-app-layout>
