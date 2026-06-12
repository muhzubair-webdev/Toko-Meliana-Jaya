<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $title }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #1a1a1a;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 20px;
            font-weight: bold;
            color: #1e3a5f;
            margin-bottom: 2px;
        }
        .header h2 {
            font-size: 14px;
            font-weight: 600;
            color: #2563eb;
            margin-bottom: 4px;
        }
        .header p {
            font-size: 10px;
            color: #6b7280;
        }
        .summary-box {
            background-color: #f0f7ff;
            border: 1px solid #bfdbfe;
            border-radius: 6px;
            padding: 12px 16px;
            margin-bottom: 20px;
        }
        .summary-box h3 {
            font-size: 11px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }
        .summary-table td {
            padding: 3px 0;
        }
        .summary-table td:last-child {
            text-align: right;
            font-weight: bold;
        }
        .summary-highlight {
            font-size: 18px;
            font-weight: bold;
            color: #2563eb;
        }
        .summary-highlight.red {
            color: #dc2626;
        }
        .summary-highlight.green {
            color: #16a34a;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.data-table thead th {
            background-color: #1e3a5f;
            color: #ffffff;
            padding: 8px 10px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }
        table.data-table thead th.text-right {
            text-align: right;
        }
        table.data-table thead th.text-center {
            text-align: center;
        }
        table.data-table tbody td {
            padding: 7px 10px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 10px;
        }
        table.data-table tbody td.text-right {
            text-align: right;
        }
        table.data-table tbody td.text-center {
            text-align: center;
        }
        table.data-table tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }
        table.data-table tfoot td {
            padding: 8px 10px;
            font-weight: bold;
            border-top: 2px solid #1e3a5f;
            font-size: 11px;
        }
        table.data-table tfoot td.text-right {
            text-align: right;
        }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: 600;
        }
        .badge-red {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .text-red {
            color: #dc2626;
        }
        .text-green {
            color: #16a34a;
        }
        .text-blue {
            color: #2563eb;
        }
        .text-muted {
            color: #6b7280;
        }
        .font-mono {
            font-family: 'DejaVu Sans Mono', monospace;
            font-size: 9px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            font-size: 9px;
            color: #9ca3af;
            text-align: center;
        }
        .empty-state {
            text-align: center;
            padding: 30px;
            color: #9ca3af;
            font-style: italic;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <h1>Toko Meliana Jaya</h1>
        <h2>{{ $title }}</h2>
        <p>Periode: {{ $monthLabel }} &mdash; Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }} WIB</p>
    </div>

    {{-- ===================== PENJUALAN ===================== --}}
    @if($reportType === 'penjualan')
        <div class="summary-box">
            <h3>Ringkasan Laba/Rugi</h3>
            <table class="summary-table">
                <tr>
                    <td>Total Penjualan</td>
                    <td>Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Total HPP (Harga Pokok Penjualan)</td>
                    <td>Rp {{ number_format($totalCost, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="text-red">Kerugian (Hilang/Rusak)</td>
                    <td class="text-red">Rp {{ number_format($monthlyLoss, 0, ',', '.') }}</td>
                </tr>
                <tr style="border-top: 2px solid #2563eb;">
                    <td style="padding-top: 6px;"><strong>Laba Kotor</strong></td>
                    <td style="padding-top: 6px;" class="{{ $totalProfit >= 0 ? 'text-green' : 'text-red' }}">
                        <span class="summary-highlight {{ $totalProfit >= 0 ? 'green' : 'red' }}">Rp {{ number_format($totalProfit, 0, ',', '.') }}</span>
                    </td>
                </tr>
            </table>
        </div>

        @if($sales->count() > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>No. Invoice</th>
                    <th>Kasir</th>
                    <th class="text-right">Total Penjualan</th>
                    <th class="text-right">HPP</th>
                    <th class="text-right">Laba</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sales as $index => $sale)
                @php
                    $rev = $sale->saleDetails->sum('final_price');
                    $cost = $sale->saleDetails->sum(fn($d) => $d->stockUnit->purchase_price ?? 0);
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $sale->sale_date->format('d M Y') }}</td>
                    <td class="font-mono">{{ $sale->invoice_number }}</td>
                    <td>{{ $sale->user->name ?? '-' }}</td>
                    <td class="text-right">Rp {{ number_format($rev, 0, ',', '.') }}</td>
                    <td class="text-right text-muted">Rp {{ number_format($cost, 0, ',', '.') }}</td>
                    <td class="text-right {{ ($rev - $cost) >= 0 ? 'text-green' : 'text-red' }}">Rp {{ number_format($rev - $cost, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4"><strong>Total</strong></td>
                    <td class="text-right">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                    <td class="text-right text-muted">Rp {{ number_format($totalCost, 0, ',', '.') }}</td>
                    <td class="text-right {{ $totalProfit >= 0 ? 'text-green' : 'text-red' }}">Rp {{ number_format($totalProfit, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
        @else
            <div class="empty-state">Tidak ada data penjualan untuk periode ini.</div>
        @endif

    {{-- ===================== STOK ===================== --}}
    @elseif($reportType === 'stok')
        <div class="summary-box">
            <h3>Ringkasan Stok Tersedia</h3>
            <table class="summary-table">
                <tr>
                    <td>Jumlah Unit Tersedia</td>
                    <td>{{ $stockUnits->count() }} unit</td>
                </tr>
                <tr style="border-top: 2px solid #2563eb;">
                    <td style="padding-top: 6px;"><strong>Total Nilai Stok</strong></td>
                    <td style="padding-top: 6px;">
                        <span class="summary-highlight">Rp {{ number_format($totalStockValue, 0, ',', '.') }}</span>
                    </td>
                </tr>
            </table>
        </div>

        @if($stockUnits->count() > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>QR Code</th>
                    <th>Produk</th>
                    <th>Kategori</th>
                    <th class="text-right">Nilai HPP</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stockUnits as $index => $unit)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="font-mono">{{ $unit->qr_code }}</td>
                    <td>{{ $unit->product->product_name }}</td>
                    <td>{{ $unit->product->category->name }}</td>
                    <td class="text-right">Rp {{ number_format($unit->purchase_price, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4"><strong>Total Nilai Stok</strong></td>
                    <td class="text-right">Rp {{ number_format($totalStockValue, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
        @else
            <div class="empty-state">Tidak ada stok tersedia.</div>
        @endif

    {{-- ===================== ADJUSTMENT ===================== --}}
    @elseif($reportType === 'adjustment')
        <div class="summary-box">
            <h3>Ringkasan Penyesuaian</h3>
            <table class="summary-table">
                <tr>
                    <td>Jumlah Penyesuaian</td>
                    <td>{{ $adjustments->count() }} item</td>
                </tr>
                <tr style="border-top: 2px solid #dc2626;">
                    <td style="padding-top: 6px;"><strong>Total Kerugian</strong></td>
                    <td style="padding-top: 6px;">
                        <span class="summary-highlight red">Rp {{ number_format($totalLoss, 0, ',', '.') }}</span>
                    </td>
                </tr>
            </table>
        </div>

        @if($adjustments->count() > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>QR / Produk</th>
                    <th>Jenis</th>
                    <th>Dilaporkan Oleh</th>
                    <th class="text-right">Kerugian</th>
                </tr>
            </thead>
            <tbody>
                @foreach($adjustments as $index => $adj)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $adj->date->format('d M Y') }}</td>
                    <td>{{ $adj->stockUnit->qr_code }} &mdash; {{ $adj->stockUnit->product->product_name }}</td>
                    <td><span class="badge badge-red">{{ ucfirst($adj->type) }}</span></td>
                    <td>{{ $adj->user->name ?? '-' }}</td>
                    <td class="text-right text-red">Rp {{ number_format($adj->stockUnit->purchase_price, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5"><strong>Total Kerugian</strong></td>
                    <td class="text-right text-red">Rp {{ number_format($totalLoss, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
        @else
            <div class="empty-state">Tidak ada penyesuaian untuk periode ini.</div>
        @endif

    {{-- ===================== BARANG MASUK ===================== --}}
    @elseif($reportType === 'masuk')
        <div class="summary-box">
            <h3>Ringkasan Barang Masuk</h3>
            <table class="summary-table">
                <tr>
                    <td>Total Unit Masuk</td>
                    <td>{{ $entries->sum('total_units') }} unit</td>
                </tr>
                <tr style="border-top: 2px solid #16a34a;">
                    <td style="padding-top: 6px;"><strong>Total Nilai Barang Masuk</strong></td>
                    <td style="padding-top: 6px;">
                        <span class="summary-highlight green">Rp {{ number_format($totalEntryValue, 0, ',', '.') }}</span>
                    </td>
                </tr>
            </table>
        </div>

        @if($entries->count() > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal Masuk</th>
                    <th>Produk</th>
                    <th>Catatan / Sumber</th>
                    <th class="text-center">Jml Unit</th>
                    <th class="text-right">Nilai Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($entries as $index => $entry)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($entry->received_date)->format('d M Y') }}</td>
                    <td>{{ $entry->product->product_name }}</td>
                    <td>{{ $entry->notes ?: '-' }}</td>
                    <td class="text-center">{{ $entry->total_units }}</td>
                    <td class="text-right text-green">Rp {{ number_format($entry->total_value, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4"><strong>Total</strong></td>
                    <td class="text-center"><strong>{{ $entries->sum('total_units') }}</strong></td>
                    <td class="text-right text-green">Rp {{ number_format($totalEntryValue, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
        @else
            <div class="empty-state">Tidak ada barang masuk untuk periode ini.</div>
        @endif
    @endif

    {{-- Footer --}}
    <div class="footer">
        <p>Dokumen ini digenerate secara otomatis oleh Sistem Informasi Manajemen Persediaan Barang &mdash; Toko Meliana Jaya</p>
        <p>Halaman 1</p>
    </div>
</body>
</html>
