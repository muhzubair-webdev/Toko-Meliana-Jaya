<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Penyesuaian Stok (Barang Hilang/Rusak)') }}</h2>
    </x-slot>

    <div class="py-6 md:py-12 pb-24 md:pb-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 mx-4 sm:mx-0 p-4 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 rounded-lg text-sm">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="mb-4 mx-4 sm:mx-0 p-4 bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 rounded-lg text-sm">@foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach</div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center bg-red-50 dark:bg-red-900/20 rounded-t-lg">
                    <h3 class="text-lg font-medium text-red-800 dark:text-red-400">Catat Kerugian / Penyesuaian</h3>
                    <button type="button" id="start-scanner" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h4a1 1 0 010 2H5v3a1 1 0 01-2 0V4zm14-1a1 1 0 011 1v3a1 1 0 01-2 0V5h-3a1 1 0 010-2h4zM3 20a1 1 0 001 1h4a1 1 0 000-2H5v-3a1 1 0 00-2 0v4zm14 1a1 1 0 001-1v-4a1 1 0 00-2 0v3h-3a1 1 0 000 2h4z"></path></svg>
                        Scan QR
                    </button>
                </div>

                <div class="p-4 bg-gray-50 dark:bg-gray-900">
                    <div id="reader" class="w-full max-w-sm mx-auto overflow-hidden rounded-lg shadow-inner bg-black hidden mb-4"></div>
                    <div id="scan-status" class="hidden text-center text-sm text-gray-500 mb-2"></div>
                </div>

                <form method="POST" action="{{ route('adjustments.store') }}" id="adjustmentForm">
                    @csrf
                    <input type="hidden" name="stock_unit_id" id="adj_stock_unit_id">
                    <div class="p-4 sm:p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Unit Terpilih (QR Code / Nama)</label>
                            <input type="text" id="adj_unit_display" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 dark:border-gray-700 dark:bg-gray-700 dark:text-gray-300 shadow-sm sm:text-sm" readonly placeholder="Scan QR atau cari manual di atas">
                            <p id="adj_hpp_display" class="mt-1 text-xs text-gray-500"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jenis Penyesuaian</label>
                            <select name="type" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm sm:text-sm">
                                <option value="rusak">Barang Rusak</option>
                                <option value="hilang">Barang Hilang / Selisih Stok</option>
                                <option value="expired">Barang Expired</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Keterangan / Catatan</label>
                            <textarea name="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm sm:text-sm" placeholder="Contoh: Tikus gigit kabel"></textarea>
                        </div>
                        <div class="mt-6">
                            <button type="submit" id="btn-submit-adj" disabled class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed">Simpan Penyesuaian</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const startBtn = document.getElementById('start-scanner');
        const readerDiv = document.getElementById('reader');
        const scanStatus = document.getElementById('scan-status');
        const unitIdInput = document.getElementById('adj_stock_unit_id');
        const unitDisplay = document.getElementById('adj_unit_display');
        const hppDisplay = document.getElementById('adj_hpp_display');
        const btnSubmit = document.getElementById('btn-submit-adj');
        let html5QrCode;

        startBtn.addEventListener('click', function() {
            if (readerDiv.classList.contains('hidden')) {
                readerDiv.classList.remove('hidden');
                startBtn.textContent = 'Batal Scan';
                startBtn.classList.replace('bg-red-600', 'bg-gray-600');
                html5QrCode = new Html5Qrcode("reader");
                html5QrCode.start({ facingMode: "environment" }, { fps: 10, qrbox: { width: 250, height: 250 } },
                    (decodedText) => {
                        lookupUnit(decodedText);
                        html5QrCode.stop();
                        readerDiv.classList.add('hidden');
                        resetBtn();
                    }, () => {}
                );
            } else {
                if (html5QrCode) html5QrCode.stop().then(() => { readerDiv.classList.add('hidden'); resetBtn(); });
            }
        });

        function resetBtn() {
            startBtn.innerHTML = '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h4a1 1 0 010 2H5v3a1 1 0 01-2 0V4zm14-1a1 1 0 011 1v3a1 1 0 01-2 0V5h-3a1 1 0 010-2h4zM3 20a1 1 0 001 1h4a1 1 0 000-2H5v-3a1 1 0 00-2 0v4zm14 1a1 1 0 001-1v-4a1 1 0 00-2 0v3h-3a1 1 0 000 2h4z"></path></svg> Scan QR';
            startBtn.classList.replace('bg-gray-600', 'bg-red-600');
        }

        function lookupUnit(code) {
            scanStatus.classList.remove('hidden');
            scanStatus.textContent = 'Mencari ' + code + '...';
            fetch('/api/stock-unit/find?code=' + encodeURIComponent(code), { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => { if (!r.ok) throw new Error('Not found'); return r.json(); })
                .then(unit => {
                    scanStatus.classList.add('hidden');
                    if (unit.status !== 'tersedia') { alert('Unit ' + unit.qr_code + ' tidak tersedia (status: ' + unit.status + ')'); return; }
                    unitIdInput.value = unit.id;
                    unitDisplay.value = unit.qr_code + ' (' + unit.product_name + ')';
                    hppDisplay.textContent = 'Nilai HPP: Rp ' + Number(unit.purchase_price).toLocaleString('id-ID');
                    btnSubmit.disabled = false;
                })
                .catch(() => { scanStatus.textContent = 'Unit tidak ditemukan: ' + code; setTimeout(() => scanStatus.classList.add('hidden'), 3000); });
        }
    });
    </script>
    @endpush
</x-app-layout>
