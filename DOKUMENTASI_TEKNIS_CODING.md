# DOKUMENTASI TEKNIS & ARSITEKTUR CODING (TECHNICAL HANDOVER)
## SISTEM INFORMASI MANAJEMEN APOTEK (SIM APOTEK)

---

## 1. Ringkasan Eksekutif & Stack Teknologi

Sistem Informasi Manajemen Apotek (SIM Apotek) dibangun dengan arsitektur **Monolith Modern (Full-Stack Reactive)** menggunakan framework **Laravel** dan **Livewire 3**, memungkinkan pengalaman pengguna yang interaktif (*Single Page Application feel*) tanpa kompleksitas API terpisah.

### Tech Stack:
* **Backend Framework:** Laravel 11 / 12 (PHP 8.2+)
* **Reactive Frontend:** Livewire 3.4+ & Livewire Volt
* **UI & Styling:** Tailwind CSS 3.x, Alpine.js, Blade Components
* **Database Engine:** MySQL 8.0+ / MariaDB 10.6+ (InnoDB Engine)
* **Authorization & Roles:** Spatie Laravel Permission 6.x
* **Export Reporting Engine:** 
  * Barryvdh Laravel DomPDF (PDF Reporting berstandar A4 & NCR)
  * Maatwebsite Excel (Import / Export format .xlsx)
* **Build Tool:** Vite 5.x & NPM

---

## 2. Struktur Direktori Utama

```
app/
├── Console/Commands/          # Artisan CLI Commands khusus maintenance & cron job
│   ├── BackupDatabase.php
│   ├── CheckUrgentAlerts.php
│   ├── FixExpenseJournals.php
│   ├── RecalculateBalances.php
│   ├── RepairJournals.php
│   ├── SendWeeklyBackup.php
│   └── SyncReturnReceivables.php   # Perbaikan & sinkronisasi retur tempo ke piutang
├── Exports/                   # Class export Excel (SalesReportExport, Aging, dll)
├── Helpers/                   # helpers.php (format rupiah, standard accounting, dll)
├── Http/Controllers/          # Controller untuk download export PDF, Excel, & Upload Import
│   ├── ExcelController.php
│   ├── ImportController.php
│   └── PdfController.php
├── Imports/                   # Importer Excel per modul (Produk, Stok, Akun, Omset)
├── Livewire/                  # Komponen reaktif UI & Business Controller
│   ├── Accounting/            # COA, Jurnal Umum, Buku Besar (General Ledger)
│   ├── Admin/                 # Manajemen User, Role, Backup
│   ├── Dashboard/             # Widget statistik & grafik performa real-time
│   ├── Finance/               # Laba Rugi, Saldo Awal, Pengeluaran (Expense), Aset Tetap
│   ├── Inventory/             # Stok, Batch/Kadaluarsa, Retur Penjualan & Pembelian
│   ├── Master/                # Produk, Kategori, Satuan, Supplier, Pelanggan
│   ├── Pos/                   # Modul Kasir (Cashier & Cetak Struk Nota)
│   ├── Procurement/           # Purchase Order (PO) & Penerimaan Barang (Goods Receipt)
│   ├── Reports/               # Laporan Penjualan, Margin, Aging Hutang/Piutang, Neraca, Arus Kas
│   └── Settings/              # Pengaturan Toko, POS, Buku Panduan (User Guide)
├── Models/                    # Eloquent Model (Relasi Database)
├── Notifications/             # Notifikasi email & alert darurat (kadaluarsa, piutang jatuh tempo)
├── Services/                  # Service Layer (Business Logic Terpusat)
│   ├── AccountingService.php  # Engine penjurnalan otomatis, COA, Laporan Keuangan
│   ├── ProfitLossService.php  # Kalkulasi Laba Rugi real-time & historis
│   └── TaxService.php         # Perhitungan PPN Keluaran / Masukan
└── Traits/
    └── HasPermissionStructure.php # Pemetaan permission granular per fitur
```

---

## 3. Skema Basis Data & Model Utama (Database Architecture)

### 3.1. Master Data & Produk
* `products`: Menyimpan data produk obat (`name`, `sku`, `barcode`, `category_id`, `min_stock`, `sell_price`, dll).
* `categories`: Kategori farmasi/obat (Generik, Paten, Alkes, Kosmetik, dll).
* `units` & `unit_conversions`: Satuan barang berjenjang (Box, Strip, Tablet) dengan rasio konversi.
* `suppliers`: Rekanan PBF (Pedagang Besar Farmasi) pemasok obat.
* `customers`: Data pasien / pelanggan umum apotek.

### 3.2. Inventori & Batch (FEFO - First Expired First Out)
* `batches`: Setiap penerimaan obat memiliki record batch tersendiri (`batch_number`, `expired_date`, `buy_price`, `stock_current`). Pengurangan stok di kasir memprioritaskan batch yang **tanggal kadaluarsanya paling dekat**.
* `stock_movements`: Kartu stok universal (`in`, `out`, `adjustment`, `return`). Mencatat pergerakan fisik stok beserta referensi dokumennya.

