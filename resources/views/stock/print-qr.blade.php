<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Print QR - {{ $stockUnit->qr_code }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Courier New', monospace; display: flex; justify-content: center; align-items: center; min-height: 100vh; background: #f3f4f6; }
        .qr-card { background: white; border: 2px solid #000; padding: 16px; text-align: center; width: 240px; }
        .qr-card h3 { font-size: 11px; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 1px; }
        .qr-card .qr-image { margin: 8px auto; }
        .qr-card .qr-code { font-size: 16px; font-weight: bold; letter-spacing: 2px; margin: 8px 0; }
        .qr-card .product-name { font-size: 10px; color: #555; }
        .qr-card .price { font-size: 12px; margin-top: 4px; font-weight: bold; }
        .no-print { margin-top: 16px; text-align: center; }
        @media print {
            body { background: white; }
            .no-print { display: none; }
            .qr-card { border: 2px solid #000; }
        }
    </style>
</head>
<body>
    <div>
        <div class="qr-card">
            <h3>Toko Meliana Jaya</h3>
            <div class="qr-image" id="qrcode"></div>
            <div class="qr-code">{{ $stockUnit->qr_code }}</div>
            <div class="product-name">{{ $stockUnit->product->product_name }} ({{ $stockUnit->product->unit }})</div>
            <div class="price">Rp {{ number_format($stockUnit->product->suggested_price, 0, ',', '.') }}</div>
        </div>
        <div class="no-print">
            <button onclick="window.print()" style="padding:8px 24px;background:#16a34a;color:white;border:none;border-radius:4px;cursor:pointer;font-size:14px;">🖨️ Print</button>
            <button onclick="window.close()" style="padding:8px 24px;background:#6b7280;color:white;border:none;border-radius:4px;cursor:pointer;font-size:14px;margin-left:8px;">Tutup</button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.min.js"></script>
    <script>
        var qr = qrcode(0, 'M');
        qr.addData('{{ $stockUnit->qr_code }}');
        qr.make();
        document.getElementById('qrcode').innerHTML = qr.createSvgTag(5, 0);
    </script>
</body>
</html>
