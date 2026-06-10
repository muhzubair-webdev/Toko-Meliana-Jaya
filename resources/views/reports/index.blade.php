<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Laporan') }}</h2>
    </x-slot>

    <div class="py-6 md:py-12 pb-24 md:pb-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('info'))
                <div class="mb-4 mx-4 sm:mx-0 p-4 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 rounded-lg text-sm">{{ session('info') }}</div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8 px-4 sm:px-0">
                {{-- Filter --}}
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Pilih Laporan</h3>
                    <form method="GET" action="{{ route('reports.index') }}" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jenis Laporan</label>
                            <select name="type" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 sm:text-sm">
                                <option value="penjualan" {{ $reportType=='penjualan'?'selected':'' }}>Laporan Penjualan (Laba/Rugi)</option>
                                <option value="stok" {{ $reportType=='stok'?'selected':'' }}>Laporan Nilai Stok Tersisa</option>
                                <option value="adjustment" {{ $reportType=='adjustment'?'selected':'' }}>Laporan Penyesuaian (Hilang/Rusak)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Periode Bulan</label>
                            <input type="month" name="month" value="{{ $month }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 sm:text-sm">
                        </div>
                        <div class="flex space-x-2 pt-2">
                            <button type="submit" class="inline-flex justify-center flex-1 py-2 px-4 rounded-md shadow-sm text-sm font-medium text-white bg-brand-600 hover:bg-brand-700">Tampilkan</button>
                            <a href="{{ route('reports.exportPdf', ['type' => $reportType, 'month' => $month]) }}" class="inline-flex justify-center items-center flex-1 py-2 px-4 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                Export PDF
                            </a>
                        </div>
                    </form>
                </div>

                {{-- Summary --}}
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6 flex flex-col justify-center">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Ringkasan {{ $monthLabel }}</h3>
                    @if($reportType === 'penjualan')
                        <div class="mt-2 text-4xl font-extrabold text-brand-600">Rp {{ number_format($totalProfit, 0, ',', '.') }}</div>
                        <p class="mt-1 text-sm text-brand-600">Total Laba Kotor</p>
                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700 space-y-2">
                            <div class="flex justify-between text-sm"><span class="text-gray-500">Total Penjualan:</span><span class="font-medium text-gray-900 dark:text-white">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span></div>
                            <div class="flex justify-between text-sm"><span class="text-gray-500">Total HPP:</span><span class="font-medium text-gray-900 dark:text-white">Rp {{ number_format($totalCost, 0, ',', '.') }}</span></div>
                            <div class="flex justify-between text-sm"><span class="text-red-500">Kerugian (Hilang/Rusak):</span><span class="font-medium text-red-500">Rp {{ number_format($monthlyLoss, 0, ',', '.') }}</span></div>
                        </div>
                    @elseif($reportType === 'stok')
                        <div class="mt-2 text-4xl font-extrabold text-blue-600">Rp {{ number_format($totalStockValue ?? 0, 0, ',', '.') }}</div>
                        <p class="mt-1 text-sm text-blue-600">Total Nilai Stok Tersedia</p>
                        <p class="mt-2 text-sm text-gray-500">{{ isset($stockUnits) ? $stockUnits->count() : 0 }} unit barang tersedia</p>
                    @else
                        <div class="mt-2 text-4xl font-extrabold text-red-600">Rp {{ number_format($totalLoss ?? $monthlyLoss, 0, ',', '.') }}</div>
                        <p class="mt-1 text-sm text-red-600">Total Kerugian</p>
                        <p class="mt-2 text-sm text-gray-500">{{ isset($adjustments) ? $adjustments->count() : 0 }} penyesuaian</p>
                    @endif
                </div>
            </div>

            {{-- Data Table --}}
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg px-4 sm:px-0">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        @if($reportType === 'penjualan') Preview Laporan Penjualan ({{ $monthLabel }})
                        @elseif($reportType === 'stok') Daftar Stok Tersedia
                        @else Riwayat Penyesuaian ({{ $monthLabel }})
                        @endif
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    @if($reportType === 'penjualan')
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 border-b bg-gray-50 dark:bg-gray-900 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Tanggal</th>
                                <th class="px-4 py-3 border-b bg-gray-50 dark:bg-gray-900 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">No. Invoice</th>
                                <th class="px-4 py-3 border-b bg-gray-50 dark:bg-gray-900 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Kasir</th>
                                <th class="px-4 py-3 border-b bg-gray-50 dark:bg-gray-900 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase text-right">Total</th>
                                <th class="px-4 py-3 border-b bg-gray-50 dark:bg-gray-900 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase text-right">HPP</th>
                                <th class="px-4 py-3 border-b bg-gray-50 dark:bg-gray-900 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase text-right">Laba</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($sales as $sale)
                            @php
                                $rev = $sale->saleDetails->sum('final_price');
                                $cost = $sale->saleDetails->sum(fn($d) => $d->stockUnit->purchase_price ?? 0);
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-4 py-4 text-sm text-gray-900 dark:text-white">{{ $sale->sale_date->format('d M Y') }}</td>
                                <td class="px-4 py-4 text-sm font-mono text-gray-500">{{ $sale->invoice_number }}</td>
                                <td class="px-4 py-4 text-sm text-gray-500">{{ $sale->user->name ?? '-' }}</td>
                                <td class="px-4 py-4 text-sm text-right text-gray-900 dark:text-white">Rp {{ number_format($rev, 0, ',', '.') }}</td>
                                <td class="px-4 py-4 text-sm text-right text-gray-500">Rp {{ number_format($cost, 0, ',', '.') }}</td>
                                <td class="px-4 py-4 text-sm text-right font-medium {{ ($rev - $cost) >= 0 ? 'text-brand-600' : 'text-red-600' }}">Rp {{ number_format($rev - $cost, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">Tidak ada data penjualan untuk periode ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>

                    @elseif($reportType === 'stok')
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 border-b bg-gray-50 dark:bg-gray-900 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">QR Code</th>
                                <th class="px-4 py-3 border-b bg-gray-50 dark:bg-gray-900 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Produk</th>
                                <th class="px-4 py-3 border-b bg-gray-50 dark:bg-gray-900 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Kategori</th>
                                <th class="px-4 py-3 border-b bg-gray-50 dark:bg-gray-900 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase text-right">Nilai HPP</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($stockUnits ?? [] as $unit)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-4 py-4 text-sm font-mono text-gray-900 dark:text-white">{{ $unit->qr_code }}</td>
                                <td class="px-4 py-4 text-sm text-gray-900 dark:text-white">{{ $unit->product->product_name }}</td>
                                <td class="px-4 py-4 text-sm text-gray-500">{{ $unit->product->category->name }}</td>
                                <td class="px-4 py-4 text-sm text-right text-gray-900 dark:text-white">Rp {{ number_format($unit->purchase_price, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="px-4 py-8 text-center text-gray-500">Tidak ada stok tersedia.</td></tr>
                            @endforelse
                        </tbody>
                    </table>

                    @else {{-- adjustment --}}
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 border-b bg-gray-50 dark:bg-gray-900 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Tanggal</th>
                                <th class="px-4 py-3 border-b bg-gray-50 dark:bg-gray-900 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">QR / Produk</th>
                                <th class="px-4 py-3 border-b bg-gray-50 dark:bg-gray-900 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Jenis</th>
                                <th class="px-4 py-3 border-b bg-gray-50 dark:bg-gray-900 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Dilaporkan Oleh</th>
                                <th class="px-4 py-3 border-b bg-gray-50 dark:bg-gray-900 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase text-right">Kerugian</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($adjustments ?? [] as $adj)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-4 py-4 text-sm text-gray-900 dark:text-white">{{ $adj->date->format('d M Y') }}</td>
                                <td class="px-4 py-4 text-sm text-gray-900 dark:text-white">{{ $adj->stockUnit->qr_code }} — {{ $adj->stockUnit->product->product_name }}</td>
                                <td class="px-4 py-4 text-sm"><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">{{ ucfirst($adj->type) }}</span></td>
                                <td class="px-4 py-4 text-sm text-gray-500">{{ $adj->user->name ?? '-' }}</td>
                                <td class="px-4 py-4 text-sm text-right font-medium text-red-600">Rp {{ number_format($adj->stockUnit->purchase_price, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">Tidak ada penyesuaian untuk periode ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
