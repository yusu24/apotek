<?php

namespace App\Livewire\Settings;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
class FormulaGuide extends Component
{
    #[Url(as: 'tab')]
    public $activeCategory = 'all';

    #[Url(as: 'q')]
    public $search = '';

    public function mount()
    {
        // Valid categories: all, finance, pos, pricing, aging, inventory, cashflow
        $validCategories = ['all', 'finance', 'pos', 'pricing', 'aging', 'inventory', 'cashflow'];
        if (!in_array($this->activeCategory, $validCategories)) {
            $this->activeCategory = 'all';
        }
    }

    public function setCategory($category)
    {
        $this->activeCategory = $category;
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->activeCategory = 'all';
    }

    public function render()
    {
        $allFormulas = $this->getFormulasData();

        $filteredFormulas = array_filter($allFormulas, function ($item) {
            // Category filter
            if ($this->activeCategory !== 'all' && $item['category_key'] !== $this->activeCategory) {
                return false;
            }

            // Search filter
            if (!empty($this->search)) {
                $q = strtolower(trim($this->search));
                $searchable = strtolower(
                    $item['title'] . ' ' .
                    $item['formula'] . ' ' .
                    $item['description'] . ' ' .
                    $item['menu_name'] . ' ' .
                    ($item['keywords'] ?? '')
                );
                return str_contains($searchable, $q);
            }

            return true;
        });

        return view('livewire.settings.formula-guide', [
            'formulas' => $filteredFormulas,
            'categories' => $this->getCategoriesList(),
            'totalCount' => count($allFormulas),
            'filteredCount' => count($filteredFormulas),
        ]);
    }

    private function getCategoriesList()
    {
        return [
            ['key' => 'all', 'label' => 'Semua Rumus', 'icon' => 'globe', 'color' => 'gray'],
            ['key' => 'finance', 'label' => 'Laba Rugi & Keuangan', 'icon' => 'chart-bar', 'color' => 'blue'],
            ['key' => 'pos', 'label' => 'Kasir & POS', 'icon' => 'shopping-cart', 'color' => 'emerald'],
            ['key' => 'pricing', 'label' => 'Harga Jual & Margin', 'icon' => 'tag', 'color' => 'indigo'],
            ['key' => 'aging', 'label' => 'Hutang & Piutang', 'icon' => 'clock', 'color' => 'amber'],
            ['key' => 'inventory', 'label' => 'Stok & Inventori', 'icon' => 'archive-box', 'color' => 'purple'],
            ['key' => 'cashflow', 'label' => 'Arus Kas & Neraca', 'icon' => 'scale', 'color' => 'teal'],
        ];
    }

