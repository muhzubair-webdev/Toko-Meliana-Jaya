<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Transaksi Penjualan') }}</h2>
    </x-slot>

    <div class="py-6 md:py-12 pb-24 md:pb-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 mx-4 sm:mx-0 p-4 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 rounded-lg text-sm">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="mb-4 mx-4 sm:mx-0 p-4 bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 rounded-lg text-sm">@foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach</div>
            @endif

            {{-- Scanner Section --}}
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg mb-6">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Scan QR Code Barang</h3>
                    <button type="button" id="start-scanner" class="inline-flex items-center px-4 py-2 bg-brand-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-brand-500 transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h4a1 1 0 010 2H5v3a1 1 0 01-2 0V4zm14-1a1 1 0 011 1v3a1 1 0 01-2 0V5h-3a1 1 0 010-2h4zM3 20a1 1 0 001 1h4a1 1 0 000-2H5v-3a1 1 0 00-2 0v4zm14 1a1 1 0 001-1v-4a1 1 0 00-2 0v3h-3a1 1 0 000 2h4z"></path></svg>
                        Mulai Scan
                    </button>
                </div>
                <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-b-lg">
                    <div id="reader" class="w-full max-w-sm mx-auto overflow-hidden rounded-lg shadow-inner bg-black hidden"></div>
                    <div id="scan-status" class="hidden mt-2 text-center text-sm text-gray-500"></div>

                    <div class="mt-4 relative"><div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-300 dark:border-gray-700"></div></div><div class="relative flex justify-center"><span class="px-2 bg-gray-50 dark:bg-gray-900 text-sm text-gray-500">ATAU</span></div></div>

                    <div class="mt-4">
                        <label for="search_product" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Cari Barang Manual (Jika QR Rusak)</label>
                        <div class="relative">
                            <input type="text" id="search_product" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm" placeholder="Ketik nama barang..." autocomplete="off">
                            <div id="autocomplete-results" class="hidden absolute z-10 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg max-h-60 overflow-y-auto"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Cart Section --}}
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Daftar Belanjaan</h3>
                </div>
                <div class="p-0 sm:p-4">
                    <ul id="cart-items" class="divide-y divide-gray-200 dark:divide-gray-700"></ul>
                    <div id="cart-empty" class="p-6 text-center text-gray-500 dark:text-gray-400 text-sm">Belum ada barang. Scan QR atau cari manual untuk menambahkan.</div>

                    <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 rounded-b-lg">
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-base font-medium text-gray-900 dark:text-gray-100">Total Harga:</span>
                            <span id="total-price" class="text-2xl font-bold text-brand-600">Rp 0</span>
                        </div>
                        <button type="button" id="btn-save-sale" disabled class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-brand-600 hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 disabled:opacity-50 disabled:cursor-not-allowed">Simpan Transaksi</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const cart = [];
        const cartList = document.getElementById('cart-items');
        const cartEmpty = document.getElementById('cart-empty');
        const totalPriceEl = document.getElementById('total-price');
        const btnSave = document.getElementById('btn-save-sale');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // ── QR Scanner ──
        const startBtn = document.getElementById('start-scanner');
        const readerDiv = document.getElementById('reader');
        const scanStatus = document.getElementById('scan-status');
        let html5QrCode;

        startBtn.addEventListener('click', function() {
            if (readerDiv.classList.contains('hidden')) {
                readerDiv.classList.remove('hidden');
                startBtn.textContent = 'Batal Scan';
                startBtn.classList.replace('bg-brand-600', 'bg-red-600');

                html5QrCode = new Html5Qrcode("reader");
                html5QrCode.start({ facingMode: "environment" }, { fps: 10, qrbox: { width: 250, height: 250 } },
                    (decodedText) => {
                        lookupQr(decodedText);
                        html5QrCode.stop();
                        readerDiv.classList.add('hidden');
                        resetScanBtn();
                    }, () => {}
                );
            } else {
                if (html5QrCode) html5QrCode.stop().then(() => { readerDiv.classList.add('hidden'); resetScanBtn(); });
            }
        });

        function resetScanBtn() {
            startBtn.innerHTML = '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h4a1 1 0 010 2H5v3a1 1 0 01-2 0V4zm14-1a1 1 0 011 1v3a1 1 0 01-2 0V5h-3a1 1 0 010-2h4zM3 20a1 1 0 001 1h4a1 1 0 000-2H5v-3a1 1 0 00-2 0v4zm14 1a1 1 0 001-1v-4a1 1 0 00-2 0v3h-3a1 1 0 000 2h4z"></path></svg> Mulai Scan';
            startBtn.classList.replace('bg-red-600', 'bg-brand-600');
        }

        function lookupQr(code) {
            scanStatus.classList.remove('hidden');
            scanStatus.textContent = 'Mencari ' + code + '...';
            fetch('/api/stock-unit/find?code=' + encodeURIComponent(code), { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => { if (!r.ok) throw new Error('Tidak ditemukan'); return r.json(); })
                .then(unit => {
                    scanStatus.classList.add('hidden');
                    if (unit.status !== 'tersedia') { alert('Unit ' + unit.qr_code + ' tidak tersedia (status: ' + unit.status + ')'); return; }
                    if (cart.find(c => c.stock_unit_id === unit.id)) { alert('Unit ' + unit.qr_code + ' sudah ada di keranjang.'); return; }

                    const existingItem = cart.find(c => c.product_id == unit.product_id);
                    if (existingItem) {
                        unit.suggested_price = existingItem.final_price;
                    }
                    addToCart(unit);
                })
                .catch(() => { scanStatus.textContent = 'Unit tidak ditemukan: ' + code; setTimeout(() => scanStatus.classList.add('hidden'), 3000); });
        }

        // ── Autocomplete Search ──
        const searchInput = document.getElementById('search_product');
        const resultsDiv = document.getElementById('autocomplete-results');
        let debounceTimer;

        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            const q = this.value.trim();
            if (q.length < 2) { resultsDiv.classList.add('hidden'); return; }
            debounceTimer = setTimeout(() => {
                const excludeIds = cart.map(c => c.stock_unit_id).join(',');
                fetch('/api/products/search?q=' + encodeURIComponent(q) + '&exclude=' + excludeIds, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(r => r.json())
                    .then(units => {
                        if (!units.length) { resultsDiv.innerHTML = '<div class="p-3 text-sm text-gray-500">Tidak ditemukan</div>'; resultsDiv.classList.remove('hidden'); return; }
                        resultsDiv.innerHTML = units.map(p =>
                            '<button type="button" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-900 dark:text-white" data-qr="' + p.qr_code + '">' +
                            '<div class="flex justify-between items-center">' +
                            '<span>' + p.product_name + ' <span class="text-gray-400">(' + p.category + ')</span></span>' +
                            '<span class="font-mono text-xs text-gray-500">' + p.qr_code + '</span>' +
                            '</div>' +
                            '<div class="text-xs text-brand-600 mt-1">Rp ' + Number(p.suggested_price).toLocaleString('id-ID') + '</div>' +
                            '</button>'
                        ).join('');
                        resultsDiv.classList.remove('hidden');
                        resultsDiv.querySelectorAll('button').forEach(btn => {
                            btn.addEventListener('click', () => { lookupQr(btn.dataset.qr); resultsDiv.classList.add('hidden'); searchInput.value = ''; });
                        });
                    });
            }, 300);
        });

        document.addEventListener('click', (e) => { if (!resultsDiv.contains(e.target) && e.target !== searchInput) resultsDiv.classList.add('hidden'); });

        // Function selectProductForManual removed, logic handled in lookupQr

        // ── Cart Management ──
        function addToCart(unit) {
            cart.push({
                stock_unit_id: unit.id,
                qr_code: unit.qr_code,
                product_id: unit.product_id,
                product_name: unit.product_name,
                purchase_price: parseFloat(unit.purchase_price),
                final_price: parseFloat(unit.suggested_price)
            });
            renderCart();
        }

        function incrementQuantity(productId) {
            const excludeIds = cart.filter(c => c.product_id == productId).map(c => c.stock_unit_id).join(',');
            fetch('/api/stock-unit/find?product_id=' + productId + '&exclude=' + excludeIds, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json())
                .then(unit => {
                    if (unit.error) { alert(unit.error); return; }
                    const existingItem = cart.find(c => c.product_id == productId);
                    if (existingItem) {
                        unit.suggested_price = existingItem.final_price;
                    }
                    addToCart(unit);
                })
                .catch(() => alert('Stok tidak cukup untuk menambah jumlah produk ini.'));
        }

        function decrementQuantity(productId) {
            for (let i = cart.length - 1; i >= 0; i--) {
                if (cart[i].product_id == productId) {
                    cart.splice(i, 1);
                    break;
                }
            }
            renderCart();
        }

        function updatePrice(productId, newPrice) {
            cart.forEach(c => {
                if (c.product_id == productId) {
                    c.final_price = newPrice;
                }
            });
            updateTotal();
        }

        function renderCart() {
            if (cart.length === 0) { cartList.innerHTML = ''; cartEmpty.classList.remove('hidden'); btnSave.disabled = true; totalPriceEl.textContent = 'Rp 0'; return; }
            cartEmpty.classList.add('hidden');
            btnSave.disabled = false;

            const groups = {};
            cart.forEach(item => {
                if (!groups[item.product_id]) {
                    groups[item.product_id] = {
                        product_id: item.product_id,
                        product_name: item.product_name,
                        units: [],
                        final_price: item.final_price,
                        total_purchase_price: 0
                    };
                }
                groups[item.product_id].units.push(item);
                groups[item.product_id].total_purchase_price += item.purchase_price;
            });

            cartList.innerHTML = Object.values(groups).map((group, i) =>
                '<li class="p-4 flex flex-col sm:flex-row sm:items-center justify-between">' +
                    '<div class="mb-3 sm:mb-0 w-full sm:w-1/3">' +
                        '<h4 class="text-sm font-bold text-gray-900 dark:text-white">' + group.product_name + '</h4>' +
                        '<div class="font-mono text-xs text-gray-500 mb-1" style="word-break: break-all;">QR: ' + group.units.map(u => u.qr_code).join(', ') + '</div>' +
                        '<p class="text-xs text-gray-500">Jumlah Unit: ' + group.units.length + ' <span class="mx-1">•</span> HPP/unit: Rp ' + Number(Math.round(group.total_purchase_price / group.units.length)).toLocaleString('id-ID') + '</p>' +
                    '</div>' +
                    '<div class="flex items-center space-x-4 w-full sm:w-2/3 justify-between sm:justify-end">' +
                        '<div class="flex items-center border border-gray-300 dark:border-gray-600 rounded-md shadow-sm">' +
                            '<button type="button" onclick="window.decrementQty(' + group.product_id + ')" class="px-3 py-1 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-l-md text-gray-700 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600 font-bold">-</button>' +
                            '<div class="px-4 py-1 text-sm font-medium text-gray-900 dark:text-white bg-white dark:bg-gray-800">' + group.units.length + '</div>' +
                            '<button type="button" onclick="window.incrementQty(' + group.product_id + ')" class="px-3 py-1 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-r-md text-gray-700 dark:text-gray-300 border-l border-gray-300 dark:border-gray-600 font-bold">+</button>' +
                        '</div>' +
                        '<div class="relative rounded-md shadow-sm w-32">' +
                            '<div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"><span class="text-gray-500 text-sm">Rp</span></div>' +
                            '<input type="number" min="0" value="' + group.final_price + '" onchange="window.updatePrice(' + group.product_id + ', parseFloat(this.value) || 0)" class="block w-full rounded-md border-gray-300 pl-8 focus:border-brand-500 focus:ring-brand-500 sm:text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white">' +
                        '</div>' +
                    '</div>' +
                '</li>'
            ).join('');

            updateTotal();
        }

        function updateTotal() {
            const total = cart.reduce((sum, item) => sum + item.final_price, 0);
            totalPriceEl.textContent = 'Rp ' + total.toLocaleString('id-ID');
        }

        window.incrementQty = incrementQuantity;
        window.decrementQty = decrementQuantity;
        window.updatePrice = updatePrice;

        // ── Save Transaction ──
        btnSave.addEventListener('click', function() {
            if (!cart.length) return;
            if (!confirm('Simpan transaksi dengan ' + cart.length + ' item?')) return;

            const items = cart.map(c => ({ stock_unit_id: c.stock_unit_id, final_price: c.final_price }));

            fetch('{{ route("sales.store") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ items })
            }).then(r => {
                if (r.redirected) { window.location.href = r.url; return; }
                return r.json();
            }).then(data => {
                if (data && data.errors) { alert(Object.values(data.errors).flat().join('\n')); return; }
                window.location.href = '{{ route("sales.create") }}';
            }).catch(() => {
                // Fallback: submit as form
                const form = document.createElement('form');
                form.method = 'POST'; form.action = '{{ route("sales.store") }}';
                form.innerHTML = '<input type="hidden" name="_token" value="' + csrfToken + '">';
                items.forEach((item, i) => {
                    form.innerHTML += '<input type="hidden" name="items[' + i + '][stock_unit_id]" value="' + item.stock_unit_id + '">';
                    form.innerHTML += '<input type="hidden" name="items[' + i + '][final_price]" value="' + item.final_price + '">';
                });
                document.body.appendChild(form); form.submit();
            });
        });
    });
    </script>
    @endpush
</x-app-layout>
