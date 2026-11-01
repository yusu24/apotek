<?php

namespace App\Livewire\Settings;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
class UserGuide extends Component
{
    #[Url(as: 'q')]
    public $search = '';

    #[Url(as: 'tab')]
    public $activeCategory = 'all';

    public $selectedSubMenuSlug = null;

    public function mount()
    {
        if (!auth()->user()->can('view guide')) {
            abort(403, 'Unauthorized');
        }

        $valid = ['all', 'dashboard', 'pos', 'stock', 'returns', 'master', 'finance_reports', 'op_reports', 'finance_admin', 'settings'];
        if (!in_array($this->activeCategory, $valid)) {
            $this->activeCategory = 'all';
        }
    }

    public function setCategory($cat)
    {
        $this->activeCategory = $cat;
    }

    public function openSubMenuDetail($slug)
    {
        $this->selectedSubMenuSlug = $slug;
    }

    public function closeDetail()
    {
        $this->selectedSubMenuSlug = null;
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->activeCategory = 'all';
    }

    public function render()
    {
        $groups = $this->getMainMenuGroupsData();

        // Find selected sub-menu if any
        $selectedSubMenu = null;
        if ($this->selectedSubMenuSlug) {
            foreach ($groups as $grp) {
                foreach ($grp['sub_menus'] as $sub) {
                    if ($sub['slug'] === $this->selectedSubMenuSlug) {
                        $selectedSubMenu = $sub;
                        $selectedSubMenu['parent_title'] = $grp['title'];
                        $selectedSubMenu['parent_color'] = $grp['color'];
                        break 2;
                    }
                }
            }
        }

        // Apply filters
        $filteredGroups = [];
        $totalSubMenus = 0;
        $matchedSubMenus = 0;

        foreach ($groups as $grp) {
            $totalSubMenus += count($grp['sub_menus']);

            // Filter category
            if ($this->activeCategory !== 'all' && $grp['id'] !== $this->activeCategory) {
                continue;
            }

            // Filter search
            if (!empty($this->search)) {
                $q = strtolower(trim($this->search));
                $matchingSubs = [];

                foreach ($grp['sub_menus'] as $sub) {
                    $searchable = strtolower(
                        $grp['title'] . ' ' .
                        $sub['title'] . ' ' .
                        $sub['function'] . ' ' .
                        ($sub['route_url'] ?? '') . ' ' .
                        ($sub['keywords'] ?? '') . ' ' .
                        json_encode($sub['cards'] ?? []) . ' ' .
                        json_encode($sub['steps'] ?? [])
                    );

                    if (str_contains($searchable, $q)) {
                        $matchingSubs[] = $sub;
                    }
                }

                if (!empty($matchingSubs)) {
                    $grpCopy = $grp;
                    $grpCopy['sub_menus'] = $matchingSubs;
                    $filteredGroups[] = $grpCopy;
                    $matchedSubMenus += count($matchingSubs);
                }
            } else {
                $filteredGroups[] = $grp;
                $matchedSubMenus += count($grp['sub_menus']);
            }
        }

        return view('livewire.settings.user-guide', [
            'groups' => $filteredGroups,
            'selectedSubMenu' => $selectedSubMenu,
            'categories' => $this->getCategoriesList(),
            'totalSubMenus' => $totalSubMenus,
            'matchedSubMenus' => $matchedSubMenus,
            'totalGroups' => count($groups),
            'matchedGroups' => count($filteredGroups),
        ]);
    }

    public function getCategoriesList()
    {
        return [
            ['key' => 'all', 'label' => 'Semua Menu', 'sub_count' => '32 Sub-Menu', 'icon' => 'squares'],
            ['key' => 'dashboard', 'label' => 'Dashboard', 'sub_count' => '1 Sub-Menu', 'icon' => 'home'],
            ['key' => 'pos', 'label' => 'Kasir (POS)', 'sub_count' => '1 Sub-Menu', 'icon' => 'cart'],
            ['key' => 'stock', 'label' => 'Stok & Pengadaan', 'sub_count' => '3 Sub-Menu', 'icon' => 'stock'],
            ['key' => 'returns', 'label' => 'Retur Barang', 'sub_count' => '2 Sub-Menu', 'icon' => 'returns'],
            ['key' => 'master', 'label' => 'Data Master', 'sub_count' => '5 Sub-Menu', 'icon' => 'cube'],
            ['key' => 'finance_reports', 'label' => 'Laporan Keuangan', 'sub_count' => '6 Sub-Menu', 'icon' => 'finance'],
            ['key' => 'op_reports', 'label' => 'Laporan Operasional', 'sub_count' => '4 Sub-Menu', 'icon' => 'reports'],
            ['key' => 'finance_admin', 'label' => 'Keuangan & Administrasi', 'sub_count' => '5 Sub-Menu', 'icon' => 'cash'],
            ['key' => 'settings', 'label' => 'Pengaturan Sistem', 'sub_count' => '5 Sub-Menu', 'icon' => 'settings'],
        ];
    }

