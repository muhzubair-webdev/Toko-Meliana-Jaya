Tepat sekali! PWA (Progressive Web App) pada dasarnya adalah "bungkus" yang membuat website kamu bisa diinstal di HP. Selama HP dan laptop (server) berada di jaringan Wi-Fi yang sama, PWA akan berfungsi dengan lancar tanpa perlu internet sama sekali. Ini adalah solusi paling cerdas untuk efisiensi di toko.

Berikut adalah **Product Requirements Document (PRD)** versi final yang telah disempurnakan beserta struktur database lengkapnya.

---

# Product Requirements Document (PRD)

**Project Name:** Sistem Informasi Manajemen Persediaan Toko Meliana Jaya

**Methodology:** Specific Identification (Identifikasi Khusus)

**Framework:** Laravel 12

**Database:** MySQL (XAMPP)

**Deployment:** Local Network Access (Wi-Fi) via PWA

---

## 1. Pendahuluan

Sistem ini dirancang untuk mendigitalisasi pencatatan stok pada Toko Meliana Jaya yang bergerak di bidang kelistrikan dan bahan plastik. Fokus utama adalah akurasi stok menggunakan metode **Specific Identification** dan kemudahan operasional melalui perangkat smartphone tanpa bergantung pada koneksi internet publik.

---

## 2. Fitur Utama (Functional Requirements)

### 2.1. Katalog Produk & Harga Patokan

* **Default Pricing:** Setiap produk memiliki "Harga Jual Saran" agar input transaksi lebih cepat.
* **Threshold Stok:** Peringatan otomatis jika stok mencapai batas minimum.

### 2.2. Manajemen Stok (Metode Identifikasi Khusus)

* **QR Code Generation:** Sistem otomatis membuat kode unik untuk setiap unit barang listrik atau batch barang plastik.
* **Inbound Tracking:** Mencatat harga beli asli (HPP) pada setiap unit/batch yang masuk.

### 2.3. Transaksi Keluar (Penjualan)

* **Hybrid Input:** Bisa menggunakan scan QR dari kamera HP atau menggunakan pencarian *Autocomplete* (jika label rusak).
* **Negotiable Pricing:** Sistem menampilkan harga saran, namun user dapat mengubahnya secara manual jika ada kesepakatan harga (diskon/nego) dengan pembeli.

### 2.4. Penyesuaian Stok (Adjustment)

* **Asset Protection:** Fitur untuk menandai barang yang hilang atau rusak agar data stok tetap sinkron dengan fisik di toko.
* **Loss Reporting:** Mencatat kerugian berdasarkan harga beli unit yang disesuaikan.

### 2.5. Pelaporan Profesional

* **Laba/Rugi Real-time:** Menghitung laba berdasarkan harga jual final dikurangi harga beli spesifik unit tersebut.
* **Laporan Penjualan & Stok:** Rekapitulasi harian/bulanan yang dapat diekspor ke PDF/Excel.

---

## 3. Spesifikasi Teknis & Infrastruktur

* **Local Server:** Satu laptop sebagai pusat database (Server XAMPP).
* **PWA Experience:** User membuka alamat IP laptop di browser HP, lalu memilih "Add to Home Screen" agar sistem muncul sebagai aplikasi di menu HP.
* **Network:** Komunikasi data dilakukan melalui jaringan Wi-Fi lokal (tanpa kuota internet).

---

## 4. Struktur Basis Data (Final Schema)

| Tabel | Kolom Utama | Penjelasan |
| --- | --- | --- |
| **`categories`** | `id`, `name` | Membedakan barang Listrik vs Plastik. |
| **`products`** | `id`, `category_id`, `product_name`, `unit`, `min_stock`, **`suggested_price`** | Master data barang beserta harga jual patokan. |
| **`stock_units`** | `id`, `product_id`, `qr_code`, **`purchase_price`**, `status`, `received_date` | Data per unit fisik. Status: Tersedia, Terjual, Rusak, Hilang. |
| **`sales`** | `id`, `invoice_number`, `sale_date`, `total_price` | Header transaksi penjualan. |
| **`sale_details`** | `id`, `sale_id`, `stock_unit_id`, **`final_price`** | Mencatat unit mana yang keluar dan harga deal akhirnya. |
| **`stock_adjustments`** | `id`, `stock_unit_id`, `type`, `date`, `notes` | Riwayat barang yang tidak terjual (hilang/rusak). |

---

## 5. Alur Logika Bisnis (Business Logic)

1. **Pengadaan:** Barang masuk → Input Harga Beli & Harga Jual Saran → Tempel QR.
2. **Penjualan:** Scan QR/Ketik Nama → Harga Jual Saran muncul otomatis → Edit harga jika nego → Simpan.
3. **Adjustment:** Barang rusak ditemukan → Scan QR → Pilih status 'Rusak' → Masukkan keterangan.
4. **Laporan:** Sistem membandingkan `sale_details.final_price` dengan `stock_units.purchase_price` untuk menghasilkan nilai laba yang 100% akurat.

---

### Saran Tambahan untuk Implementasi Laravel 12:

* **PWA Setup:** Kamu bisa menggunakan package seperti `silviolleite/laravel-pwa` atau sekadar membuat file `manifest.json` dan *service worker* sederhana secara manual.
* **QR Scanner:** Untuk fitur scan di HP, gunakan library **JavaScript** `html5-qrcode`. Library ini sangat ringan dan bisa langsung mengakses kamera dari browser Chrome/Safari di HP.
* **UI/UX:** Gunakan **Tailwind CSS** agar tampilan di smartphone terlihat modern, bersih, dan seperti aplikasi profesional.