    private function getFormulasData()
    {
        return [
            // ==================== 1. LABA RUGI & KEUANGAN ====================
            [
                'id' => 'net-revenue',
                'category_key' => 'finance',
                'category_label' => 'Laba Rugi & Keuangan',
                'title' => 'Pendapatan Bersih (Net Revenue / Penjualan Riil)',
                'menu_name' => 'Laporan Laba Rugi',
                'menu_route' => 'finance.profit-loss',
                'menu_url' => '/finance/profit-loss',
                'formula' => 'Pendapatan Bersih = DPP Penjualan Kotor - Retur Penjualan',
                'formula_math' => '$$\text{Net Revenue} = \text{Gross Revenue (DPP)} - \text{Total Retur Penjualan}$$',
                'description' => 'Nilai omzet riil apotek yang diakui setelah dikurangi seluruh pengembalian obat/retur transaksi penjualan dari pasien pada periode yang dipilih.',
                'variables' => [
                    ['name' => 'DPP Penjualan Kotor', 'desc' => 'Total Dasar Pengenaan Pajak (DPP) penjualan sebelum retur dari tabel sales.'],
                    ['name' => 'Retur Penjualan', 'desc' => 'Total nilai nominal retur obat/barang yang disetujui dari tabel sales_returns.'],
                ],
                'example' => [
                    'scenario' => 'Apotek mencatat total penjualan Rp 50.000.000 pada bulan ini, dan terdapat retur obat rusak/batal dari pasien sebesar Rp 500.000.',
                    'calc' => 'Rp 50.000.000 - Rp 500.000',
                    'result' => 'Rp 49.500.000',
                ],
                'note' => 'Pendapatan Bersih ini disinkronkan langsung dengan angka yang tertera pada Laporan Penjualan Kasir.',
                'keywords' => 'pendapatan omzet bersih gross revenue dpp retur penjualan laba rugi',
            ],
            [
                'id' => 'cogs',
                'category_key' => 'finance',
                'category_label' => 'Laba Rugi & Keuangan',
                'title' => 'Harga Pokok Penjualan (HPP / COGS)',
                'menu_name' => 'Laporan Laba Rugi & Margin Produk',
                'menu_route' => 'finance.profit-loss',
                'menu_url' => '/finance/profit-loss',
                'formula' => 'HPP = ∑ (Jumlah Terjual × Harga Beli Modal Batch Obat)',
                'formula_math' => '$$\text{HPP (COGS)} = \sum (\text{Qty Terjual} \times \text{Harga Beli Batch})$$',
                'description' => 'Akumulasi total nilai modal (biaya pengadaan/beli) atas obat dan produk yang benar-benar berhasil terjual kepada pelanggan selama periode laporan.',
                'variables' => [
                    ['name' => 'Qty Terjual', 'desc' => 'Banyaknya kuantitas unit obat yang terjual dari tabel sale_items.'],
                    ['name' => 'Harga Beli Batch', 'desc' => 'Harga modal netto pembelian per unit yang melekat pada batch obat (tabel batches.buy_price) atau HPP omset historis.'],
                ],
                'example' => [
                    'scenario' => 'Terjual 100 strip Amoxicillin dengan harga beli batch Rp 8.000/strip dan 50 botol Sirup Paracetamol dengan harga beli Rp 12.000/botol.',
                    'calc' => '(100 × Rp 8.000) + (50 × Rp 12.000) = Rp 800.000 + Rp 600.000',
                    'result' => 'Rp 1.400.000',
                ],
                'note' => 'Apotek menggunakan pelacakan batch spesifik sehingga HPP obat dihitung akurat berdasarkan batch fisik obat yang dikeluarkan saat kasir bertransaksi.',
                'keywords' => 'hpp cogs harga pokok penjualan modal beli batch buy_price laba rugi',
            ],
            [
                'id' => 'gross-profit',
                'category_key' => 'finance',
                'category_label' => 'Laba Rugi & Keuangan',
                'title' => 'Laba Kotor (Gross Profit)',
                'menu_name' => 'Laporan Laba Rugi',
                'menu_route' => 'finance.profit-loss',
                'menu_url' => '/finance/profit-loss',
                'formula' => 'Laba Kotor = Pendapatan Bersih (Net Revenue) - HPP (COGS)',
                'formula_math' => '$$\text{Laba Kotor} = \text{Pendapatan Bersih} - \text{HPP}$$',
                'description' => 'Keuntungan murni dari selisih nilai penjualan produk dengan harga modal pengadaannya, sebelum dipotong beban biaya operasional apotek.',
                'variables' => [
                    ['name' => 'Pendapatan Bersih', 'desc' => 'Omzet riil setelah dikurangi retur penjualan.'],
                    ['name' => 'HPP (COGS)', 'desc' => 'Total biaya pokok modal barang yang terjual.'],
                ],
                'example' => [
                    'scenario' => 'Pendapatan Bersih bulan ini Rp 49.500.000 dan total HPP obat yang terjual adalah Rp 34.650.000.',
                    'calc' => 'Rp 49.500.000 - Rp 34.650.000',
                    'result' => 'Rp 14.850.000 (Margin Kotor 30%)',
                ],
                'note' => 'Gross Profit Margin dihitung dengan: (Laba Kotor ÷ Pendapatan Bersih) × 100%.',
                'keywords' => 'laba kotor gross profit margin untung kotor laba rugi',
            ],
            [
                'id' => 'operating-expenses',
                'category_key' => 'finance',
                'category_label' => 'Laba Rugi & Keuangan',
                'title' => 'Total Beban Operasional Usaha',
                'menu_name' => 'Pengeluaran & Pemasukan',
                'menu_route' => 'finance.expenses',
                'menu_url' => '/finance/expenses',
                'formula' => 'Beban Operasional = ∑ Seluruh Pengeluaran Rutin (di luar Bunga & Pajak)',
                'formula_math' => '$$\text{Beban Operasional} = \sum \text{Beban Gaji, Listrik, Sewa, ATK, Kebersihan, dll}$$',
                'description' => 'Seluruh biaya rutin untuk menjalankan kegiatan operasional apotek sehari-hari yang dicatat melalui menu Pengeluaran.',
                'variables' => [
                    ['name' => 'Beban Operasional', 'desc' => 'Semua baris di tabel expenses yang bertipe expense, kecuali kategori yang mengandung kata "bunga", "lain-lain", atau "pajak".'],
                ],
                'example' => [
                    'scenario' => 'Bulan ini dicatat: Gaji Karyawan Rp 7.000.000, Listrik PLN & Air Rp 1.200.000, Plastik/Etiket Obat Rp 300.000, dan Sewa Rp 2.000.000.',
                    'calc' => 'Rp 7.000.000 + Rp 1.200.000 + Rp 300.000 + Rp 2.000.000',
                    'result' => 'Rp 10.500.000',
                ],
                'note' => 'Beban bunga pinjaman dan beban pajak dipisahkan otomatis ke pos keuangan tersendiri sesuai standar PSAK.',
                'keywords' => 'beban operasional expenses pengeluaran gaji listrik sewa atk operasional laba rugi',
            ],
            [
                'id' => 'operating-profit',
                'category_key' => 'finance',
                'category_label' => 'Laba Rugi & Keuangan',
                'title' => 'Laba Usaha / Operasional (EBIT)',
                'menu_name' => 'Laporan Laba Rugi',
                'menu_route' => 'finance.profit-loss',
                'menu_url' => '/finance/profit-loss',
                'formula' => 'Laba Usaha (EBIT) = Laba Kotor - Total Beban Operasional',
                'formula_math' => '$$\text{Laba Usaha (EBIT)} = \text{Laba Kotor} - \text{Beban Operasional}$$',
                'description' => 'Pendapatan usaha operasional apotek sebelum memperhitungkan pendapatan bunga bank, biaya keuangan lain-lain, serta kewajiban pajak.',
                'variables' => [
                    ['name' => 'Laba Kotor', 'desc' => 'Pendapatan Bersih dikurangi HPP.'],
                    ['name' => 'Beban Operasional', 'desc' => 'Total biaya operasional apotek periode tersebut.'],
                ],
                'example' => [
                    'scenario' => 'Laba Kotor Rp 14.850.000 dan Total Beban Operasional Rp 10.500.000.',
                    'calc' => 'Rp 14.850.000 - Rp 10.500.000',
                    'result' => 'Rp 4.350.000',
                ],
                'note' => 'EBIT (Earnings Before Interest and Tax) menunjukkan profitabilitas murni dari kegiatan inti operasional apotek.',
                'keywords' => 'laba usaha ebit operating profit laba operasional laba rugi',
            ],
            [
                'id' => 'tax-expenses',
                'category_key' => 'finance',
                'category_label' => 'Laba Rugi & Keuangan',
                'title' => 'Beban Pajak Penghasilan (PPh Final UMKM / Manual)',
                'menu_name' => 'Laporan Laba Rugi & Pengaturan Toko',
                'menu_route' => 'finance.profit-loss',
                'menu_url' => '/finance/profit-loss',
                'formula' => 'PPh Final UMKM = Pendapatan Kotor × 0.5% (Tarif PP 55/2022)',
                'formula_math' => '$$\text{Beban Pajak UMKM} = \text{Gross Revenue} \times 0.5\%$$',
                'description' => 'Kewajiban pajak penghasilan apotek. Sistem mendukung 2 skema: PPh Final UMKM 0,5% otomatis dihitung dari omzet bruto, atau Skema Manual dari inputan menu Pengeluaran.',
                'variables' => [
                    ['name' => 'Skema UMKM Final', 'desc' => 'Dihitung: round(revenue * tax_rate_umkm / 100), default tarif 0.5%.'],
                    ['name' => 'Skema Manual', 'desc' => 'Total transaksi di tabel expenses dengan kategori "pajak", "pph", atau "tax".'],
                ],
                'example' => [
                    'scenario' => 'Omzet Apotek bulan ini Rp 50.000.000 dan menggunakan skema PPh Final UMKM 0.5%.',
                    'calc' => 'Rp 50.000.000 × 0.5%',
                    'result' => 'Rp 250.000',
                ],
                'note' => 'Pilihan skema pajak dapat dikonfigurasi melalui menu Pengaturan Toko > Pengaturan Keuangan & Pajak.',
                'keywords' => 'pajak pph final umkm 0.5 persen tax pph21 pph23 laba rugi',
            ],
            [
                'id' => 'net-profit',
                'category_key' => 'finance',
                'category_label' => 'Laba Rugi & Keuangan',
                'title' => 'Laba Bersih Setelah Pajak (Net Profit)',
                'menu_name' => 'Laporan Laba Rugi',
                'menu_route' => 'finance.profit-loss',
                'menu_url' => '/finance/profit-loss',
                'formula' => 'Laba Bersih = Laba Sebelum Pajak (EBT) - Beban Pajak Penghasilan',
                'formula_math' => '$$\text{Laba Bersih (Net Profit)} = \text{EBT} - \text{Beban Pajak}$$',
                'description' => 'Keuntungan final apotek yang menjadi hak pemilik/badan usaha setelah melunasi seluruh HPP, beban operasional, beban bunga, dan beban pajak.',
                'variables' => [
                    ['name' => 'Laba Sebelum Pajak (EBT)', 'desc' => 'Laba Usaha (EBIT) + (Pendapatan Lain-lain - Beban Lain-lain).'],
                    ['name' => 'Beban Pajak', 'desc' => 'Beban PPh Final UMKM atau Beban Pajak Manual.'],
                ],
                'example' => [
                    'scenario' => 'Laba Usaha Rp 4.350.000, Pendapatan bunga bank Rp 50.000, Biaya admin bank Rp 20.000, Pajak PPh UMKM Rp 250.000.',
                    'calc' => '(Rp 4.350.000 + Rp 30.000) - Rp 250.000 = Rp 4.380.000 - Rp 250.000',
                    'result' => 'Rp 4.130.000 (Net Margin 8.34%)',
                ],
                'note' => 'Net Profit Margin dihitung dengan: (Laba Bersih ÷ Pendapatan Bersih) × 100%.',
                'keywords' => 'laba bersih net profit bottom line keuntungan bersih laba rugi',
            ],

            // ==================== 2. KASIR & PENJUALAN (POS) ====================
            [
                'id' => 'pos-item-subtotal',
                'category_key' => 'pos',
                'category_label' => 'Kasir & POS',
                'title' => 'Subtotal Item Penjualan Kasir',
                'menu_name' => 'Kasir (POS)',
                'menu_route' => 'pos.cashier',
                'menu_url' => '/cashier',
                'formula' => 'Subtotal Item = (Jumlah Qty × Harga Satuan) - Diskon Item',
                'formula_math' => '$$\text{Subtotal Item} = (\text{Qty} \times \text{Harga Satuan}) - \text{Diskon Item}$$',
                'description' => 'Nilai kotor setiap baris obat yang di-scan atau diinput kasir pada laci belanja transaksi kasir.',
                'variables' => [
                    ['name' => 'Qty', 'desc' => 'Jumlah kuantitas satuan obat yang dibeli.'],
                    ['name' => 'Harga Satuan', 'desc' => 'Harga jual obat per satuan (strip, botol, tablet, box).'],
                    ['name' => 'Diskon Item', 'desc' => 'Diskon khusus yang diberikan pada produk tersebut (dalam % atau nominal).'],
                ],
                'example' => [
                    'scenario' => 'Pasien membeli 3 strip Vitamin C seharga Rp 15.000/strip dengan diskon promo Rp 1.000 per strip.',
                    'calc' => '(3 × Rp 15.000) - (3 × Rp 1.000) = Rp 45.000 - Rp 3.000',
                    'result' => 'Rp 42.000',
                ],
                'note' => 'Jika obat memiliki konversi satuan (misal beli 1 Box isi 10 Strip), harga satuan disesuaikan otomatis dengan faktor konversi.',
                'keywords' => 'pos kasir subtotal item diskon per item transaksi penjualan struk',
            ],
            [
                'id' => 'pos-ppn',
                'category_key' => 'pos',
                'category_label' => 'Kasir & POS',
                'title' => 'Dasar Pengenaan Pajak (DPP) & PPN Keluaran (11%)',
                'menu_name' => 'Kasir (POS) & Laporan PPN',
                'menu_route' => 'finance.ppn-report',
                'menu_url' => '/finance/ppn-report',
                'formula' => 'Harga Include PPN: DPP = Total ÷ 1.11 | PPN = Total - DPP',
                'formula_math' => '$$\text{DPP} = \frac{\text{Total}}{1 + \text{Tarif PPN}} \quad;\quad \text{PPN} = \text{DPP} \times 11\%$$',
                'description' => 'Penghitungan Pajak Pertambahan Nilai (PPN) atas transaksi penjualan produk kena pajak sesuai tarif PPN yang berlaku.',
                'variables' => [
                    ['name' => 'Metode Include PPN', 'desc' => 'Harga pada label sudah mencakup PPN 11%. DPP = Harga ÷ 1.11 dan PPN = DPP × 11%.'],
                    ['name' => 'Metode Exclude PPN', 'desc' => 'Harga belum termasuk PPN. PPN = Subtotal × 11% ditambahkan di akhir struk.'],
                ],
                'example' => [
                    'scenario' => 'Obat dijual dengan harga Rp 111.000 (sudah include PPN 11%).',
                    'calc' => 'DPP = Rp 111.000 ÷ 1.11 = Rp 100.000 | PPN = Rp 111.000 - Rp 100.000',
                    'result' => 'DPP = Rp 100.000, PPN Keluaran = Rp 11.000',
                ],
                'note' => 'Pengaturan apakah harga include/exclude PPN dan tarif persentase PPN diatur di menu Pengaturan Toko.',
                'keywords' => 'ppn 11 persen dpp dasar pengenaan pajak include exclude pajak keluaran kasir pos',
            ],
            [
                'id' => 'pos-tuslah-embalase',
                'category_key' => 'pos',
                'category_label' => 'Kasir & POS',
                'title' => 'Biaya Resep Dokter (Tuslah Racik & Embalase)',
                'menu_name' => 'Kasir (POS) - Resep',
                'menu_route' => 'pos.cashier',
                'menu_url' => '/cashier',
                'formula' => 'Total Resep = Harga Komponen Bahan Obat + Jasa Tuslah (R/) + Biaya Embalase',
                'formula_math' => '$$\text{Total Resep} = \sum \text{Bahan Obat} + \text{Jasa Tuslah} + \text{Embalase Kemasan}$$',
                'description' => 'Penghitungan biaya obat racikan resep dokter yang memperhitungkan harga bahan baku obat, jasa peracikan oleh apoteker (tuslah), dan wadah kemasan obat (embalase).',
                'variables' => [
                    ['name' => 'Bahan Baku', 'desc' => 'Total harga bahan obat serbuk, kapsul, atau cairan yang diracik.'],
                    ['name' => 'Tuslah (Jasa Resep)', 'desc' => 'Biaya jasa peracikan per resep dokter (R/).'],
                    ['name' => 'Embalase', 'desc' => 'Biaya perlengkapan wadah kemasan, seperti cangkang kapsul, kertas puyer, pot salep, botol sirup, atau plastik klip obat.'],
                ],
                'example' => [
                    'scenario' => 'Racikan puyer anak 10 bungkus: total bahan baku Rp 25.000, jasa tuslah Rp 5.000, biaya kertas puyer & klip Rp 2.000.',
                    'calc' => 'Rp 25.000 + Rp 5.000 + Rp 2.000',
                    'result' => 'Rp 32.000',
                ],
                'note' => 'Tuslah dan embalase memastikan apotek tidak mengalami kebocoran biaya atas bahan habis pakai dan jasa keahlian farmasi.',
                'keywords' => 'tuslah embalase racikan puyer resep dokter jasa racik kasir pos',
            ],
            [
                'id' => 'pos-grand-total',
                'category_key' => 'pos',
                'category_label' => 'Kasir & POS',
                'title' => 'Grand Total Pembayaran & Kembalian',
                'menu_name' => 'Kasir (POS)',
                'menu_route' => 'pos.cashier',
                'menu_url' => '/cashier',
                'formula' => 'Grand Total = (Subtotal - Diskon Nota) + PPN + Biaya Lain | Kembalian = Tunai - Grand Total',
                'formula_math' => '$$\text{Grand Total} = (\sum \text{Subtotal} - \text{Diskon}) + \text{PPN} \quad;\quad \text{Kembali} = \text{Bayar} - \text{Grand Total}$$',
                'description' => 'Kalkulasi akhir struk kasir yang wajib dibayarkan oleh pelanggan serta uang kembalian yang harus diserahkan kasir.',
                'variables' => [
                    ['name' => 'Diskon Nota', 'desc' => 'Diskon potongan langsung atas keseluruhan struk transaksi belanja.'],
                    ['name' => 'Kembalian', 'desc' => 'Selisih lebih antara uang fisik/pembayaran yang diterima dengan Grand Total belanja.'],
                ],
                'example' => [
                    'scenario' => 'Subtotal belanja Rp 105.000, diskon member Rp 5.000, Grand Total Rp 100.000. Pelanggan membayar uang tunai pecahan Rp 100.000 sebanyak 2 lembar (Rp 200.000).',
                    'calc' => 'Kembalian = Rp 200.000 - Rp 100.000',
                    'result' => 'Kembalian: Rp 100.000',
                ],
                'note' => 'Sistem mendukung multi-metode pembayaran: Tunai (Cash), Transfer Bank, QRIS, dan Piutang (Kredit/Tempo).',
                'keywords' => 'grand total kembalian diskon nota struk belanja kasir checkout bayar',
            ],

            // ==================== 3. HARGA JUAL & MARGIN OBAT ====================
            [
                'id' => 'margin-percentage',
                'category_key' => 'pricing',
                'category_label' => 'Harga Jual & Margin',
                'title' => 'Margin Keuntungan Produk (%)',
                'menu_name' => 'Laporan Margin Produk',
                'menu_route' => 'reports.product-margin',
                'menu_url' => '/reports/product-margin',
                'formula' => 'Margin (%) = [(Harga Jual - Harga Modal HPP) ÷ Harga Jual] × 100%',
                'formula_math' => '$$\text{Margin (\%)} = \left(\frac{\text{Harga Jual} - \text{Harga Beli}}{\text{Harga Jual}}\right) \times 100\%$$',
                'description' => 'Persentase keuntungan bersih yang dihasilkan dari setiap rupiah harga jual obat ke pelanggan.',
                'variables' => [
                    ['name' => 'Harga Jual', 'desc' => 'Harga jual obat saat ini ke konsumen (tabel products.sell_price).'],
                    ['name' => 'Harga Modal HPP', 'desc' => 'Harga beli modal rata-rata batch berjalan atau pembelian terakhir.'],
                ],
                'example' => [
                    'scenario' => 'Suplemen dibeli seharga Rp 80.000 dan dijual seharga Rp 100.000.',
                    'calc' => '[(Rp 100.000 - Rp 80.000) ÷ Rp 100.000] × 100% = [Rp 20.000 ÷ Rp 100.000] × 100%',
                    'result' => 'Margin = 20.00%',
                ],
                'note' => 'Margin dihitung dari basis Harga Jual, sedangkan Markup dihitung dari basis Harga Beli Modal.',
                'keywords' => 'margin keuntungan persen profit margin produk laporan margin obat',
            ],
            [
                'id' => 'markup-percentage',
                'category_key' => 'pricing',
                'category_label' => 'Harga Jual & Margin',
                'title' => 'Persentase Markup atas Modal (%)',
                'menu_name' => 'Data Obat & Master Produk',
                'menu_route' => 'products.index',
                'menu_url' => '/products',
                'formula' => 'Markup (%) = [(Harga Jual - Harga Modal HPP) ÷ Harga Modal HPP] × 100%',
                'formula_math' => '$$\text{Markup (\%)} = \left(\frac{\text{Harga Jual} - \text{Harga Beli}}{\text{Harga Beli}}\right) \times 100\%$$',
                'description' => 'Tingkat kenaikan harga yang ditambahkan di atas harga modal pembelian agar apotek mendapatkan laba target.',
                'variables' => [
                    ['name' => 'Rumus Harga Jual dari Target Markup', 'desc' => 'Harga Jual = Harga Beli Modal × (1 + Markup %)'],
                ],
                'example' => [
                    'scenario' => 'Obat sirup dibeli modal Rp 20.000. Apotek menetapkan target markup 25%.',
                    'calc' => 'Harga Jual = Rp 20.000 × (1 + 0.25) = Rp 20.000 × 1.25',
                    'result' => 'Harga Jual: Rp 25.000 (Laba Rp 5.000)',
                ],
                'note' => 'Contoh korelasi: Markup 25% setara dengan Margin 20%. Markup 100% setara dengan Margin 50%.',
                'keywords' => 'markup persentase kenaikan harga modal jual hna obat apotek',
            ],
            [
                'id' => 'hja-apotek',
                'category_key' => 'pricing',
                'category_label' => 'Harga Jual & Margin',
                'title' => 'Harga Jual Apotek (HJA) Resmi',
                'menu_name' => 'Edit Data Obat',
                'menu_route' => 'products.index',
                'menu_url' => '/products',
                'formula' => 'HJA = HNA (Harga Netto Apotek) × (1 + Margin Target) × (1 + PPN Masukan)',
                'formula_math' => '$$\text{HJA} = \text{HNA} \times (1 + \text{Markup}) \times (1 + \text{PPN } 11\%)$$',
                'description' => 'Standar baku penetapan harga jual obat di apotek dari distributor PBF (Pedagang Besar Farmasi) dengan memperhitungkan PPN faktur beli distributor.',
                'variables' => [
                    ['name' => 'HNA', 'desc' => 'Harga Netto Apotek sebelum PPN dari faktur PBF/distributor.'],
                    ['name' => 'PPN Masukan (11%)', 'desc' => 'Pajak Pertambahan Nilai 11% yang tercantum pada faktur pembelian distributor.'],
                ],
                'example' => [
                    'scenario' => 'HNA obat dari PBF adalah Rp 10.000, target markup apotek 20%, PPN faktur beli 11%.',
                    'calc' => 'Modal Setelah PPN = Rp 10.000 × 1.11 = Rp 11.100. Harga Jual = Rp 11.100 × 1.20',
                    'result' => 'HJA = Rp 13.320 (dibulatkan menjadi Rp 13.500)',
                ],
                'note' => 'Memastikan apotek tidak merugi akibat lupa memperhitungkan beban PPN 11% dari faktur pembelian PBF.',
                'keywords' => 'hja hna harga jual apotek netto pbf distributor farmasi kalkulasi',
            ],

            // ==================== 4. HUTANG & PIUTANG (AGING REPORT) ====================
            [
                'id' => 'aging-days',
                'category_key' => 'aging',
                'category_label' => 'Hutang & Piutang',
                'title' => 'Hari Keterlambatan / Umur Tagihan (Aging Days)',
                'menu_name' => 'Laporan Hutang & Piutang',
                'menu_route' => 'finance.aging-report',
                'menu_url' => '/finance/aging-report',
                'formula' => 'Hari Umur = Tanggal Hari Ini - Tanggal Jatuh Tempo Faktur',
                'formula_math' => '$$\text{Aging Days} = \text{Current Date} - \text{Due Date}$$',
                'description' => 'Jumlah hari selisih sejak faktur pembelian/penjualan tempo melewati batas waktu jatuh tempo (due date).',
                'variables' => [
                    ['name' => 'Hari ≤ 0', 'desc' => 'Faktur belum jatuh tempo (status lancar / aman).'],
                    ['name' => 'Hari > 0', 'desc' => 'Faktur sudah jatuh tempo dan telah menunggak selama n hari.'],
                ],
                'example' => [
                    'scenario' => 'Faktur jatuh tempo tanggal 10 Oktober 2026. Hari ini tanggal 25 Oktober 2026.',
                    'calc' => '25 Oktober 2026 - 10 Oktober 2026',
                    'result' => '15 Hari Terlambat (Masuk Kelompok 8-15 Hari)',
                ],
                'note' => 'Umur tagihan dikelompokkan ke dalam 5 keranjang usia risiko: 0-7 hari, 8-15 hari, 16-30 hari, 31-45 hari, dan 45+ hari.',
                'keywords' => 'aging umur piutang hutang due date jatuh tempo hari keterlambatan',
            ],
            [
                'id' => 'outstanding-balance',
                'category_key' => 'aging',
                'category_label' => 'Hutang & Piutang',
                'title' => 'Sisa Tagihan Hutang / Piutang (Outstanding Balance)',
                'menu_name' => 'Laporan Hutang & Piutang',
                'menu_route' => 'finance.aging-report',
                'menu_url' => '/finance/aging-report',
                'formula' => 'Sisa Tagihan = Total Nilai Faktur - Total Pembayaran Terverifikasi',
                'formula_math' => '$$\text{Sisa Tagihan} = \text{Total Tagihan Faktur} - \sum \text{Cicilan/Pembayaran}$$',
                'description' => 'Sisa nominal saldo hutang apotek ke distributor (AP) atau piutang pelanggan ke apotek (AR) yang belum terbayar lunas.',
                'variables' => [
                    ['name' => 'Hutang Dagang (AP)', 'desc' => 'Faktur penerimaan barang (Goods Receipt) dari supplier yang pembayarannya tempo.'],
                    ['name' => 'Piutang Pasien (AR)', 'desc' => 'Penjualan kasir ke pasien atau instansi rekanan yang pembayarannya bertempo.'],
                ],
                'example' => [
                    'scenario' => 'Faktur distributor obat Rp 10.000.000, apotek telah membayar uang muka/cicilan Rp 4.000.000.',
                    'calc' => 'Rp 10.000.000 - Rp 4.000.000',
                    'result' => 'Sisa Hutang: Rp 6.000.000',
                ],
                'note' => 'Begitu pembayaran dilunasi penuh, status faktur otomatis berubah menjadi LUNAS dan tidak lagi dihitung sebagai beban outstanding.',
                'keywords' => 'sisa tagihan outstanding balance hutang piutang ap ar pelunasan cicilan aging',
            ],

            // ==================== 5. STOK & INVENTORI ====================
            [
                'id' => 'inventory-valuation',
                'category_key' => 'inventory',
                'category_label' => 'Stok & Inventori',
                'title' => 'Nilai Total Aset Persediaan Obat (Inventory Valuation)',
                'menu_name' => 'Laporan Stok & Neraca',
                'menu_route' => 'reports.stock',
                'menu_url' => '/reports/stock',
                'formula' => 'Nilai Persediaan = ∑ (Sisa Stok Fisik Batch × Harga Beli Modal Batch)',
                'formula_math' => '$$\text{Nilai Persediaan} = \sum (\text{Stok Berjalan} \times \text{Harga Beli Batch})$$',
                'description' => 'Total nilai uang modal yang tertanam pada seluruh stok obat yang saat ini berada di rak dan gudang penyimpanan apotek.',
                'variables' => [
                    ['name' => 'Stok Berjalan', 'desc' => 'Kuantitas fisik sisa obat pada batch tersebut (tabel batches.stock_current).'],
                    ['name' => 'Harga Beli Batch', 'desc' => 'Harga modal pengadaan per unit dari batch obat bersangkutan.'],
                ],
                'example' => [
                    'scenario' => 'Batch A sisa 50 box @ Rp 40.000 (Rp 2.000.000) dan Batch B sisa 20 box @ Rp 42.000 (Rp 840.000).',
                    'calc' => '(50 × Rp 40.000) + (20 × Rp 42.000) = Rp 2.000.000 + Rp 840.000',
                    'result' => 'Rp 2.840.000',
                ],
                'note' => 'Angka ini tercermin pada baris Aset Lancar > Persediaan Barang Dagang di Laporan Neraca Keuangan.',
                'keywords' => 'nilai persediaan valuasi stok aset lancar modal barang gudang stok opname',
            ],
            [
                'id' => 'reorder-point',
                'category_key' => 'inventory',
                'category_label' => 'Stok & Inventori',
                'title' => 'Titik Pemesanan Kembali (Reorder Point / ROP)',
                'menu_name' => 'Gudang & Stok Obat',
                'menu_route' => 'inventory.index',
                'menu_url' => '/stock',
                'formula' => 'ROP = (Lead Time Pengiriman × Penjualan Rata-rata Harian) + Safety Stock',
                'formula_math' => '$$\text{ROP} = (\text{Lead Time} \times \text{Rata-rata Penjualan/Hari}) + \text{Safety Stock}$$',
                'description' => 'Batas minimum jumlah sisa stok obat di mana petugas gudang harus segera membuat Purchase Order (PO) baru ke supplier agar obat tidak sampai kehabisan stok (out of stock).',
                'variables' => [
                    ['name' => 'Lead Time', 'desc' => 'Waktu tunggu (dalam hari) sejak PO dikirim ke PBF hingga barang tiba di apotek.'],
                    ['name' => 'Rata-rata Penjualan', 'desc' => 'Rata-rata strip/botol obat yang terjual per hari.'],
                    ['name' => 'Safety Stock', 'desc' => 'Jumlah cadangan pengaman untuk mengantisipasi lonjakan permintaan pasien tak terduga.'],
                ],
                'example' => [
                    'scenario' => 'Lead time kirim distributor 3 hari. Rata-rata terjual 10 strip/hari. Cadangan pengaman 15 strip.',
                    'calc' => '(3 hari × 10 strip) + 15 strip = 30 + 15',
                    'result' => 'ROP = 45 Strip (Segera pesan ulang saat sisa stok ≤ 45 strip)',
                ],
                'note' => 'Mencegah terjadinya kehilangan omzet (lost sales) dan kekecewaan pasien akibat obat kosong.',
                'keywords' => 'rop reorder point titik pemesanan kembali safety stock lead time gudang stok',
            ],
            [
                'id' => 'stock-opname-variance',
                'category_key' => 'inventory',
                'category_label' => 'Stok & Inventori',
                'title' => 'Selisih Stok Opname (Stock Variance)',
                'menu_name' => 'Stok & Opname',
                'menu_route' => 'inventory.index',
                'menu_url' => '/stock',
                'formula' => 'Selisih Stok = Stok Fisik Dihitung Nyata - Stok Sistem (Komputer)',
                'formula_math' => '$$\text{Selisih Stok} = \text{Stok Fisik} - \text{Stok Sistem}$$',
                'description' => 'Selisih antara jumlah fisik obat yang ada di lemari apotek dengan jumlah yang tercatat di database komputer saat dilakukan audit opname.',
                'variables' => [
                    ['name' => 'Selisih Negatif (-)', 'desc' => 'Stok fisik lebih sedikit dari komputer (indikasi obat tercecer, expired dibuang, atau pencurian).'],
                    ['name' => 'Selisih Positif (+)', 'desc' => 'Stok fisik lebih banyak (indikasi kasir lupa scan atau salah identifikasi batch).'],
                ],
                'example' => [
                    'scenario' => 'Komputer mencatat sisa 20 botol sirup, namun hasil hitung fisik di rak hanya ada 18 botol.',
                    'calc' => '18 botol - 20 botol',
                    'result' => '-2 Botol (Selisih Kurang 2 Botol)',
                ],
                'note' => 'Penyesuaian stok otomatis mencatat riwayat log dan jurnal penyesuaian persediaan pada buku akuntansi.',
                'keywords' => 'selisih opname stock variance audit stok fisik penyesuaian stok barang',
            ],

            // ==================== 6. ARUS KAS & NERACA ====================
            [
                'id' => 'net-cash-flow',
                'category_key' => 'cashflow',
                'category_label' => 'Arus Kas & Neraca',
                'title' => 'Arus Kas Bersih (Net Cash Flow)',
                'menu_name' => 'Laporan Arus Kas',
                'menu_route' => 'finance.cash-flow',
                'menu_url' => '/finance/cash-flow',
                'formula' => 'Arus Kas Bersih = Total Kas Masuk Riil - Total Kas Keluar Riil',
                'formula_math' => '$$\text{Net Cash Flow} = \sum \text{Kas Masuk} - \sum \text{Kas Keluar}$$',
                'description' => 'Selisih bersih antara aliran uang tunai/bank yang benar-benar masuk dengan uang yang keluar dari laci kasir dan rekening operasional apotek.',
                'variables' => [
                    ['name' => 'Total Kas Masuk', 'desc' => 'Penerimaan penjualan kasir tunai, pelunasan piutang pasien, dan pendapatan lain-lain.'],
                    ['name' => 'Total Kas Keluar', 'desc' => 'Pembayaran tunai pembelian obat, pelunasan hutang ke PBF, dan pembayaran beban operasional kas.'],
                ],
                'example' => [
                    'scenario' => 'Bulan ini uang kas masuk Rp 60.000.000 dan pengeluaran kas keluar riil Rp 48.000.000.',
                    'calc' => 'Rp 60.000.000 - Rp 48.000.000',
                    'result' => 'Net Cash Flow: +Rp 12.000.000 (Surplus Kas)',
                ],
                'note' => 'Laba akuntansi berbeda dengan arus kas: Laba menghitung penjualan tempo sebagai pendapatan, sedangkan Arus Kas hanya mencatat saat uang nyata diterima.',
                'keywords' => 'arus kas cash flow kas masuk kas keluar uang tunai saldo bank neraca',
            ],
            [
                'id' => 'balance-sheet-equation',
                'category_key' => 'cashflow',
                'category_label' => 'Arus Kas & Neraca',
                'title' => 'Persamaan Dasar Neraca Keuangan (Balance Sheet)',
                'menu_name' => 'Neraca (Standar)',
                'menu_route' => 'finance.balance-sheet',
                'menu_url' => '/finance/balance-sheet',
                'formula' => 'Total Aset (Aktiva) = Total Kewajiban (Liabilitas) + Total Modal (Ekuitas)',
                'formula_math' => '$$\text{Aset (Aktiva)} = \text{Kewajiban (Hutang)} + \text{Ekuitas (Modal)}$$',
                'description' => 'Prinsip keseimbangan mutlak akuntansi di mana seluruh kekayaan fisik apotek harus sama persis dengan sumber pendanaannya (dari hutang pihak ketiga atau modal pemilik).',
                'variables' => [
                    ['name' => 'Aset (Aktiva)', 'desc' => 'Kas di laci + Saldo Rekening Bank + Piutang Pasien + Nilai Persediaan Obat + Aset Tetap Toko.'],
                    ['name' => 'Kewajiban (Liabilitas)', 'desc' => 'Hutang Dagang ke Distributor (AP) + Hutang Pajak + Beban yang masih harus dibayar.'],
                    ['name' => 'Ekuitas (Modal)', 'desc' => 'Modal Awal Disetor + Akumulasi Laba Ditahan + Laba Bersih Tahun Berjalan.'],
                ],
                'example' => [
                    'scenario' => 'Total Aset Apotek Rp 150.000.000. Hutang ke distributor Rp 40.000.000. Modal pemilik Rp 110.000.000.',
                    'calc' => 'Aset Rp 150.000.000 = Hutang Rp 40.000.000 + Modal Rp 110.000.000',
                    'result' => 'Seimbang (Balance = Rp 150.000.000)',
                ],
                'note' => 'Jika laporan neraca Anda selisih (tidak balance), periksa kembali saldo awal persediaan atau jurnal umum yang belum diposting.',
                'keywords' => 'neraca balance sheet aktiva pasiva aset liabilitas ekuitas modal akuntansi',
            ],
        ];
    }
}
