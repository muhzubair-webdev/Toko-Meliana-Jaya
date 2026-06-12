<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peringatan Stok Menipis</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f6f9; color: #333;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f4f6f9; padding: 32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08);">
                    {{-- Header --}}
                    <tr>
                        <td style="background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%); padding: 28px 32px; text-align: center;">
                            <table role="presentation" cellspacing="0" cellpadding="0" style="margin: 0 auto;">
                                <tr>
                                    <td style="padding-right: 12px; vertical-align: middle;">
                                        <div style="width: 40px; height: 40px; background-color: rgba(255,255,255,0.2); border-radius: 50%; text-align: center; line-height: 40px; font-size: 20px;">⚠️</div>
                                    </td>
                                    <td style="vertical-align: middle;">
                                        <h1 style="margin: 0; color: #ffffff; font-size: 22px; font-weight: 700; letter-spacing: -0.3px;">Peringatan Stok Menipis</h1>
                                    </td>
                                </tr>
                            </table>
                            <p style="margin: 8px 0 0; color: rgba(255,255,255,0.85); font-size: 14px;">{{ $products->count() }} produk membutuhkan restock segera</p>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding: 28px 32px;">
                            <p style="margin: 0 0 20px; font-size: 15px; color: #555; line-height: 1.6;">
                                Halo Admin,<br>
                                Berikut adalah daftar produk yang stoknya sudah mencapai batas minimum dan perlu segera di-restock:
                            </p>

                            {{-- Products Table --}}
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; margin-bottom: 24px;">
                                <thead>
                                    <tr style="background-color: #f9fafb;">
                                        <th style="padding: 12px 16px; text-align: left; font-size: 11px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #e5e7eb;">Produk</th>
                                        <th style="padding: 12px 16px; text-align: left; font-size: 11px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #e5e7eb;">Kategori</th>
                                        <th style="padding: 12px 16px; text-align: center; font-size: 11px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #e5e7eb;">Stok Saat Ini</th>
                                        <th style="padding: 12px 16px; text-align: center; font-size: 11px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #e5e7eb;">Min. Stok</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($products as $product)
                                    <tr style="border-bottom: 1px solid #f3f4f6;">
                                        <td style="padding: 12px 16px; font-size: 14px; font-weight: 600; color: #111827;">
                                            {{ $product->product_name }} <span style="color: #9ca3af; font-weight: 400;">({{ $product->unit }})</span>
                                        </td>
                                        <td style="padding: 12px 16px; font-size: 13px; color: #6b7280;">
                                            {{ $product->category->name }}
                                        </td>
                                        <td style="padding: 12px 16px; text-align: center;">
                                            <span style="display: inline-block; padding: 4px 12px; border-radius: 9999px; font-size: 13px; font-weight: 700; {{ $product->available_count == 0 ? 'background-color: #fef2f2; color: #dc2626;' : 'background-color: #fffbeb; color: #d97706;' }}">
                                                {{ $product->available_count }}
                                            </span>
                                        </td>
                                        <td style="padding: 12px 16px; text-align: center; font-size: 13px; color: #6b7280;">
                                            {{ $product->min_stock }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            {{-- CTA Button --}}
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td align="center">
                                        <a href="{{ url('/stock') }}" style="display: inline-block; padding: 14px 32px; background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); color: #ffffff; text-decoration: none; border-radius: 8px; font-size: 14px; font-weight: 600; letter-spacing: 0.3px;">
                                            Buka Manajemen Stok →
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background-color: #f9fafb; padding: 20px 32px; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0; font-size: 12px; color: #9ca3af; text-align: center; line-height: 1.6;">
                                Email ini dikirim secara otomatis oleh sistem <strong>{{ config('app.name') }}</strong>.<br>
                                Dikirim pada {{ now()->translatedFormat('d F Y, H:i') }} WIB.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
