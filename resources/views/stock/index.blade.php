<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Manajemen Stok') }}</h2>
        </div>
    </x-slot>

    <div class="py-6 md:py-12 pb-24 md:pb-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 mx-4 sm:mx-0 p-4 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 rounded-lg text-sm">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="mb-4 mx-4 sm:mx-0 p-4 bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 rounded-lg text-sm">@foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach</div>
            @endif

            {{-- Status Summary Bar --}}
            @php
                $total = array_sum($statusCounts->toArray());
                $tersedia = $statusCounts['tersedia'] ?? 0;
                $terjual = $statusCounts['terjual'] ?? 0;
                $rusak = $statusCounts['rusak'] ?? 0;
                $hilang = $statusCounts['hilang'] ?? 0;
            @endphp
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-6 mx-4 sm:mx-0">
                <a href="{{ route('stock.index') }}" class="block bg-white dark:bg-gray-800 rounded-lg shadow px-4 py-3 hover:shadow-md transition-shadow {{ !request('status') ? 'ring-2 ring-brand-500' : '' }}">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Unit</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($total) }}</p>
                </a>
                <a href="{{ route('stock.index', ['status' => 'tersedia']) }}" class="block bg-white dark:bg-gray-800 rounded-lg shadow px-4 py-3 hover:shadow-md transition-shadow {{ request('status') === 'tersedia' ? 'ring-2 ring-green-500' : '' }}">
                    <p class="text-xs font-medium text-green-600 dark:text-green-400 uppercase tracking-wide">Tersedia</p>
                    <p class="text-2xl font-bold text-green-700 dark:text-green-300 mt-1">{{ number_format($tersedia) }}</p>
                </a>
                <a href="{{ route('stock.index', ['status' => 'terjual']) }}" class="block bg-white dark:bg-gray-800 rounded-lg shadow px-4 py-3 hover:shadow-md transition-shadow {{ request('status') === 'terjual' ? 'ring-2 ring-gray-400' : '' }}">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Terjual</p>
                    <p class="text-2xl font-bold text-gray-700 dark:text-gray-300 mt-1">{{ number_format($terjual) }}</p>
                </a>
                <a href="{{ route('stock.index', ['status' => 'rusak']) }}" class="block bg-white dark:bg-gray-800 rounded-lg shadow px-4 py-3 hover:shadow-md transition-shadow {{ request('status') === 'rusak' ? 'ring-2 ring-red-500' : '' }}">
                    <p class="text-xs font-medium text-red-600 dark:text-red-400 uppercase tracking-wide">Rusak</p>
                    <p class="text-2xl font-bold text-red-700 dark:text-red-300 mt-1">{{ number_format($rusak) }}</p>
                </a>
                <a href="{{ route('stock.index', ['status' => 'hilang']) }}" class="block bg-white dark:bg-gray-800 rounded-lg shadow px-4 py-3 hover:shadow-md transition-shadow col-span-2 sm:col-span-1 {{ request('status') === 'hilang' ? 'ring-2 ring-yellow-500' : '' }}">
                    <p class="text-xs font-medium text-yellow-600 dark:text-yellow-400 uppercase tracking-wide">Hilang</p>
                    <p class="text-2xl font-bold text-yellow-700 dark:text-yellow-300 mt-1">{{ number_format($hilang) }}</p>
                </a>
            </div>

            {{-- Inbound Form --}}
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg mb-6">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Input Barang Masuk (Inbound)</h3>
                </div>
                <div class="p-4">
                    <form method="POST" action="{{ route('stock.store') }}">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pilih Produk</label>
                                <select name="product_id" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm">
                                    <option value="">-- Pilih Produk --</option>
                                    @foreach($products as $p)
                                        <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>{{ $p->product_name }} ({{ $p->unit }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jumlah Unit Masuk</label>
                                <input type="number" name="quantity" min="1" max="100" value="1" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Harga Beli / HPP (per unit)</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"><span class="text-gray-500 sm:text-sm">Rp</span></div>
                                    <input type="number" name="purchase_price" min="0" required class="block w-full rounded-md border-gray-300 pl-8 focus:border-brand-500 focus:ring-brand-500 sm:text-sm dark:bg-gray-900 dark:border-gray-700 dark:text-white" placeholder="0">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Masuk</label>
                                <input type="date" name="received_date" value="{{ date('Y-m-d') }}" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Catatan / Sumber Barang</label>
                                <input type="text" name="notes" placeholder="Contoh: Dari Supplier A" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm">
                            </div>
                        </div>
                        <div class="mt-4 flex justify-end">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white">Simpan & Generate QR</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Stock Units List --}}
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Daftar Unit Fisik & QR</h3>
                    <form method="GET" action="{{ route('stock.index') }}" class="flex flex-col sm:flex-row w-full sm:w-auto gap-2 mt-2 sm:mt-0">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari QR / produk..." class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 sm:text-sm w-full sm:w-48">
                        <select name="status" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 sm:text-sm w-full sm:w-auto">
                            <option value="">Semua Status</option>
                            <option value="tersedia" {{ request('status')=='tersedia'?'selected':'' }}>Tersedia</option>
                            <option value="terjual" {{ request('status')=='terjual'?'selected':'' }}>Terjual</option>
                            <option value="rusak" {{ request('status')=='rusak'?'selected':'' }}>Rusak</option>
                            <option value="hilang" {{ request('status')=='hilang'?'selected':'' }}>Hilang</option>
                        </select>
                        <button type="submit" class="px-3 py-2 bg-gray-800 dark:bg-gray-200 text-white dark:text-gray-800 rounded-md text-sm w-full sm:w-auto">Filter</button>
                    </form>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 border-b bg-gray-50 dark:bg-gray-900 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">QR Code</th>
                                <th class="px-4 py-3 border-b bg-gray-50 dark:bg-gray-900 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Produk</th>
                                <th class="px-4 py-3 border-b bg-gray-50 dark:bg-gray-900 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Harga Beli</th>
                                <th class="px-4 py-3 border-b bg-gray-50 dark:bg-gray-900 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Status</th>
                                <th class="px-4 py-3 border-b bg-gray-50 dark:bg-gray-900 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($stockUnits as $unit)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 {{ $unit->status !== 'tersedia' ? 'opacity-60' : '' }}">
                                <td class="px-4 py-4 text-sm font-mono text-gray-900 dark:text-white">{{ $unit->qr_code }}</td>
                                <td class="px-4 py-4 text-sm text-gray-900 dark:text-white">{{ $unit->product->product_name }}</td>
                                <td class="px-4 py-4 text-sm text-gray-500">Rp {{ number_format($unit->purchase_price, 0, ',', '.') }}</td>
                                <td class="px-4 py-4 text-sm">
                                    @php $colors = ['tersedia'=>'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300','terjual'=>'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300','rusak'=>'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300','hilang'=>'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300']; @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $colors[$unit->status] ?? '' }}">{{ ucfirst($unit->status) }}</span>
                                    {{-- Adjustment info for damaged/missing items --}}
                                    @if(in_array($unit->status, ['rusak', 'hilang', 'expired']) && $unit->stockAdjustment)
                                        <div class="mt-1 text-xs text-gray-400 dark:text-gray-500">
                                            <span title="{{ $unit->stockAdjustment->notes }}">
                                                {{ $unit->stockAdjustment->date->format('d/m/Y') }}
                                                @if($unit->stockAdjustment->user) · {{ $unit->stockAdjustment->user->name }} @endif
                                                @if($unit->stockAdjustment->notes)
                                                    · <span class="italic truncate inline-block max-w-[120px] align-bottom" title="{{ $unit->stockAdjustment->notes }}">{{ Str::limit($unit->stockAdjustment->notes, 30) }}</span>
                                                @endif
                                            </span>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-sm text-right">
                                    @if($unit->status === 'tersedia')
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('stock.printQr', $unit) }}" target="_blank" class="text-brand-600 hover:text-brand-900 dark:text-brand-400 dark:hover:text-brand-300 text-xs font-medium">Print QR</a>
                                            <span class="text-gray-300 dark:text-gray-600">|</span>
                                            {{-- Action dropdown for status change --}}
                                            <div class="relative inline-block text-left" x-data="{ open: false }">
                                                <button @click="open = !open" @click.away="open = false" type="button" class="inline-flex items-center text-xs font-medium text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 focus:outline-none" id="adjust-menu-{{ $unit->id }}">
                                                    <svg class="w-4 h-4 mr-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126Z" /></svg>
                                                    Ubah Status
                                                </button>
                                                <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 z-50 mt-2 w-48 origin-top-right rounded-md bg-white dark:bg-gray-700 shadow-lg ring-1 ring-black/5 dark:ring-white/10 focus:outline-none" style="display: none;">
                                                    <div class="py-1">
                                                        <button type="button" @click="open = false; openAdjustModal({{ $unit->id }}, '{{ $unit->qr_code }}', '{{ addslashes($unit->product->product_name) }}', {{ $unit->purchase_price }}, 'rusak')" class="w-full text-left px-4 py-2 text-sm text-red-700 dark:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/30 flex items-center gap-2">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                                                            Tandai Rusak
                                                        </button>
                                                        <button type="button" @click="open = false; openAdjustModal({{ $unit->id }}, '{{ $unit->qr_code }}', '{{ addslashes($unit->product->product_name) }}', {{ $unit->purchase_price }}, 'hilang')" class="w-full text-left px-4 py-2 text-sm text-yellow-700 dark:text-yellow-300 hover:bg-yellow-50 dark:hover:bg-yellow-900/30 flex items-center gap-2">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.182 16.318A4.486 4.486 0 0 0 12.016 15a4.486 4.486 0 0 0-3.198 1.318M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Z" /></svg>
                                                            Tandai Hilang
                                                        </button>
                                                        {{-- <button type="button" @click="open = false; openAdjustModal({{ $unit->id }}, '{{ $unit->qr_code }}', '{{ addslashes($unit->product->product_name) }}', {{ $unit->purchase_price }}, 'expired')" class="w-full text-left px-4 py-2 text-sm text-orange-700 dark:text-orange-300 hover:bg-orange-50 dark:hover:bg-orange-900/30 flex items-center gap-2">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                                                            Tandai Expired
                                                        </button> --}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @elseif(in_array($unit->status, ['rusak', 'hilang']))
                                        <span class="text-xs text-gray-400 dark:text-gray-500 italic">Disesuaikan</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">Belum ada unit stok. Tambahkan barang masuk di atas.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($stockUnits->hasPages())
                <div class="p-4 border-t border-gray-200 dark:border-gray-700">{{ $stockUnits->withQueryString()->links() }}</div>
                @endif
            </div>
        </div>
    </div>

    {{-- Adjustment Confirmation Modal --}}
    <div id="adjustModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="closeAdjustModal()"></div>
        {{-- Modal Panel --}}
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-xl shadow-2xl transform transition-all" id="adjustModalPanel">
                {{-- Header --}}
                <div class="px-6 pt-5 pb-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div id="modalIconRusak" class="hidden flex-shrink-0 w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/40 flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                        </div>
                        <div id="modalIconHilang" class="hidden flex-shrink-0 w-10 h-10 rounded-full bg-yellow-100 dark:bg-yellow-900/40 flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.182 16.318A4.486 4.486 0 0 0 12.016 15a4.486 4.486 0 0 0-3.198 1.318M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Z" /></svg>
                        </div>
                        <div id="modalIconExpired" class="hidden flex-shrink-0 w-10 h-10 rounded-full bg-orange-100 dark:bg-orange-900/40 flex items-center justify-center">
                            <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                        </div>
                        <div>
                            <h3 id="modalTitle" class="text-lg font-semibold text-gray-900 dark:text-white"></h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Tindakan ini tidak dapat dibatalkan.</p>
                        </div>
                    </div>
                </div>

                {{-- Body --}}
                <form id="adjustForm" method="POST">
                    @csrf
                    <input type="hidden" name="type" id="modalType">
                    <div class="px-6 py-4 space-y-4">
                        {{-- Unit Info --}}
                        <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-3 space-y-1">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500 dark:text-gray-400">QR Code</span>
                                <span id="modalQr" class="font-mono font-medium text-gray-900 dark:text-white"></span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500 dark:text-gray-400">Produk</span>
                                <span id="modalProduct" class="font-medium text-gray-900 dark:text-white"></span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500 dark:text-gray-400">Nilai HPP (Kerugian)</span>
                                <span id="modalHpp" class="font-medium text-red-600 dark:text-red-400"></span>
                            </div>
                        </div>

                        {{-- Notes --}}
                        <div>
                            <label for="modalNotes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Keterangan / Alasan <span class="text-gray-400 font-normal">(opsional)</span></label>
                            <textarea name="notes" id="modalNotes" rows="3" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm" placeholder="Contoh: Barang jatuh saat pengiriman..."></textarea>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 rounded-b-xl flex items-center justify-end gap-3">
                        <button type="button" onclick="closeAdjustModal()" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600 transition">Batal</button>
                        <button type="submit" id="modalSubmitBtn" class="px-4 py-2 text-sm font-medium text-white rounded-md transition focus:outline-none focus:ring-2 focus:ring-offset-2">Konfirmasi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    function openAdjustModal(unitId, qrCode, productName, purchasePrice, type) {
        const modal = document.getElementById('adjustModal');
        const form = document.getElementById('adjustForm');
        const title = document.getElementById('modalTitle');
        const submitBtn = document.getElementById('modalSubmitBtn');

        // Set form action
        form.action = '/stock/' + unitId + '/adjust';

        // Set hidden type
        document.getElementById('modalType').value = type;

        // Set unit info
        document.getElementById('modalQr').textContent = qrCode;
        document.getElementById('modalProduct').textContent = productName;
        document.getElementById('modalHpp').textContent = 'Rp ' + Number(purchasePrice).toLocaleString('id-ID');

        // Clear notes
        document.getElementById('modalNotes').value = '';

        // Hide all icons, show the relevant one
        document.getElementById('modalIconRusak').classList.add('hidden');
        document.getElementById('modalIconHilang').classList.add('hidden');
        document.getElementById('modalIconExpired').classList.add('hidden');

        // Style based on type
        if (type === 'rusak') {
            title.textContent = 'Tandai Unit sebagai Rusak';
            submitBtn.className = 'px-4 py-2 text-sm font-medium text-white rounded-md transition focus:outline-none focus:ring-2 focus:ring-offset-2 bg-red-600 hover:bg-red-700 focus:ring-red-500';
            document.getElementById('modalIconRusak').classList.remove('hidden');
        } else if (type === 'hilang') {
            title.textContent = 'Tandai Unit sebagai Hilang';
            submitBtn.className = 'px-4 py-2 text-sm font-medium text-white rounded-md transition focus:outline-none focus:ring-2 focus:ring-offset-2 bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500';
            document.getElementById('modalIconHilang').classList.remove('hidden');
        } else if (type === 'expired') {
            title.textContent = 'Tandai Unit sebagai Expired';
            submitBtn.className = 'px-4 py-2 text-sm font-medium text-white rounded-md transition focus:outline-none focus:ring-2 focus:ring-offset-2 bg-orange-600 hover:bg-orange-700 focus:ring-orange-500';
            document.getElementById('modalIconExpired').classList.remove('hidden');
        }

        // Show modal with animation
        modal.classList.remove('hidden');
        requestAnimationFrame(() => {
            document.getElementById('adjustModalPanel').classList.add('scale-100', 'opacity-100');
        });
    }

    function closeAdjustModal() {
        const modal = document.getElementById('adjustModal');
        modal.classList.add('hidden');
    }

    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeAdjustModal();
    });
    </script>
    @endpush
</x-app-layout>