### 3.3. Penjualan & Kasir (POS)
* `sales`: Header transaksi kasir (`invoice_no`, `date`, `total_amount`, `discount`, `tax`, `rounding`, `grand_total`, `payment_method`, `cash_amount`, `status`).
  * `payment_method`: `cash`, `tempo`, `transfer`, `qris`.
* `sale_items`: Detail obat yang terjual, terhubung ke `batch_id` untuk akurasi HPP (*Cost of Goods Sold*).
* `sales_returns` & `sales_return_items`: Pengembalian obat dari pelanggan, mengembalikan stok ke batch asal serta memotong pendapatan/piutang.

### 3.4. Pengadaan (Procurement) & Hutang
* `purchase_orders` & `purchase_order_items`: Surat pesanan obat ke supplier (Status: `draft`, `ordered`, `partial`, `received`).
* `goods_receipts` & `goods_receipt_items`: Faktur fisik penerimaan barang dari supplier (surat jalan), mencatat harga beli riil dan nomor batch.
* `purchase_returns`: Pengembalian barang rusak/kadaluarsa ke supplier.
* `supplier_payments`: Pembayaran pelunasan hutang pembelian barang ke supplier.

### 3.5. Piutang (Receivables)
* `receivables`: Tagihan atas penjualan kasir bertipe `tempo`.
  * Kolom: `sale_id`, `customer_id`, `amount`, `paid_amount`, `remaining_balance`, `status` (`unpaid`, `partial`, `paid`), `due_date`.
* `receivable_payments`: Riwayat cicilan / pelunasan piutang oleh pelanggan.

### 3.6. Akuntansi & Keuangan (Automated Double-Entry)
* `accounts`: Chart of Accounts (COA) hierarkis:
  * `1-XXXX`: Aset (Kas, Bank, Piutang Usaha `1-1300`, Persediaan `1-1400`, Aset Tetap).
  * `2-XXXX`: Kewajiban (Utang Jatuh Tempo / Dagang `2-1200`, PPN Keluaran `2-1400`).
  * `3-XXXX`: Ekuitas (Modal Awal, Laba Ditahan).
  * `4-XXXX`: Pendapatan (Penjualan `4-1000`, Retur Penjualan `4-1100`).
  * `5-XXXX`: Beban & HPP (HPP / COGS `5-1000`, Retur Pembelian `5-1100`, Biaya Operasional `5-2XXX`).
* `journal_entries`: Header jurnal transaksi (`entry_number`, `date`, `source`, `source_id`, `is_posted`).
* `journal_entry_lines`: Detail baris jurnal Debit dan Kredit (`debit`, `credit`, `account_id`, `notes`).
* `expenses` & `expense_categories`: Pencatatan pengeluaran operasional toko (listrik, gaji, sewa, ATK).
* `fixed_assets` & `asset_depreciations`: Inventaris barang modal apotek dan penyusutan bulanan otomatis.
* `opening_balances`: Kunci saldo awal kas, bank, persediaan, dan modal saat pertama kali migrasi.

---

## 4. Logika Bisnis Inti (Core Business Flows)

### 4.1. Alur Transaksi Kasir (POS)
1. Kasir memilih produk & satuan. Sistem secara otomatis memilih **Batch dengan tanggal kadaluarsa terdekat (FEFO)** yang masih memiliki stok.
2. Saat transaksi disimpan (`Cashier::saveSale`):
   * Insert ke tabel `sales` & `sale_items`.
   * Stok batch didekrementasi (`Batch::decrement`).
   * Record `StockMovement` dicatat dengan tipe `out`.
   * Jika metode pembayaran `tempo`: Sistem membuat record `receivables` dengan `remaining_balance = grand_total - cash_amount`.
   * Menjalankan otomatisasi jurnal akuntansi via `AccountingService::postSaleJournal()`.

### 4.2. Penjurnalan Otomatis Kasir
* **Penjualan Tunai / Transfer:**
  * `Dr. Kas (1-1100)` atau `Dr. Bank (1-1200)` $[Grand\ Total]$
  * `Cr. Penjualan (4-1000)` $[DPP]$
  * `Cr. PPN Keluaran (2-1400)` $[Pajak\ jika\ ada]$
  * `Dr. COGS / HPP (5-1000)` $[Total\ Modal\ Obat]$
  * `Cr. Persediaan (1-1400)` $[Total\ Modal\ Obat]$
* **Penjualan Tempo (Kredit):**
  * `Dr. Kas (1-1100)` $[Uang\ Muka\ /\ DP\ jika\ ada]$
  * `Dr. Piutang Usaha (1-1300)` $[Sisa\ Tagihan\ Tempo]$
  * `Cr. Penjualan & PPN` $[Total]$
  * `Dr. COGS` & `Cr. Persediaan` $[Modal\ Obat]$