    public function getMainMenuGroupsData()
    {
        return [
            // ==================== 1. DASHBOARD ====================
            [
                'id' => 'dashboard',
                'title' => 'Dashboard',
                'color' => 'blue',
                'description' => 'Pusat ringkasan dan kendali utama performa operasional serta finansial apotek secara real-time.',
                'sub_menus' => [
                    [
                        'slug' => 'dashboard-main',
                        'title' => 'Dashboard Utama',
                        'route_name' => 'dashboard',
                        'route_url' => '/dashboard',
                        'function' => 'Memantau performa harian apotek secara real-time: total katalog obat, peringatan obat kritis/stok menipis, jumlah transaksi kasir hari ini, omset berjalan, dan tren penjualan.',
                        'cards' => [
                            [
                                'name' => 'Card Total Katalog Obat',
                                'value_desc' => 'Jumlah keseluruhan varian obat & alkes aktif yang terdata di apotek.',
                                'source' => 'Dihitung dari query Product::count() pada tabel products.',
                                'formula' => 'COUNT(products.id)',
                                'notes' => 'Bertambah otomatis setiap kali ada obat baru yang ditambahkan di Data Master.',
                            ],
                            [
                                'name' => 'Card Stok Menipis (Alert)',
                                'value_desc' => 'Jumlah batch obat yang sisa stok fisiknya sudah menyentuh atau berada di bawah batas minimum.',
                                'source' => 'Dihitung dari query Product::lowStock()->count() pada tabel batches.',
                                'formula' => 'Stok Batch Saat Ini ≤ Batas Minimum Produk',
                                'notes' => 'Jika berwarna kuning/oranye, petugas gudang disarankan segera membuat Purchase Order (PO).',
                            ],
                            [
                                'name' => 'Card Penjualan Hari Ini',
                                'value_desc' => 'Jumlah nota/struk kasir dan total uang belanja yang berhasil diselesaikan pada hari ini.',
                                'source' => 'Dihitung dari Sale::whereDate(\'date\', today())->where(\'status\', \'completed\').',
                                'formula' => '∑ Transaksi Selesai & ∑ Nilai Belanja Hari Ini',
                                'notes' => 'Otomatis ter-refresh setiap kali kasir menyelesaikan transaksi belanja.',
                            ],
                            [
                                'name' => 'Card Omset Bulan Ini',
                                'value_desc' => 'Akumulasi total omset kotor dari seluruh transaksi penjualan sejak tanggal 1 bulan berjalan.',
                                'source' => 'Dihitung dari Sale::whereBetween(\'date\', [awal_bulan, akhir_bulan])->sum(\'grand_total\').',
                                'formula' => '∑ Grand Total Penjualan Bulan Berjalan',
                                'notes' => 'Membantu pemilik apotek melihat pencapaian target penjualan bulanan.',
                            ],
                            [
                                'name' => 'Grafik Tren Omset & Klasemen Kasir',
                                'value_desc' => 'Grafik fluktuasi omset harian/mingguan dan ranking kasir dengan kontribusi penjualan tertinggi.',
                                'source' => 'Dikelompokkan berdasarkan kolom sales.date dan relasi sales.user_id.',
                                'formula' => 'Omset per Tanggal & Total Penjualan per Kasir',
                                'notes' => 'Berguna untuk evaluasi kinerja kasir dan melihat hari-hari sibuk apotek.',
                            ],
                        ],
                        'steps' => [
                            'Buka halaman Dashboard setelah login untuk melihat kondisi kesehatan bisnis apotek secara sepintas.',
                            'Perhatikan indikator <strong>Stok Menipis</strong>: jika ada obat bertanda alert, klik tombol "Cek Stok" untuk melihat obat apa saja yang perlu di-restock.',
                            'Periksa card <strong>Penjualan Hari Ini</strong> saat jam operasional siang atau sore hari untuk memantau ritme transaksi kasir.',
                            'Gunakan dropdown filter periode (Harian, Mingguan, Bulanan) pada grafik tren untuk membandingkan performa penjualan.',
                        ],
                        'keywords' => 'dashboard beranda ringkasan katalog stok menipis omset kasir leaderboard',
                    ],
                ],
            ],

            // ==================== 2. KASIR (POS) ====================
            [
                'id' => 'pos',
                'title' => 'Kasir (POS)',
                'color' => 'emerald',
                'description' => 'Layanan penjualan langsung ke pasien, scan barcode, penanganan resep racikan, diskon, PPN, dan cetak struk kasir.',
                'sub_menus' => [
                    [
                        'slug' => 'pos-cashier',
                        'title' => 'Kasir Penjualan (Point of Sale)',
                        'route_name' => 'pos.cashier',
                        'route_url' => '/cashier',
                        'function' => 'Menu kerja staf kasir untuk melayani transaksi penjualan langsung ke pasien, scan barcode obat, menangani resep racikan dokter, menghitung diskon & pajak PPN, memilih metode pembayaran (Tunai, Bank/QRIS, Piutang), serta mencetak struk belanja thermal.',
                        'cards' => [
                            [
                                'name' => 'Subtotal Item Belanja',
                                'value_desc' => 'Nilai perkalian kuantitas obat dengan harga satuan setelah diskon per baris item.',
                                'source' => 'Dihitung di keranjang kasir dari products.sell_price dikurangi diskon produk.',
                                'formula' => '(Qty × Harga Satuan) - Diskon Item',
                                'notes' => 'Sistem otomatis mendeteksi batch dengan masa expired paling dekat (prinsip FEFO - First Expired First Out).',
                            ],
                            [
                                'name' => 'Tuslah & Embalase (Obat Resep)',
                                'value_desc' => 'Biaya tambahan jasa keahlian peracikan obat oleh apoteker (tuslah) serta wadah kemasan plastik klip/kapsul (embalase).',
                                'source' => 'Diinput pada panel transaksi resep dokter di kasir.',
                                'formula' => 'Jasa Peracikan R/ + Biaya Wadah Kemasan',
                                'notes' => 'Mencegah kerugian biaya bahan pembungkus obat dan menghargai jasa farmasi.',
                            ],
                            [
                                'name' => 'Pajak PPN (11%)',
                                'value_desc' => 'Nominal pajak pertambahan nilai yang dipungut dari transaksi penjualan.',
                                'source' => 'Dihitung otomatis sesuai tarif PPN yang aktif pada Pengaturan Toko.',
                                'formula' => 'DPP × 11% (atau include: Total - Total/1.11)',
                                'notes' => 'Tergantung opsi apakah harga toko diatur include atau exclude PPN.',
                            ],
                            [
                                'name' => 'Grand Total & Kembalian',
                                'value_desc' => 'Nominal final yang wajib dibayar pasien serta sisa uang kembalian yang harus diserahkan kasir.',
                                'source' => 'Grand Total = (Subtotal - Diskon Nota) + PPN + Biaya Resep; Kembalian = Uang Diterima - Grand Total.',
                                'formula' => 'Kembalian = Uang Diterima Pasien - Grand Total',
                                'notes' => 'Mendukung pembayaran Tunai, QRIS/Transfer Bank, maupun Piutang (Kredit tempo pelanggan).',
                            ],
                        ],
                        'steps' => [
                            'Scan barcode obat menggunakan barcode scanner atau ketik minimal 3 huruf nama obat di kolom pencarian.',
                            'Pilih satuan obat yang dibeli (misal: Strip atau Box), sistem otomatis mengalikan harga sesuai konversi satuan.',
                            'Masukkan jumlah kuantitas (Qty) yang dibeli pasien.',
                            'Jika ada resep racikan, klik opsi Resep Racikan lalu masukkan komponen obat beserta biaya tuslah/embalase.',
                            'Jika ada potongan khusus nota (misal diskon member), isi pada kolom Diskon Nota.',
                            'Pilih metode pembayaran: <strong>Tunai</strong> (masukkan nominal uang pasien), <strong>Transfer/QRIS</strong> (pilih rekening tujuan), atau <strong>Piutang/Tempo</strong> (pilih data nama pelanggan).',
                            'Klik <strong>Bayar & Cetak Struk</strong> (atau tekan tombol pintas F9/Enter). Printer thermal kasir otomatis mencetak struk belanja.',
                        ],
                        'keywords' => 'kasir pos transaksi bayar struk scan barcode resep racikan tuslah embalase diskon kembalian',
                    ],
                ],
            ],

            // ==================== 3. STOK & PENGADAAN ====================
            [
                'id' => 'stock',
                'title' => 'Stok & Pengadaan',
                'color' => 'purple',
                'description' => 'Manajemen persediaan obat, stok opname, surat pesanan (PO) ke PBF, dan penerimaan kiriman faktur barang.',
                'sub_menus' => [
                    [
                        'slug' => 'stock-inventory',
                        'title' => 'Stok & Opname',
                        'route_name' => 'inventory.index',
                        'route_url' => '/stock',
                        'function' => 'Memantau ketersediaan fisik obat di rak dan gudang, mengontrol tanggal kadaluarsa (expired date), serta melakukan audit fisik (stok opname) dan penyesuaian jika ada obat hilang, rusak, atau tercecer.',
                        'cards' => [
                            [
                                'name' => 'Banner Alert Stok Menipis',
                                'value_desc' => 'Kotak peringatan oranye yang menunjukkan jumlah item obat yang jumlahnya sudah berada di bawah ambang batas minimal.',
                                'source' => 'Dihitung dari relasi produk dengan jumlah stok fisik yang ada pada tabel batches.',
                                'formula' => 'Total Fisik ≤ Batas Stok Minimum Produk',
                                'notes' => 'Klik tombol filter "Stok Menipis" untuk langsung menampilkan obat-obat darurat yang wajib dipesan.',
                            ],
                            [
                                'name' => 'Kolom Stok Fisik per Satuan',
                                'value_desc' => 'Sisa kuantitas fisik riil obat dalam satuan terkecil (tablet, kapsul, strip, botol).',
                                'source' => 'Akumulasi dari kolom batches.stock_current untuk produk terkait.',
                                'formula' => '∑ stock_current dari batch yang belum kadaluarsa',
                                'notes' => 'Stok berkurang otomatis saat kasir menjual obat, dan bertambah saat penerimaan pesanan dari PBF.',
                            ],
                        ],
                        'steps' => [
                            'Gunakan kolom pencarian obat untuk mengecek ketersediaan obat tertentu secara cepat.',
                            'Gunakan tombol filter "Stok Menipis" untuk mendata obat yang harus segera dibuatkan Surat Pesanan (PO).',
                            'Untuk melihat riwayat keluar-masuk obat, klik tombol <strong>Riwayat</strong> (ikon jam/mata) di samping baris obat.',
                            'Untuk melakukan penyesuaian fisik (Stok Opname): klik tombol <strong>Penyesuaian Stok</strong>, pilih batch yang dihitung, masukkan jumlah fisik riil di rak, pilih alasan (misal: Rusak, Selisih Opname, Expired), lalu simpan.',
                        ],
                        'keywords' => 'stok inventori gudang opname fisik expired kadaluarsa batch penyesuaian',
                    ],
                    [
                        'slug' => 'procurement-po',
                        'title' => 'Pesanan Pembelian (PO)',
                        'route_name' => 'procurement.purchase-orders.index',
                        'route_url' => '/procurement/purchase-orders',
                        'function' => 'Membuat dokumen Surat Pesanan (SP / Purchase Order) resmi kepada distributor farmasi (PBF) untuk pengadaan obat, melacak status pesanan (Draft, Disetujui, Dipesan, Diterima, Dibatalkan), dan mencetak SP berformat standar farmasi.',
                        'cards' => [
                            [
                                'name' => 'Total Nominal PO',
                                'value_desc' => 'Estimasi total anggaran belanja yang dipesan ke distributor PBF.',
                                'source' => 'Dihitung dari perkalian jumlah unit yang dipesan dengan estimasi harga beli modal (HNA).',
                                'formula' => '∑ (Qty Pesan × Estimasi Harga Beli)',
                                'notes' => 'Status PO belum memengaruhi hutang maupun stok fisik sebelum barang fisiknya benar-benar diterima di menu Penerimaan Pesanan.',
                            ],
                        ],
                        'steps' => [
                            'Klik tombol <strong>Buat PO Baru</strong>.',
                            'Pilih nama <strong>Supplier / PBF</strong> tujuan pemesanan.',
                            'Pilih obat-obatan yang ingin dipesan beserta jumlah kuantitas dan satuannya (Box, Botol, Strip).',
                            'Simpan dokumen. Status awal adalah <strong>Draft</strong>.',
                            'Jika disetujui oleh Apoteker Penanggung Jawab (APJ), ubah status menjadi <strong>Disetujui</strong>.',
                            'Cetak Surat Pesanan (SP) untuk ditandatangani dan dicap oleh Apoteker dengan mencantumkan Nomor SIPA, lalu serahkan ke sales distributor.',
                        ],
                        'keywords' => 'po purchase order surat pesanan pbf supplier distributor pengadaan beli',
                    ],
                    [
                        'slug' => 'procurement-gr',
                        'title' => 'Penerimaan Pesanan (Goods Receipt)',
                        'route_name' => 'procurement.goods-receipts.index',
                        'route_url' => '/procurement/goods-receipts',
                        'function' => 'Mencatat faktur dan fisik barang yang dikirim oleh distributor PBF ke apotek. Menginput Nomor Batch pabrik, Tanggal Kadaluarsa (Expired Date), dan Harga Beli Netto (HNA). Pada saat disimpan, stok otomatis bertambah dan saldo Hutang Dagang (AP) otomatis terbentuk.',
                        'cards' => [
                            [
                                'name' => 'Total Nilai Faktur Penerimaan',
                                'value_desc' => 'Total tagihan faktur pembelian yang mencakup DPP pembelian, diskon distributor, dan PPN Masukan 11%.',
                                'source' => 'Dihitung dari subtotal item faktur dikurangi diskon faktur ditambah PPN Masukan.',
                                'formula' => '(Subtotal - Diskon PBF) + PPN Masukan 11%',
                                'notes' => 'Menjadi dasar pencatatan saldo Hutang Dagang pada Laporan Aging Report.',
                            ],
                        ],
                        'steps' => [
                            'Saat kurir PBF mengantar barang beserta Faktur/Surat Jalan, klik tombol <strong>Terima Pesanan Baru</strong>.',
                            'Pilih nomor PO terkait (jika sebelumnya membuat PO), atau pilih Supplier PBF langsung jika pembelian langsung.',
                            'Ketik <strong>Nomor Faktur Distributor</strong> dan tanggal jatuh tempo pembayaran.',
                            'Cek fisik obat satu per satu: masukkan <strong>Nomor Batch</strong> yang tercetak di kemasan, <strong>Tanggal Expired</strong>, jumlah fisik yang datang, dan harga beli.',
                            'Periksa apakah ada diskon faktur atau PPN Masukan 11% yang tercantum pada faktur PBF.',
                            'Klik <strong>Simpan Penerimaan</strong>. Sistem secara otomatis: menambah stok fisik obat per batch, memperbarui HPP modal, dan mencatat hutang dagang apotek.',
                        ],
                        'keywords' => 'penerimaan goods receipt faktur surat jalan batch expired hna pbf hutang',
                    ],
                ],
            ],

            // ==================== 4. RETUR BARANG ====================
            [
                'id' => 'returns',
                'title' => 'Retur Barang',
                'color' => 'rose',
                'description' => 'Pengembalian obat dari pasien (retur penjualan) dan klaim retur obat rusak/ED ke distributor PBF (retur pembelian).',
                'sub_menus' => [
                    [
                        'slug' => 'returns-sales',
                        'title' => 'Retur Penjualan',
                        'route_name' => 'inventory.returns.sales',
                        'route_url' => '/inventory/returns/sales',
                        'function' => 'Mencatat pengembalian obat dari pasien (misalnya pasien salah beli, obat cacat pabrik, atau transaksi dibatalkan). Mengembalikan obat ke stok fisik dan memotong omzet penjualan secara riil.',
                        'cards' => [
                            [
                                'name' => 'Total Nilai Retur',
                                'value_desc' => 'Akumulasi rupiah obat yang dikembalikan dan dana yang dikembalikan ke pelanggan.',
                                'source' => 'Dihitung dari tabel sales_returns.total_amount.',
                                'formula' => '∑ (Qty Retur × Harga Jual Satuan)',
                                'notes' => 'Nilai ini otomatis mengurangi Penjualan Kotor pada Laporan Penjualan dan Laporan Laba Rugi.',
                            ],
                        ],
                        'steps' => [
                            'Klik tombol <strong>Tambah Retur Penjualan</strong>.',
                            'Cari nomor faktur/struk penjualan pasien terkait.',
                            'Pilih obat yang dikembalikan beserta jumlahnya dan cantumkan alasan retur.',
                            'Pilih apakah barang yang diretur masih layak masuk stok rak atau rusak (jika rusak, tidak menambah stok aktif).',
                            'Simpan retur. Kas kasir otomatis berkurang sesuai nilai uang yang dikembalikan ke pasien.',
                        ],
                        'keywords' => 'retur penjualan kembalikan obat batal transaksi pasien refund',
                    ],
                    [
                        'slug' => 'returns-purchase',
                        'title' => 'Retur Pembelian',
                        'route_name' => 'inventory.returns.purchase',
                        'route_url' => '/inventory/returns/purchase',
                        'function' => 'Mencatat pengembalian obat dari apotek kepada distributor PBF (misalnya obat mendekati masa kadaluarsa/ED, kemasan penyok/rusak saat datang, atau salah kirim dari PBF). Otomatis mengurangi stok gudang dan memotong sisa hutang apotek ke PBF.',
                        'cards' => [
                            [
                                'name' => 'Total Retur Pembelian',
                                'value_desc' => 'Nominal tagihan faktur yang dipotongkan kepada pihak distributor PBF.',
                                'source' => 'Dihitung dari tabel purchase_returns.',
                                'formula' => '∑ (Qty Retur × Harga Beli Modal HNA)',
                                'notes' => 'Otomatis mengurangi sisa tagihan hutang pada Laporan Hutang & Piutang.',
                            ],
                        ],
                        'steps' => [
                            'Klik <strong>Buat Retur Pembelian</strong>.',
                            'Pilih supplier PBF dan faktur penerimaan asalnya.',
                            'Pilih obat dan batch yang akan dikembalikan beserta alasannya.',
                            'Cetak formulir Retur Pembelian untuk diserahkan ke sales/ekspedisi PBF bersama fisik obat.',
                        ],
                        'keywords' => 'retur pembelian return supplier pbf klaim obat ed rusak potong hutang',
                    ],
                ],
            ],

            // ==================== 5. DATA MASTER ====================
            [
                'id' => 'master',
                'title' => 'Data Master',
                'color' => 'indigo',
                'description' => 'Database induk obat & alkes, pengali konversi multi-satuan, kategori produk, supplier PBF, dan data pelanggan.',
                'sub_menus' => [
                    [
                        'slug' => 'master-products',
                        'title' => 'Data Obat / Produk',
                        'route_name' => 'products.index',
                        'route_url' => '/products',
                        'function' => 'Database induk seluruh obat, produk farmasi, dan alat kesehatan yang dijual di apotek. Mengatur kode barcode, nama resmi obat, kategori, satuan terkecil, harga beli modal (HNA), harga jual (HJA), batas stok minimum, dan lokasi rak obat.',
                        'cards' => [
                            [
                                'name' => 'Total Obat Aktif',
                                'value_desc' => 'Jumlah keseluruhan varian obat terdaftar.',
                                'source' => 'Tabel products.',
                                'formula' => 'COUNT(products.id)',
                                'notes' => 'Pastikan setiap obat memiliki satuan dasar yang jelas (misal: Tablet atau Strip).',
                            ],
                        ],
                        'steps' => [
                            'Klik tombol <strong>Tambah Obat</strong> (atau Import Excel jika memasukkan database obat secara massal).',
                            'Ketik Nama Obat, sistem otomatis membuat kode Barcode (atau scan barcode resmi pabrik pada kemasan obat).',
                            'Pilih Kategori Produk (misal: Obat Bebas, Obat Keras, Generik, Suplemen, Alkes).',
                            'Pilih Satuan Dasar (misal: Tablet, Strip, Botol).',
                            'Masukkan Harga Beli Modal (HNA) dan tetapkan Harga Jual Toko (HJA).',
                            'Tentukan <strong>Batas Minimum Stok</strong> (misal 10), agar sistem dapat memberi peringatan otomatis saat stok menipis.',
                            'Klik <strong>Simpan</strong>.',
                        ],
                        'keywords' => 'master data obat produk katalog harga beli hna harga jual hja barcode sku',
                    ],
                    [
                        'slug' => 'master-units',
                        'title' => 'Konversi Satuan Obat',
                        'route_name' => 'master.product-units',
                        'route_url' => '/master/product-units',
                        'function' => 'Mengatur rasio konversi multi-satuan obat agar kasir dapat melayani penjualan baik eceran maupun grosir secara otomatis tanpa merusak stok (misal: 1 Box = 10 Strip, 1 Strip = 10 Tablet).',
                        'cards' => [
                            [
                                'name' => 'Faktor Pengali Satuan',
                                'value_desc' => 'Rasio perbandingan satuan besar terhadap satuan dasar terkecil.',
                                'source' => 'Tabel product_units.',
                                'formula' => 'Satuan Besar = n × Satuan Dasar',
                                'notes' => 'Jika pasien membeli 1 Box, sistem otomatis memotong 10 Strip dari total persediaan.',
                            ],
                        ],
                        'steps' => [
                            'Buka menu <strong>Konversi Satuan</strong>.',
                            'Cari obat yang ingin diatur multi-satuannya.',
                            'Tambahkan satuan turunan: tentukan Satuan Dasar (misal Tablet) dan Satuan Besar (misal Box).',
                            'Isi angka rasio pengalinya (misal: isi 100 jika 1 Box berisi 100 Tablet).',
                            'Tentukan harga jual khusus untuk satuan besar tersebut.',
                        ],
                        'keywords' => 'konversi satuan multi unit box strip tablet blister botol rasio pengali',
                    ],
                    [
                        'slug' => 'master-categories',
                        'title' => 'Kategori Produk',
                        'route_name' => 'master.categories',
                        'route_url' => '/master/categories',
                        'function' => 'Mengelompokkan produk farmasi ke dalam klasifikasi yang teratur (misalnya: Obat Bebas, Obat Keras / Ethical, Sirup Anak, Antibiotik, Salep & Kulit, Suplemen & Vitamin, Alat Kesehatan).',
                        'cards' => [
                            [
                                'name' => 'Total Kategori Terdaftar',
                                'value_desc' => 'Jumlah kelompok kategori aktif.',
                                'source' => 'Tabel categories.',
                                'formula' => 'COUNT(categories.id)',
                                'notes' => 'Mempermudah filter pencarian di kasir dan pembuatan laporan penjualan per kategori.',
                            ],
                        ],
                        'steps' => [
                            'Buka menu Kategori Produk, klik <strong>Tambah Kategori</strong>.',
                            'Masukkan Nama Kategori (misal: "Obat Bebas Terbatas (W)").',
                            'Klik Simpan. Kategori langsung tersedia di form input obat.',
                        ],
                        'keywords' => 'kategori produk kelompok obat golongan obat bebas keras generik alkes',
                    ],
                    [
                        'slug' => 'master-suppliers',
                        'title' => 'Supplier (Distributor PBF)',
                        'route_name' => 'master.suppliers',
                        'route_url' => '/master/suppliers',
                        'function' => 'Mengelola buku alamat rekanan apotek: data distributor Pedagang Besar Farmasi (PBF) beserta nomor izin, sales, alamat, dan syarat tempo pembayaran.',
                        'cards' => [
                            [
                                'name' => 'Batas Tempo Pembayaran (Term Days)',
                                'value_desc' => 'Jumlah hari tenggat pembayaran yang disepakati dengan distributor (misal: 30 hari).',
                                'source' => 'Tabel suppliers.payment_terms.',
                                'formula' => 'Tanggal Faktur + Term Days = Tanggal Jatuh Tempo',
                                'notes' => 'Menjadi acuan otomatis penentuan tanggal jatuh tempo saat input penerimaan pesanan.',
                            ],
                        ],
                        'steps' => [
                            'Klik <strong>Tambah Supplier</strong>.',
                            'Isi Nama PBF (misal: PT. Kimia Farma Trading), nomor telepon, nama sales, alamat, dan jangka waktu tempo standar (misal 30 hari).',
                            'Klik Simpan.',
                        ],
                        'keywords' => 'supplier distributor pbf rekanan tempo pbf farmasi',
                    ],
                    [
                        'slug' => 'master-customers',
                        'title' => 'Pelanggan (Pasien / Instansi)',
                        'route_name' => 'master.customers',
                        'route_url' => '/master/customers',
                        'function' => 'Mencatat data pasien tetap, klinik rekanan, atau instansi yang memiliki fasilitas pembayaran tempo/kredit di apotek.',
                        'cards' => [
                            [
                                'name' => 'Total Pelanggan Terdaftar',
                                'value_desc' => 'Jumlah pasien/instansi yang tercatat di database.',
                                'source' => 'Tabel customers.',
                                'formula' => 'COUNT(customers.id)',
                                'notes' => 'Nama pelanggan ini dapat dipilih kasir saat metode bayar Piutang.',
                            ],
                        ],
                        'steps' => [
                            'Klik <strong>Tambah Pelanggan</strong>.',
                            'Isi Nama Lengkap, Nomor WhatsApp/Telepon, dan Alamat.',
                            'Klik Simpan.',
                        ],
                        'keywords' => 'pelanggan pasien member customer klinik instansi rekanan tempo piutang',
                    ],
                ],
            ],

            // ==================== 6. LAPORAN KEUANGAN ====================
            [
                'id' => 'finance_reports',
                'title' => 'Laporan Keuangan',
                'color' => 'teal',
                'description' => 'Laporan akuntansi resmi: Laba Rugi, Umur Hutang & Piutang (Aging), Neraca Standar, Arus Kas, dan Pajak PPN.',
                'sub_menus' => [
                    [
                        'slug' => 'reports-profit-loss',
                        'title' => 'Laporan Laba Rugi (Profit & Loss)',
                        'route_name' => 'finance.profit-loss',
                        'route_url' => '/finance/profit-loss',
                        'function' => 'Laporan kinerja keuangan paling vital yang merangkum hasil usaha apotek selama periode tertentu. Menunjukkan secara transparan berapa pendapatan kotor, potongan retur, modal obat yang terjual (HPP), beban operasional rutin, beban pajak, dan keuntungan bersih akhir.',
                        'cards' => [
                            [
                                'name' => 'Card 1: Pendapatan Riil',
                                'value_desc' => 'Omzet bersih penjualan obat setelah dikurangi seluruh retur barang pasien.',
                                'source' => 'DPP Penjualan Kotor (tabel sales) dikurangi Total Retur (tabel sales_returns).',
                                'formula' => 'DPP Penjualan - Retur Penjualan',
                                'notes' => 'Sinkron 100% dengan total Penjualan Bersih di Laporan Penjualan Kasir.',
                            ],
                            [
                                'name' => 'Card 2: Total HPP (Modal)',
                                'value_desc' => 'Total biaya pokok modal pembelian atas seluruh obat yang terjual pada periode laporan.',
                                'source' => 'Akumulasi perkalian qty obat terjual di sale_items dengan harga modal beli batch di batches.buy_price.',
                                'formula' => '∑ (Qty Terjual × Harga Beli Modal Batch)',
                                'notes' => 'Berdasarkan batch fisik nyata obat yang dikeluarkan kasir saat bertransaksi.',
                            ],
                            [
                                'name' => 'Card 3: Laba Kotor (Gross Profit)',
                                'value_desc' => 'Keuntungan murni dari selisih omzet penjualan dengan harga modal obat.',
                                'source' => 'Pendapatan Riil dikurangi HPP.',
                                'formula' => 'Pendapatan Riil - HPP',
                                'notes' => 'Gross Profit Margin = (Laba Kotor ÷ Pendapatan Riil) × 100%.',
                            ],
                            [
                                'name' => 'Card 4: Total Beban (Operasional & Lainnya)',
                                'value_desc' => 'Seluruh biaya pengeluaran operasional apotek (Gaji staf, Listrik PLN, Air PDAM, Sewa Ruko, Plastik/Etiket Obat, Perawatan AC/Komputer, ATK).',
                                'source' => 'Diambil dari transaksi bertipe expense pada tabel expenses (menu Pengeluaran & Pemasukan).',
                                'formula' => '∑ Pengeluaran Operasional Rutin',
                                'notes' => 'Beban bunga dan beban pajak dipisahkan ke baris pos tersendiri.',
                            ],
                            [
                                'name' => 'Card 5: Laba Bersih (Net Profit)',
                                'value_desc' => 'Keuntungan bersih final apotek yang menjadi hak pemilik setelah dipotong beban usaha dan pajak.',
                                'source' => 'Laba Sebelum Pajak dikurangi Beban Pajak (PPh Final UMKM 0.5% atau manual).',
                                'formula' => 'EBT - Beban Pajak Penghasilan',
                                'notes' => 'Net Profit Margin = (Laba Bersih ÷ Pendapatan Riil) × 100%.',
                            ],
                        ],
                        'steps' => [
                            'Pilih filter periode tanggal di bagian atas: Bulan Ini, Bulan Lalu, Tahun Ini, atau Custom Tanggal.',
                            'Pantau baris <strong>Pendapatan Riil</strong> dan <strong>HPP</strong> untuk melihat margin keuntungan kotor apotek.',
                            'Periksa kartu <strong>Total Beban</strong>: jika nilainya masih Rp 0, pastikan staf administrasi sudah menginput pengeluaran di menu <em>Pengeluaran & Pemasukan</em>.',
                            'Klik tombol <strong>Excel</strong> atau <strong>PDF</strong> di pojok kanan atas untuk mengunduh laporan berformat resmi.',
                            'Klik tombol <strong>ℹ️ Rumus Perhitungan</strong> untuk melihat rincian rumus matematis laporan ini.',
                        ],
                        'keywords' => 'laba rugi profit loss pnl revenue hpp cogs beban operasional laba kotor laba bersih',
                    ],
                    [
                        'slug' => 'reports-aging',
                        'title' => 'Laporan Hutang & Piutang (Aging Report)',
                        'route_name' => 'finance.aging-report',
                        'route_url' => '/finance/aging-report',
                        'function' => 'Mengawasi umur jatuh tempo tagihan apotek ke distributor PBF (Hutang Dagang / AP) serta tagihan apotek ke pelanggan tempo/instansi (Piutang Usaha / AR). Dilengkapi fitur pelunasan langsung dan melihat detail surat jalan/transaksi.',
                        'cards' => [
                            [
                                'name' => 'Card 0 - 7 Hari',
                                'value_desc' => 'Total sisa tagihan faktur yang tanggal jatuh temponya berada dalam 1 minggu ke depan.',
                                'source' => 'Selisih hari antara tanggal jatuh tempo faktur dengan hari ini berada di rentang 0 s/d 7 hari.',
                                'formula' => '∑ Sisa Faktur (0 ≤ Jatuh Tempo ≤ 7 Hari)',
                                'notes' => 'Prioritas pelunasan jangka pendek agar pasokan obat dari PBF tidak dibekukan (hold).',
                            ],
                            [
                                'name' => 'Card 8 - 15 Hari',
                                'value_desc' => 'Total tagihan faktur yang akan jatuh tempo dalam minggu kedua.',
                                'source' => 'Selisih hari jatuh tempo 8 s/d 15 hari.',
                                'formula' => '∑ Sisa Faktur (8 - 15 Hari)',
                                'notes' => 'Membantu merencanakan alokasi anggaran kas mingguan.',
                            ],
                            [
                                'name' => 'Card 16 - 30 Hari',
                                'value_desc' => 'Total tagihan faktur tempo 2 s/d 4 minggu ke depan.',
                                'source' => 'Selisih hari jatuh tempo 16 s/d 30 hari.',
                                'formula' => '∑ Sisa Faktur (16 - 30 Hari)',
                                'notes' => 'Umur tagihan standar untuk distributor PBF dengan term 30 hari.',
                            ],
                            [
                                'name' => 'Card 31 - 45 Hari',
                                'value_desc' => 'Total tagihan faktur yang telah melewati jatuh tempo lebih dari 1 bulan.',
                                'source' => 'Selisih hari jatuh tempo 31 s/d 45 hari.',
                                'formula' => '∑ Sisa Faktur Menunggak (31 - 45 Hari)',
                                'notes' => 'Status waspada: tagihan sudah menunggak dan perlu segera diselesaikan.',
                            ],
                            [
                                'name' => 'Card 45+ Hari (Kritis / Macet)',
                                'value_desc' => 'Total tagihan faktur yang menunggak lebih dari 45 hari.',
                                'source' => 'Selisih hari jatuh tempo > 45 hari.',
                                'formula' => '∑ Sisa Faktur Macet (> 45 Hari)',
                                'notes' => 'Berbahaya jika berupa piutang pelanggan (indikasi piutang tak tertagih) atau hutang PBF (resiko somasi/blokir).',
                            ],
                        ],
                        'steps' => [
                            'Pilih tab: <strong>Hutang Dagang (AP)</strong> untuk melihat tagihan supplier, atau <strong>Piutang Usaha (AR)</strong> untuk melihat tagihan pasien.',
                            'Pilih tab kategori umur (0-7, 8-15, 16-30, 31-45, 45+) untuk melihat daftar faktur yang masuk ke kelompok risiko tersebut.',
                            'Klik ikon mata / dokumen pada baris faktur untuk melihat <strong>Detail Transaksi / Surat Jalan</strong> (nama barang yang dibeli/dijual).',
                            'Untuk melakukan pelunasan/cicilan: klik tombol <strong>Bayar</strong> di dalam modal detail, masukkan nominal bayar, pilih sumber rekening bank/kas, tanggal, dan simpan.',
                        ],
                        'keywords' => 'aging report umur hutang piutang ap ar tempo jatuh tempo tagihan supplier pelunasan',
                    ],
                    [
                        'slug' => 'reports-balance-sheet',
                        'title' => 'Neraca Standar (Balance Sheet)',
                        'route_name' => 'finance.balance-sheet',
                        'route_url' => '/finance/balance-sheet',
                        'function' => 'Menyajikan posisi kekayaan finansial apotek pada tanggal tertentu secara komprehensif. Menunjukkan keseimbangan antara seluruh kekayaan (Aset) dengan kewajiban (Hutang) dan modal pemilik (Ekuitas).',
                        'cards' => [
                            [
                                'name' => 'Card Total Aset (Aktiva)',
                                'value_desc' => 'Nilai keseluruhan kekayaan apotek: Kas laci + Saldo Bank + Piutang Pasien + Nilai Stok Obat di Rak/Gudang + Aset Tetap Toko.',
                                'source' => 'Akumulasi seluruh akun berkategori Aset (Aset Lancar dan Aset Tetap).',
                                'formula' => 'Aset Lancar + Aset Tetap',
                                'notes' => 'Mencerminkan nilai kapitalisasi apotek.',
                            ],
                            [
                                'name' => 'Card Total Liabilitas (Kewajiban)',
                                'value_desc' => 'Seluruh kewajiban finansial apotek kepada pihak luar: Hutang Dagang ke PBF + Hutang Pajak + Beban yang masih harus dibayar.',
                                'source' => 'Akumulasi akun berkategori Kewajiban/Liabilitas.',
                                'formula' => 'Hutang Jangka Pendek + Hutang Jangka Panjang',
                                'notes' => 'Menunjukkan beban tanggungan hutang apotek.',
                            ],
                            [
                                'name' => 'Card Total Ekuitas (Modal)',
                                'value_desc' => 'Modal bersih milik pemilik apotek.',
                                'source' => 'Modal Awal Disetor + Laba Ditahan + Laba Bersih Tahun Berjalan.',
                                'formula' => 'Modal Disetor + Saldo Laba',
                                'notes' => 'Wajib seimbang secara mutlak: Aset = Liabilitas + Ekuitas.',
                            ],
                        ],
                        'steps' => [
                            'Tentukan per tanggal berapa posisi neraca ingin dilihat.',
                            'Klik tombol <strong>Generate</strong>.',
                            'Periksa indikator <strong>Balance Check</strong> di bagian atas: sistem akan memberi tanda hijau jika Neraca seimbang (Aset = Liabilitas + Ekuitas), dan memberi peringatan merah jika ada ketidakseimbangan.',
                        ],
                        'keywords' => 'neraca balance sheet aktiva pasiva aset kewajiban liabilitas ekuitas modal',
                    ],
                    [
                        'slug' => 'reports-cash-flow',
                        'title' => 'Laporan Arus Kas (Cash Flow)',
                        'route_name' => 'finance.cash-flow',
                        'route_url' => '/finance/cash-flow',
                        'function' => 'Memantau pergerakan fisik uang tunai dan saldo bank yang benar-benar masuk dan keluar (Aktivitas Operasi, Investasi, dan Pendanaan). Memastikan apotek tidak mengalami krisis likuiditas.',
                        'cards' => [
                            [
                                'name' => 'Total Kas Masuk Riil',
                                'value_desc' => 'Seluruh uang yang benar-benar diterima laci kasir dan rekening bank apotek.',
                                'source' => 'Penjualan tunai kasir + pelunasan piutang pasien + pendapatan lain-lain.',
                                'formula' => '∑ Seluruh Penerimaan Kas Riil',
                                'notes' => 'Berbeda dengan Laba: penjualan kredit belum dihitung kas masuk sampai uang fisiknya diterima.',
                            ],
                            [
                                'name' => 'Total Kas Keluar Riil',
                                'value_desc' => 'Seluruh uang yang dibayarkan keluar dari dompet kasir dan rekening bank.',
                                'source' => 'Pembelian obat tunai + pembayaran hutang ke PBF + biaya operasional kas.',
                                'formula' => '∑ Seluruh Pengeluaran Kas Riil',
                                'notes' => 'Mencerminkan beban pengeluaran riil.',
                            ],
                            [
                                'name' => 'Arus Kas Bersih (Net Cash Flow)',
                                'value_desc' => 'Selisih surplus atau defisit kas selama periode laporan.',
                                'source' => 'Total Kas Masuk Riil dikurangi Total Kas Keluar Riil.',
                                'formula' => 'Kas Masuk - Kas Keluar',
                                'notes' => 'Jika positif = surplus kas; jika negatif = defisit kas.',
                            ],
                            [
                                'name' => 'Saldo Akhir Kas',
                                'value_desc' => 'Total sisa uang tunai dan rekening bank pada akhir periode.',
                                'source' => 'Saldo Awal Kas + Net Cash Flow.',
                                'formula' => 'Saldo Kas Awal + Arus Kas Bersih',
                                'notes' => 'Wajib sama dengan total kas fisik di laci dan mutasi rekening bank.',
                            ],
                        ],
                        'steps' => [
                            'Pilih rentang tanggal yang ingin dianalisis arus kasnya.',
                            'Bandingkan Kas Masuk Operasi dengan Kas Keluar Operasi.',
                            'Pastikan Net Cash Flow bernilai positif agar operasional apotek berjalan lancar dan aman.',
                        ],
                        'keywords' => 'arus kas cash flow uang tunai saldo bank kas masuk keluar likuiditas',
                    ],
                    [
                        'slug' => 'reports-ppn',
                        'title' => 'Laporan PPN (Pajak)',
                        'route_name' => 'finance.ppn-report',
                        'route_url' => '/finance/ppn-report',
                        'function' => 'Merekapitulasi Pajak Pertambahan Nilai (PPN 11%) baik PPN Keluaran yang dipungut saat kasir menjual obat, maupun PPN Masukan yang dibayar saat apotek membeli obat dari PBF. Menghitung status Kurang Bayar atau Lebih Bayar untuk pelaporan SPT Masa PPN.',
                        'cards' => [
                            [
                                'name' => 'Card PPN Keluaran',
                                'value_desc' => 'Total nominal PPN 11% yang dipungut dari seluruh transaksi penjualan kasir.',
                                'source' => 'Dihitung dari kolom sales.tax pada tabel penjualan.',
                                'formula' => '∑ PPN Penjualan Obat ke Pasien',
                                'notes' => 'Merupakan titipan pajak dari pembeli yang harus dipertanggungjawabkan ke kas negara.',
                            ],
                            [
                                'name' => 'Card PPN Masukan',
                                'value_desc' => 'Total nominal PPN 11% yang tercantum pada faktur pembelian distributor PBF.',
                                'source' => 'Dihitung dari kolom goods_receipts.tax_amount pada tabel penerimaan pesanan.',
                                'formula' => '∑ PPN Faktur Pembelian dari Distributor PBF',
                                'notes' => 'Dapat digunakan sebagai pengurang (kredit pajak) atas PPN Keluaran.',
                            ],
                            [
                                'name' => 'Card Kurang / Lebih Bayar',
                                'value_desc' => 'Kewajiban neto setor PPN apotek ke kantor pajak.',
                                'source' => 'Selisih antara PPN Keluaran dikurangi PPN Masukan.',
                                'formula' => 'PPN Keluaran - PPN Masukan',
                                'notes' => 'Jika positif = Kurang Bayar (wajib disetor via kode e-Billing); jika negatif = Lebih Bayar (dikompensasikan ke masa berikutnya).',
                            ],
                        ],
                        'steps' => [
                            'Pilih bulan dan tahun masa pajak yang ingin direkap.',
                            'Periksa rincian faktur penjualan di tabel PPN Keluaran dan faktur PBF di tabel PPN Masukan.',
                            'Lihat status pada card <strong>Kurang / Lebih Bayar</strong> untuk keperluan pembuatan SPT Masa PPN.',
                        ],
                        'keywords' => 'ppn pajak pertambahan nilai keluaran masukan spt masa e-billing pajak penjualan pembelian',
                    ],
                    [
                        'slug' => 'finance-summary-rep',
                        'title' => 'Ringkasan Keuangan (Financial Summary)',
                        'route_name' => 'finance.summary',
                        'route_url' => '/finance/summary',
                        'function' => 'Dashboard ringkasan kondisi likuiditas keuangan apotek: memantau total saldo kas di laci dan rekening-rekening bank operasional, total kewajiban hutang yang belum lunas, serta posisi ketahanan kas bersih.',
                        'cards' => [
                            [
                                'name' => 'Card Total Saldo (Kas & Bank)',
                                'value_desc' => 'Total uang nyata yang tersedia di laci kasir dan seluruh rekening bank apotek.',
                                'source' => 'Akumulasi saldo akun kas dan bank aktif pada tabel accounts.',
                                'formula' => '∑ Saldo Akun Kas + ∑ Saldo Akun Bank',
                                'notes' => 'Menunjukkan ketersediaan dana likuid siap pakai.',
                            ],
                            [
                                'name' => 'Card Total Kewajiban (Hutang)',
                                'value_desc' => 'Total seluruh tagihan hutang apotek yang belum lunas ke distributor dan pihak ketiga.',
                                'source' => 'Akumulasi sisa saldo hutang dagang pada tabel accounts dan goods_receipts.',
                                'formula' => '∑ Tagihan Hutang Belum Lunas',
                                'notes' => 'Menunjukkan beban kewajiban yang harus dibayar.',
                            ],
                            [
                                'name' => 'Card Posisi Kas Bersih',
                                'value_desc' => 'Likuiditas dana bersih apotek jika seluruh hutang dilunasi seketika.',
                                'source' => 'Total Saldo Kas & Bank dikurangi Total Kewajiban.',
                                'formula' => 'Total Saldo Kas & Bank - Total Kewajiban',
                                'notes' => 'Jika bernilai positif (hijau) = kondisi keuangan likuid dan sehat; jika negatif (merah) = apotek dalam posisi rentan likuiditas.',
                            ],
                        ],
                        'steps' => [
                            'Pilih rentang tanggal pemeriksaan.',
                            'Lihat rincian saldo per rekening bank pada tabel di bawah card ringkasan.',
                            'Gunakan card Posisi Kas Bersih untuk mengambil keputusan apakah apotek aman melakukan pembelian obat dalam jumlah besar atau ekspansi.',
                        ],
                        'keywords' => 'ringkasan keuangan saldo kas bank kewajiban hutang likuiditas posisi kas bersih',
                    ],
                ],
            ],

            // ==================== 7. LAPORAN OPERASIONAL ====================
            [
                'id' => 'op_reports',
                'title' => 'Laporan Operasional',
                'color' => 'amber',
                'description' => 'Rekapitulasi penjualan kasir, analisis ketersediaan stok fisik & nilai, kartu stok kronologis, dan analisis margin produk.',
                'sub_menus' => [
                    [
                        'slug' => 'reports-sales-op',
                        'title' => 'Laporan Penjualan',
                        'route_name' => 'reports.sales',
                        'route_url' => '/reports/sales',
                        'function' => 'Merekapitulasi seluruh riwayat transaksi penjualan kasir secara rinci: nomor struk/invois, tanggal & jam belanja, nama kasir bertugas, metode pembayaran, rincian obat yang dibeli, serta rekonsiliasi antara uang kotor kasir dengan omzet riil setelah retur dan pajak.',
                        'cards' => [
                            [
                                'name' => 'Card 1: Total Transaksi',
                                'value_desc' => 'Jumlah keseluruhan nota/struk kasir yang berstatus lunas.',
                                'source' => 'Dihitung dari Sale::where(\'status\', \'completed\')->count() pada rentang tanggal terpilih.',
                                'formula' => 'COUNT(sales.id)',
                                'notes' => 'Menunjukkan intensitas kunjungan pasien yang melakukan transaksi di apotek.',
                            ],
                            [
                                'name' => 'Card 2: Penjualan Kotor',
                                'value_desc' => 'Akumulasi seluruh uang yang dibayarkan pelanggan di kasir sebelum dipotong retur.',
                                'source' => 'Dihitung dari Sale::where(\'status\', \'completed\')->sum(\'grand_total\').',
                                'formula' => '∑ Grand Total Struk Kasir',
                                'notes' => 'Mencakup harga jual obat, PPN Keluaran 11%, dan selisih pembulatan kasir.',
                            ],
                            [
                                'name' => 'Card 3: Retur Penjualan',
                                'value_desc' => 'Total nominal obat yang dikembalikan oleh pasien pada periode ini.',
                                'source' => 'Dihitung dari SalesReturn::sum(\'total_amount\').',
                                'formula' => '∑ Transaksi Retur Pasien',
                                'notes' => 'Mengurangi total pendapatan kotor kasir.',
                            ],
                            [
                                'name' => 'Card 4: Penjualan Bersih (Riil)',
                                'value_desc' => 'Pendapatan riil efektif yang diperoleh apotek.',
                                'source' => 'Subtotal Bersih (DPP) dikurangi Total Retur Penjualan.',
                                'formula' => 'DPP Penjualan - Retur Penjualan',
                                'notes' => 'Angka inilah yang menjadi Pendapatan Riil di Laporan Laba Rugi.',
                            ],
                            [
                                'name' => 'Card Box: Rincian Rekonsiliasi Pendapatan',
                                'value_desc' => 'Rincian pembeda uang fisik di laci kasir menuju pendapatan riil toko.',
                                'source' => 'Penjualan Kotor dikurangi PPN (Sale::sum(\'tax\')) dan Pembulatan (Sale::sum(\'rounding\')) menghasilkan DPP, lalu dikurangi Retur menghasilkan Pendapatan Riil.',
                                'formula' => 'Penjualan Kotor - PPN - Pembulatan = DPP; DPP - Retur = Pendapatan Riil',
                                'notes' => 'Tampil di bawah tabel penjualan untuk mempermudah pengecekan uang kas kasir.',
                            ],
                        ],
                        'steps' => [
                            'Pilih rentang tanggal transaksi (Dari Tanggal s/d Sampai Tanggal).',
                            'Bisa memfilter berdasarkan metode bayar (Tunai, QRIS, Transfer, atau Piutang) atau mencari nomor invois spesifik.',
                            'Klik ikon mata pada baris tabel untuk melihat <strong>Rincian Struk Penjualan</strong> (nama obat, batch, qty, diskon).',
                            'Klik ikon printer untuk mencetak ulang struk belanja jika pasien meminta salinan struk.',
                            'Gunakan tombol <strong>Export Excel / PDF</strong> untuk pelaporan ke pemilik apotek.',
                        ],
                        'keywords' => 'laporan penjualan omset kotor bersih retur dpp kasir transaksi rekapitulasi',
                    ],
                    [
                        'slug' => 'reports-stock-op',
                        'title' => 'Laporan Stok & Nilai Persediaan',
                        'route_name' => 'reports.stock',
                        'route_url' => '/reports/stock',
                        'function' => 'Menyajikan posisi kuantitas stok fisik seluruh obat per batch pabrik beserta tanggal kadaluarsanya dan total saldo nilai rupiah modal obat yang tersimpan di apotek.',
                        'cards' => [
                            [
                                'name' => 'Kolom Saldo Nilai Stok',
                                'value_desc' => 'Nilai aset persediaan rupiah per baris batch obat.',
                                'source' => 'Dihitung dari perkalian sisa stok fisik batch dengan harga beli modal batch.',
                                'formula' => 'Stok Fisik Batch × Harga Beli Modal Batch (HNA)',
                                'notes' => 'Total keseluruhan kolom ini menjadi akun Persediaan Barang Dagang di Neraca.',
                            ],
                        ],
                        'steps' => [
                            'Gunakan filter Kategori atau pencarian nama obat untuk memfilter obat tertentu.',
                            'Gunakan filter Tanggal Kadaluarsa untuk melihat obat yang akan expired dalam rentang waktu tertentu.',
                            'Cetak Laporan Stok fisik untuk digunakan petugas saat pelaksanaan audit berkala di rak apotek.',
                        ],
                        'keywords' => 'laporan stok nilai persediaan aset obat batch kadaluarsa saldo gudang',
                    ],
                    [
                        'slug' => 'reports-margin-op',
                        'title' => 'Laporan Margin Produk',
                        'route_name' => 'reports.product-margin',
                        'route_url' => '/reports/product-margin',
                        'function' => 'Menganalisis tingkat profitabilitas setiap obat yang dijual di apotek. Membantu pemilik apotek mengidentifikasi produk yang memberikan margin laba tertinggi (produk andalan) dan produk yang marginnya terlalu tipis atau bahkan merugi.',
                        'cards' => [
                            [
                                'name' => 'Card Total Produk',
                                'value_desc' => 'Jumlah keseluruhan varian produk teranalisis yang memiliki data harga.',
                                'source' => 'Dihitung dari total produk dengan harga jual aktif.',
                                'formula' => 'COUNT(products.id)',
                                'notes' => 'Menjadi dasar perbandingan statistik.',
                            ],
                            [
                                'name' => 'Card Margin Positif',
                                'value_desc' => 'Jumlah obat yang memiliki harga jual lebih tinggi daripada harga modal beli.',
                                'source' => 'Produk di mana Harga Jual > Harga Beli Modal.',
                                'formula' => 'Harga Jual > Harga Beli',
                                'notes' => 'Produk yang menghasilkan laba nyata bagi apotek.',
                            ],
                            [
                                'name' => 'Card Total Keuntungan',
                                'value_desc' => 'Akumulasi potensi nilai keuntungan rupiah atas produk yang dijual.',
                                'source' => 'Penjumlahan selisih nominal (Harga Jual - Harga Beli).',
                                'formula' => '∑ (Harga Jual - Harga Modal HPP)',
                                'notes' => 'Menunjukkan total margin nominal.',
                            ],
                            [
                                'name' => 'Card Rata-rata Margin',
                                'value_desc' => 'Persentase profitabilitas rata-rata katalog obat apotek.',
                                'source' => 'Rata-rata persentase margin keuntungan: ((Harga Jual - Harga Modal) ÷ Harga Jual) × 100%.',
                                'formula' => 'Rata-rata Margin Keuntungan (%)',
                                'notes' => 'Standar margin apotek yang sehat biasanya berada di kisaran 20% - 35%.',
                            ],
                        ],
                        'steps' => [
                            'Pilih mode: <strong>Margin Potensial</strong> (berdasarkan harga katalog master) atau <strong>Margin Realisasi</strong> (berdasarkan obat yang benar-benar terjual pada rentang tanggal).',
                            'Gunakan filter Margin: bisa memfilter produk Margin Tinggi (>30%), Margin Rendah (<10%), atau Margin Negatif (rugi).',
                            'Gunakan informasi ini untuk meninjau kembali harga jual obat yang marginnya terlalu kecil.',
                        ],
                        'keywords' => 'margin laba keuntungan profitabilitas markup harga jual harga beli produk laporan margin',
                    ],
                    [
                        'slug' => 'reports-history-op',
                        'title' => 'Riwayat Transaksi Produk (Kartu Stok)',
                        'route_name' => 'reports.transaction-history',
                        'route_url' => '/reports/transaction-history',
                        'function' => 'Kartu stok elektronik yang mencatat jejak mutasi keluar-masuk setiap obat secara kronologis dari detik ke detik: asal transaksi (penjualan kasir, penerimaan barang dari PBF, retur, atau penyesuaian opname), kuantitas masuk/keluar, dan sisa saldo stok akhir.',
                        'cards' => [
                            [
                                'name' => 'Kolom Saldo Berjalan (Running Balance)',
                                'value_desc' => 'Sisa stok obat tepat setelah mutasi transaksi tersebut terjadi.',
                                'source' => 'Tabel stock_movements.',
                                'formula' => 'Saldo Sebelumnya + Masuk - Keluar',
                                'notes' => 'Sangat efektif untuk melacak penyebab selisih stok obat atau audit investigasi.',
                            ],
                        ],
                        'steps' => [
                            'Pilih nama obat yang ingin diaudit kartu stoknya.',
                            'Pilih rentang tanggal pemeriksaan.',
                            'Perhatikan setiap baris transaksi: apakah mutasi berupa Penjualan Kasir (Keluar), Pembelian PBF (Masuk), atau Penyesuaian Opname.',
                        ],
                        'keywords' => 'kartu stok riwayat mutasi audit keluar masuk running balance stok movement',
                    ],
                ],
            ],

            // ==================== 8. KEUANGAN & ADMINISTRASI ====================
            [
                'id' => 'finance_admin',
                'title' => 'Keuangan & Administrasi',
                'color' => 'cyan',
                'description' => 'Pencatatan kas operasional (pengeluaran rutin), saldo awal neraca, jurnal umum, dan bagan akun (COA).',
                'sub_menus' => [
                    [
                        'slug' => 'finance-expenses-admin',
                        'title' => 'Pengeluaran & Pemasukan',
                        'route_name' => 'finance.expenses',
                        'route_url' => '/finance/expenses',
                        'function' => 'Tempat mencatat segala biaya operasional rutin apotek di luar pembelian obat (seperti Gaji Staf, Listrik PLN, Air PDAM, Sewa Tempat, Bahan Habis Pakai/ATK/Plastik Klip Obat, Pemeliharaan AC) serta pemasukan non-penjualan (seperti bunga tabungan bank atau klaim cashback).',
                        'cards' => [
                            [
                                'name' => 'Card Total Pengeluaran',
                                'value_desc' => 'Total rupiah kas keluar untuk membiayai operasional apotek pada periode filter.',
                                'source' => 'Dihitung dari Expense::where(\'type\', \'expense\')->sum(\'amount\').',
                                'formula' => '∑ Transaksi Beban Pengeluaran',
                                'notes' => 'Angka inilah yang langsung terakumulasi pada kartu Total Beban di Laporan Laba Rugi.',
                            ],
                            [
                                'name' => 'Card Total Pemasukan',
                                'value_desc' => 'Total rupiah pendapatan lain-lain di luar transaksi penjualan kasir.',
                                'source' => 'Dihitung dari Expense::where(\'type\', \'income\')->sum(\'amount\').',
                                'formula' => '∑ Transaksi Pemasukan Non-Kasir',
                                'notes' => 'Masuk ke pos Pendapatan Lain-lain pada Laporan Laba Rugi.',
                            ],
                            [
                                'name' => 'Card Net (Surplus / Defisit)',
                                'value_desc' => 'Selisih kas operasional non-penjualan.',
                                'source' => 'Total Pemasukan dikurangi Total Pengeluaran.',
                                'formula' => 'Total Pemasukan - Total Pengeluaran',
                                'notes' => 'Jika pengeluaran lebih besar, maka bernilai defisit.',
                            ],
                        ],
                        'steps' => [
                            'Klik tombol <strong>Tambah Transaksi</strong>.',
                            'Pilih tanggal transaksi.',
                            'Pilih Tipe: <strong>Pengeluaran</strong> (untuk membayar biaya) atau <strong>Pemasukan Lain-lain</strong>.',
                            'Ketik Keterangan (misal: "Bayar Tagihan Listrik PLN Bulan Oktober").',
                            'Masukkan Nominal Rupiah.',
                            'Pilih <strong>Kategori</strong> biaya yang sesuai (misal: Beban Utilitas, Beban Gaji, Perlengkapan/ATK).',
                            'Pilih <strong>Sumber Dana</strong> rekening/kas yang digunakan untuk otomatis membentuk jurnal akuntansi.',
                            'Klik <strong>Simpan</strong>.',
                        ],
                        'keywords' => 'pengeluaran pemasukan biaya beban operasional gaji listrik sewa atk kas',
                    ],
                    [
                        'slug' => 'finance-opening-balance-admin',
                        'title' => 'Neraca Saldo Awal',
                        'route_name' => 'finance.opening-balance',
                        'route_url' => '/finance/opening-balance',
                        'function' => 'Digunakan saat pertama kali apotek mulai menggunakan aplikasi untuk menginput saldo awal uang kas laci, saldo rekening bank, taksiran nilai persediaan awal obat, serta modal disetor pemilik agar neraca seimbang dan pembukuan tidak minus.',
                        'cards' => [
                            [
                                'name' => 'Indikator Keseimbangan (Balance)',
                                'value_desc' => 'Status apakah Total Debit (Aset) sama persis dengan Total Kredit (Liabilitas + Modal).',
                                'source' => 'Pengecekan selisih nilai antara total debet dan kredit saldo awal.',
                                'formula' => 'Total Debet - Total Kredit = 0',
                                'notes' => 'Jika selisih belum 0, sistem tidak mengizinkan penguncian saldo awal.',
                            ],
                        ],
                        'steps' => [
                            'Masukkan uang kas laci awal di akun Kas.',
                            'Masukkan saldo buku tabungan terakhir di masing-masing akun Bank.',
                            'Masukkan taksiran total modal stok obat yang sudah dimiliki di akun Persediaan Barang.',
                            'Masukkan angka yang sama di akun Modal Awal Pemilik pada bagian Ekuitas.',
                            'Pastikan selisih menunjukkan angka Rp 0 (Seimbang).',
                            'Klik <strong>Konfirmasi & Kunci Saldo Awal</strong>.',
                        ],
                        'keywords' => 'saldo awal opening balance neraca awal modal awal persediaan kas awal kunci',
                    ],
                    [
                        'slug' => 'finance-accounts-admin',
                        'title' => 'Daftar Akun (COA)',
                        'route_name' => 'accounting.accounts.index',
                        'route_url' => '/accounting/accounts',
                        'function' => 'Bagan akun akuntansi standar apotek yang menjadi fondasi seluruh pembukuan otomatis (Kas, Bank, Piutang, Persediaan Obat, Aset Tetap, Hutang Dagang, Modal, Pendapatan Penjualan, dan Beban Operasional).',
                        'cards' => [
                            [
                                'name' => 'Kolom Saldo Berjalan Akun',
                                'value_desc' => 'Posisi saldo debit atau kredit pada akun bersangkutan saat ini.',
                                'source' => 'Akumulasi mutasi debit dan kredit dari jurnal umum.',
                                'formula' => 'Saldo Awal + Debet - Kredit (atau sebaliknya sesuai jenis akun)',
                                'notes' => 'Akun Kas & Bank bertambah di Debet; akun Hutang & Pendapatan bertambah di Kredit.',
                            ],
                        ],
                        'steps' => [
                            'Bagan akun standar sudah disiapkan otomatis oleh sistem.',
                            'Jika apotek membuka rekening bank operasional baru, klik <strong>Tambah Akun</strong>, pilih kategori Kas & Bank, masukkan nama bank dan nomor rekening.',
                        ],
                        'keywords' => 'daftar akun coa chart of accounts kas bank debet kredit buku besar akuntansi',
                    ],
                ],
            ],

            // ==================== 9. PENGATURAN SISTEM ====================
            [
                'id' => 'settings',
                'title' => 'Pengaturan Sistem',
                'color' => 'slate',
                'description' => 'Konfigurasi identitas toko, ukuran printer thermal kasir, manajemen user & hak akses staf, dan backup database.',
                'sub_menus' => [
                    [
                        'slug' => 'settings-store-main',
                        'title' => 'Identitas Toko & Kasir',
                        'route_name' => 'settings.store',
                        'route_url' => '/settings/store',
                        'function' => 'Mengatur identitas resmi apotek (Nama Apotek, Nomor SIA, Nomor SIPA Apoteker Pengelola, Alamat Lengkap, Nomor Kontak, Logo Toko, Tarif PPN Default) serta pengaturan teknis kasir seperti ukuran kertas struk printer thermal (58mm atau 80mm).',
                        'cards' => [
                            [
                                'name' => 'Preview Kop Struk Kasir',
                                'value_desc' => 'Tampilan simulasi cetak nama apotek, nomor telepon, dan izin resmi di bagian atas struk belanja pelanggan.',
                                'source' => 'Data identitas toko yang disimpan pada tabel settings.',
                                'formula' => 'Pengaturan Toko',
                                'notes' => 'Wajib diisi dengan benar karena nomor SIA & SIPA merupakan persyaratan wajib regulasi kefarmasian.',
                            ],
                        ],
                        'steps' => [
                            'Isi Nama Resmi Apotek, Alamat Lengkap, dan Nomor Telepon toko.',
                            'Cantumkan Nomor Izin Apotek (SIA) dan Nomor SIPA Apoteker Penanggung Jawab.',
                            'Pilih tarif PPN default (misal 11%) atau 0% jika apotek non-PKP.',
                            'Pada tab Pengaturan Kasir: pilih ukuran kertas printer struk kasir (58mm atau 80mm) dan isi pesan footer struk.',
                            'Klik <strong>Simpan Perubahan</strong>.',
                        ],
                        'keywords' => 'pengaturan toko identitas apotek sia sipa logo kasir printer thermal ukuran kertas',
                    ],
                    [
                        'slug' => 'settings-users-main',
                        'title' => 'Kelola User & Jabatan (Hak Akses)',
                        'route_name' => 'admin.users.index',
                        'route_url' => '/admin/users',
                        'function' => 'Mengelola akun staf apotek (Kasir, Staf Gudang, Apoteker, Admin, Super Admin) dan mengatur hak akses wewenang (permission) agar kasir tidak dapat melihat laporan keuangan rahasia atau mengubah harga obat sembarangan.',
                        'cards' => [
                            [
                                'name' => 'Status Role & Permission',
                                'value_desc' => 'Daftar wewenang yang diizinkan untuk setiap peran pengguna di apotek.',
                                'source' => 'Tabel roles dan permissions (Spatie Permission).',
                                'formula' => 'Role RBAC (Role-Based Access Control)',
                                'notes' => 'Menjaga keamanan data keuangan dan operasional apotek.',
                            ],
                        ],
                        'steps' => [
                            'Untuk menambah akun staf baru: klik <strong>Tambah User</strong>, isi nama, email, password, dan pilih Role (misal: Kasir).',
                            'Untuk mengubah wewenang jabatan: buka menu <strong>Kelola Jabatan</strong>, centang izin yang diperbolehkan untuk jabatan tersebut, lalu simpan.',
                        ],
                        'keywords' => 'kelola user staf karyawan hak akses jabatan role permission wewenang kasir admin',
                    ],
                    [
                        'slug' => 'settings-backups-main',
                        'title' => 'Backup Database & Log Aktivitas',
                        'route_name' => 'admin.backups',
                        'route_url' => '/admin/backups',
                        'function' => 'Fitur pengamanan data apotek: membuat dan mengunduh cadangan (backup) database secara berkala agar aman dari resiko kerusakan komputer kasir, serta memantau riwayat audit log aktivitas untuk melacak siapa yang menghapus, mengubah, atau membuat data.',
                        'cards' => [
                            [
                                'name' => 'Status Backup Terakhir',
                                'value_desc' => 'Ukuran file cadangan SQL/Zip dan tanggal pembuatan backup terbaru.',
                                'source' => 'Sistem file storage backups.',
                                'formula' => 'Database Dump (SQL / ZIP)',
                                'notes' => 'Disarankan mengunduh file backup ke flashdisk atau cloud minimal sekali seminggu.',
                            ],
                        ],
                        'steps' => [
                            'Buka menu <strong>Backup Data</strong>, klik tombol <strong>Buat Backup Sekarang</strong>.',
                            'Tunggu hingga proses selesai, lalu klik ikon unduh untuk mendownload file cadangan ke komputer atau flashdisk eksternal.',
                            'Buka menu <strong>Riwayat Aktivitas</strong> jika ingin melihat catatan jejak audit tindakan pengguna.',
                        ],
                        'keywords' => 'backup database cadangan data restore log audit trail jejak aktivitas keamanan',
                    ],
                ],
            ],
        ];
    }
}