### 4.3. Alur Retur Penjualan & Sinkronisasi Piutang
Ketika kasir memproses retur penjualan di menu `SalesReturnList`:
1. Sistem menambah kembali stok ke `batch_id` yang sesuai dan mencatat `StockMovement` tipe `return`.
2. Record `sales_returns` dan itemnya dibuat.
3. **Pengecekan Piutang:** Jika invoice penjualan memiliki record `receivables`:
   * Saldo sisa piutang (`remaining_balance`) dipotong sebesar nominal retur:
     $$\text{Sisa Piutang Baru} = \max(0, \text{remaining\_balance} - \text{nominal\_retur})$$
   * Jika sisa saldo mencapai 0, status diubah menjadi `paid` (lunas/selesai) sehingga tagihan **otomatis hilang dari Aging Report**.
4. **Jurnal Retur Penjualan:**
   * `Dr. Retur Penjualan (4-1100)` $[Total\ Retur]$
   * `Cr. Piutang Usaha (1-1300)` $[Sebesar\ piutang\ yang\ belum\ dibayar]$
   * `Cr. Kas (1-1100)` $[Hanya\ jika\ ada\ kelebihan\ bayar/DP\ yang\ dikembalikan\ tunai]$
   * `Dr. Persediaan (1-1400)` $[Modal\ barang\ yang\ kembali]$
   * `Cr. COGS (5-1000)` $[Pengurangan\ HPP]$

---

## 5. Command Artisan Khusus (CLI Maintenance)

Sistem dilengkapi sejumlah perintah konsol untuk pemeliharaan data di server produksi:

| Command Artisan | Fungsi & Kapan Digunakan |
|---|---|
| `php artisan finance:sync-return-receivables` | Menyelaraskan saldo piutang dan jurnal retur tempo historis (mengatasi masalah piutang gantung dan kas minus akibat retur tempo). Mendukung opsi `--dry-run`. |
| `php artisan finance:repair-journals` | Menghasilkan ulang (*regenerate*) seluruh jurnal akuntansi yang hilang dari transaksi penjualan, pembelian, retur, dan pembayaran lama. |
| `php artisan finance:recalculate-balances` | Menghitung ulang saldo buku besar (GL) dan saldo kas/bank dari seluruh mutasi jurnal secara presisi. |
| `php artisan finance:fix-expense-journals` | Membersihkan jurnal yatim (*orphaned journals*) dari pengeluaran yang pernah diedit/dihapus di masa lalu. |
| `php artisan backup:database` | Menjalankan *dump* database otomatis dan menyimpannya di direktori penyimpanan lokal. |
| `php artisan email:urgent-alerts` | Mengecek dan mengirim email peringatan obat kadaluarsa dan piutang yang mendekati jatuh tempo. |

---

## 6. Prosedur Deployment & Sinkronisasi Server (VPS / Cloud)

### Skrip Deployment Otomatis (`deploy.sh`)
Pada server produksi (Ubuntu / Debian), deployment dan update kode dilakukan dengan menjalankan:
```bash
bash deploy.sh
```

Isi tahapan yang dieksekusi:
```bash
# 1. Tarik pembaruan dari Git
git pull origin main

# 2. Update dependensi tanpa paket dev
composer install --no-dev --optimize-autoloader
npm install

# 3. Build aset frontend (Tailwind & JS)
npm run build

# 4. Migrasi skema database (jika ada update)
php artisan migrate --force

# 5. Reset cache permission Spatie & optimize Laravel
php artisan permission:cache-reset
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Konfigurasi Cron Job Server (`crontab -e`)
Jadwalkan task scheduler Laravel untuk notifikasi otomatis dan backup database mingguan:
```cron
* * * * * cd /path-ke-projek/apotek && php artisan schedule:run >> /dev/null 2>&1
```

### Rekomendasi Web Server (Nginx)
* Pastikan `client_max_body_size 64M;` untuk mendukung import data master file Excel besar.
* Direktori `storage` dan `bootstrap/cache` harus memiliki hak akses tulis:
  ```bash
  chmod -R 775 storage bootstrap/cache
  chown -R www-data:www-data storage bootstrap/cache
  ```

---

## 7. Lembar Pengesahan Serah Terima Kode Program & Teknis (Technical Handover)

Dokumen arsitektur dan repositori source code Sistem Informasi Manajemen Apotek (SIM Apotek) ini telah diserahkan dan diverifikasi secara teknis:

| Pihak | Identitas & Peran |
|---|---|
| **PIHAK PERTAMA (Pengembang / Developer)** | Nama: ....................................................<br>Jabatan: Lead Software Engineer |
| **PIHAK KEDUA (Klien / IT Representative)** | Nama: ....................................................<br>Jabatan: IT Manager / Penanggung Jawab Teknis Klien |

Kedua belah pihak menyatakan bahwa source code aplikasi, skema database, dokumentasi API/komponen, serta akses server telah diserahterimakan dengan lengkap.

<br>

| PIHAK PERTAMA (Developer) | PIHAK KEDUA (Klien / IT) |
|:---:|:---:|
| <br><br><br><br>____________________________<br>**( .................................................. )** | <br><br><br><br>____________________________<br>**( .................................................. )** |

