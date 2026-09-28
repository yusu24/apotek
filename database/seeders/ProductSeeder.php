<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Batch;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks to allow truncation
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Truncate tables to ensure clean slate
        Batch::truncate();
        Product::truncate();
        Category::truncate();
        Unit::truncate();
        
        // Enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Create Categories
        $categories = [
            'Obat Bebas' => 'blue',
            'Obat Keras' => 'red',
            'Obat Bebas Terbatas' => 'blue',
            'Vitamin & Suplemen' => 'green',
            'Alat Kesehatan' => 'gray', 
            'Ibu & Anak' => 'pink',
            'Herbal' => 'green',
        ];

        $categoryIds = [];
        foreach ($categories as $name => $color) {
            $cat = Category::create([
                'name' => $name,
                'slug' => Str::slug($name),
            ]);
            $categoryIds[$name] = $cat->id;
        }

        // 2. Create Units
        $unitNames = ['Strip', 'Botol', 'Box', 'Tube', 'Pcs', 'Sachet', 'Tablet', 'Roll', 'Pot'];
        $unitIds = [];
        foreach ($unitNames as $name) {
            $unit = Unit::create(['name' => $name]);
            $unitIds[$name] = $unit->id;
        }

        // 3. Define Indonesian Products (Comprehensive Pharmacy Catalog)
        $products = [
            // --- Obat Bebas ---
            ['name' => 'Paracetamol 500mg', 'category' => 'Obat Bebas', 'unit' => 'Strip', 'sell_price' => 4000, 'buy_price' => 2500, 'min_stock' => 20, 'description' => 'Obat penurun demam dan pereda nyeri umum.'],
            ['name' => 'Panadol Extra', 'category' => 'Obat Bebas', 'unit' => 'Strip', 'sell_price' => 15000, 'buy_price' => 12000, 'min_stock' => 15, 'description' => 'Meredakan sakit kepala dan sakit gigi.'],
            ['name' => 'Bodrex Sakit Kepala', 'category' => 'Obat Bebas', 'unit' => 'Strip', 'sell_price' => 5000, 'buy_price' => 3500, 'min_stock' => 20, 'description' => 'Meredakan sakit kepala dan demam.'],
            ['name' => 'Sanmol 500mg Tablet', 'category' => 'Obat Bebas', 'unit' => 'Strip', 'sell_price' => 5500, 'buy_price' => 3800, 'min_stock' => 15, 'description' => 'Analgesik dan antipiretik paracetamol murni.'],
            ['name' => 'Biogesic 500mg', 'category' => 'Obat Bebas', 'unit' => 'Strip', 'sell_price' => 7000, 'buy_price' => 5000, 'min_stock' => 10, 'description' => 'Obat pereda demam yang aman untuk lambung.'],
            ['name' => 'Promag Tablet Kunyah', 'category' => 'Obat Bebas', 'unit' => 'Strip', 'sell_price' => 9500, 'buy_price' => 7000, 'min_stock' => 20, 'description' => 'Mengatasi sakit maag, nyeri ulu hati dan kembung.'],
            ['name' => 'Mylanta Cair 50ml', 'category' => 'Obat Bebas', 'unit' => 'Botol', 'sell_price' => 18500, 'buy_price' => 14000, 'min_stock' => 10, 'description' => 'Antasida cair redakan nyeri lambung dengan cepat.'],
            ['name' => 'Polysilane Tablet', 'category' => 'Obat Bebas', 'unit' => 'Strip', 'sell_price' => 11000, 'buy_price' => 8500, 'min_stock' => 10, 'description' => 'Antasida kembung dan maag tablet kunyah.'],
            ['name' => 'Diapet Kapsul', 'category' => 'Obat Bebas', 'unit' => 'Strip', 'sell_price' => 4000, 'buy_price' => 2800, 'min_stock' => 20, 'description' => 'Mengurangi frekuensi buang air besar pada diare non-spesifik.'],
            ['name' => 'Entrostop Tablet', 'category' => 'Obat Bebas', 'unit' => 'Strip', 'sell_price' => 9000, 'buy_price' => 6500, 'min_stock' => 20, 'description' => 'Obat diare dengan attapulgite dan pectin.'],
            ['name' => 'Ultraflu', 'category' => 'Obat Bebas', 'unit' => 'Strip', 'sell_price' => 4000, 'buy_price' => 2800, 'min_stock' => 25, 'description' => 'Meredakan gejala flu seperti bersin dan demam.'],
            ['name' => 'Mixagrip Flu & Batuk', 'category' => 'Obat Bebas', 'unit' => 'Strip', 'sell_price' => 3500, 'buy_price' => 2400, 'min_stock' => 25, 'description' => 'Mengatasi demam, sakit kepala, bersin, dan batuk.'],
            ['name' => 'Komix Herbal Jeruk Nipis', 'category' => 'Obat Bebas', 'unit' => 'Sachet', 'sell_price' => 3000, 'buy_price' => 2000, 'min_stock' => 40, 'description' => 'Sirup herbal pereda batuk berdahak kemasan sachet.'],
            ['name' => 'Salonpas Koyo Hangat', 'category' => 'Obat Bebas', 'unit' => 'Sachet', 'sell_price' => 7500, 'buy_price' => 5500, 'min_stock' => 30, 'description' => 'Plester pereda nyeri otot dan pegal linu.'],
            ['name' => 'GPU Minyak Urut Jahe 60ml', 'category' => 'Obat Bebas', 'unit' => 'Botol', 'sell_price' => 22000, 'buy_price' => 17000, 'min_stock' => 10, 'description' => 'Minyak pijat untuk meredakan pegal linu dan keseleo.'],
            ['name' => 'Termorex Sirup 60ml', 'category' => 'Obat Bebas', 'unit' => 'Botol', 'sell_price' => 19000, 'buy_price' => 15000, 'min_stock' => 10, 'description' => 'Sirup penurun panas anak rasa jeruk bebas alkohol.'],
            ['name' => 'Counterpain Cream 30g', 'category' => 'Obat Bebas', 'unit' => 'Tube', 'sell_price' => 48000, 'buy_price' => 39000, 'min_stock' => 8, 'description' => 'Krim pereda nyeri sendi dan nyeri otot.'],

            // --- Obat Bebas Terbatas ---
            ['name' => 'Decolgen Tablet', 'category' => 'Obat Bebas Terbatas', 'unit' => 'Strip', 'sell_price' => 4500, 'buy_price' => 3000, 'min_stock' => 20, 'description' => 'Obat flu, batuk, dan hidung tersumbat.'],
            ['name' => 'Siladex Mucolytic 60ml', 'category' => 'Obat Bebas Terbatas', 'unit' => 'Botol', 'sell_price' => 17500, 'buy_price' => 13500, 'min_stock' => 10, 'description' => 'Obat batuk berdahak pengencer lendir.'],
            ['name' => 'OBH Combi Batuk & Flu 100ml', 'category' => 'Obat Bebas Terbatas', 'unit' => 'Botol', 'sell_price' => 24500, 'buy_price' => 19000, 'min_stock' => 10, 'description' => 'Sirup batuk hitam untuk batuk flu pilek.'],
            ['name' => 'Insto Regular Eye Drops 7.5ml', 'category' => 'Obat Bebas Terbatas', 'unit' => 'Botol', 'sell_price' => 17000, 'buy_price' => 13000, 'min_stock' => 15, 'description' => 'Tetes mata untuk mata merah dan iritasi ringan.'],
            ['name' => 'Rohto Cool Eye Drops 7ml', 'category' => 'Obat Bebas Terbatas', 'unit' => 'Botol', 'sell_price' => 18500, 'buy_price' => 14500, 'min_stock' => 12, 'description' => 'Tetes mata sensasi dingin menyegarkan.'],
            ['name' => 'Betadine Kumur Gargle 100ml', 'category' => 'Obat Bebas Terbatas', 'unit' => 'Botol', 'sell_price' => 28000, 'buy_price' => 22000, 'min_stock' => 8, 'description' => 'Obat kumur antiseptik rongga mulut dan tenggorokan.'],
            ['name' => 'Actifed Plus Expectorant Sirup 60ml', 'category' => 'Obat Bebas Terbatas', 'unit' => 'Botol', 'sell_price' => 68000, 'buy_price' => 56000, 'min_stock' => 5, 'description' => 'Meredakan batuk berdahak dan pilek (Actifed Hijau).'],
            ['name' => 'Neozep Forte', 'category' => 'Obat Bebas Terbatas', 'unit' => 'Strip', 'sell_price' => 6000, 'buy_price' => 4500, 'min_stock' => 15, 'description' => 'Meringankan gejala flu dan alergi pernapasan.'],
            ['name' => 'Procold Flu & Batuk', 'category' => 'Obat Bebas Terbatas', 'unit' => 'Strip', 'sell_price' => 4500, 'buy_price' => 3200, 'min_stock' => 20, 'description' => 'Meringankan gejala flu yang disertai batuk tidak berdahak.'],
            ['name' => 'Daktarin Cream 5g', 'category' => 'Obat Bebas Terbatas', 'unit' => 'Tube', 'sell_price' => 32000, 'buy_price' => 25000, 'min_stock' => 10, 'description' => 'Krim antijamur untuk infeksi kulit miconazole.'],

            // --- Obat Keras (Resep / Ethical) ---
            ['name' => 'Amoxicillin 500mg', 'category' => 'Obat Keras', 'unit' => 'Strip', 'sell_price' => 8500, 'buy_price' => 5500, 'min_stock' => 15, 'description' => 'Antibiotik spektrum luas untuk infeksi bakteri.', 'low_stock' => true], // Low Stock Flag
            ['name' => 'Cefixime 100mg', 'category' => 'Obat Keras', 'unit' => 'Strip', 'sell_price' => 35000, 'buy_price' => 27000, 'min_stock' => 10, 'description' => 'Antibiotik sefalosporin generasi ketiga.'],
            ['name' => 'Ciprofloxacin 500mg', 'category' => 'Obat Keras', 'unit' => 'Strip', 'sell_price' => 18000, 'buy_price' => 13000, 'min_stock' => 10, 'description' => 'Antibiotik fluorokuinolon untuk infeksi saluran kemih & cerna.'],
            ['name' => 'Amlodipine 5mg', 'category' => 'Obat Keras', 'unit' => 'Strip', 'sell_price' => 7500, 'buy_price' => 4500, 'min_stock' => 20, 'description' => 'Antihipertensi kalsium antagonis dosis 5mg.'],
            ['name' => 'Amlodipine 10mg', 'category' => 'Obat Keras', 'unit' => 'Strip', 'sell_price' => 12000, 'buy_price' => 8000, 'min_stock' => 15, 'description' => 'Antihipertensi kalsium antagonis dosis 10mg.'],
            ['name' => 'Metformin 500mg', 'category' => 'Obat Keras', 'unit' => 'Strip', 'sell_price' => 6500, 'buy_price' => 4000, 'min_stock' => 20, 'description' => 'Antidiabetes oral untuk diabetes mellitus tipe 2.'],
            ['name' => 'Glimepiride 2mg', 'category' => 'Obat Keras', 'unit' => 'Strip', 'sell_price' => 16000, 'buy_price' => 11000, 'min_stock' => 10, 'description' => 'Sulfonilurea untuk pengontrol gula darah.'],
            ['name' => 'Simvastatin 10mg', 'category' => 'Obat Keras', 'unit' => 'Strip', 'sell_price' => 9000, 'buy_price' => 6000, 'min_stock' => 15, 'description' => 'Pereda kolesterol dan trigliserida tinggi.'],
            ['name' => 'Simvastatin 20mg', 'category' => 'Obat Keras', 'unit' => 'Strip', 'sell_price' => 15000, 'buy_price' => 10000, 'min_stock' => 10, 'description' => 'Statin penurun kolesterol dosis 20mg.'],
            ['name' => 'Captopril 25mg', 'category' => 'Obat Keras', 'unit' => 'Strip', 'sell_price' => 6000, 'buy_price' => 3500, 'min_stock' => 15, 'description' => 'Antihipertensi ACE inhibitor dosis 25mg.', 'low_stock' => true], // Low Stock Flag
            ['name' => 'Candesartan 8mg', 'category' => 'Obat Keras', 'unit' => 'Strip', 'sell_price' => 28000, 'buy_price' => 21000, 'min_stock' => 10, 'description' => 'Antihipertensi reseptor angiotensin II.'],
            ['name' => 'Asam Mefenamat 500mg', 'category' => 'Obat Keras', 'unit' => 'Strip', 'sell_price' => 7500, 'buy_price' => 4500, 'min_stock' => 20, 'description' => 'Antiinflamasi non-steroid untuk nyeri haid dan sakit gigi.'],
            ['name' => 'Dexamethasone 0.5mg', 'category' => 'Obat Keras', 'unit' => 'Strip', 'sell_price' => 4500, 'buy_price' => 2500, 'min_stock' => 20, 'description' => 'Kortikosteroid untuk radang dan reaksi alergi.'],
            ['name' => 'Methylprednisolone 4mg', 'category' => 'Obat Keras', 'unit' => 'Strip', 'sell_price' => 14000, 'buy_price' => 9500, 'min_stock' => 15, 'description' => 'Steroid anti radang dan imunosupresif.'],
            ['name' => 'Omeprazole 20mg', 'category' => 'Obat Keras', 'unit' => 'Strip', 'sell_price' => 12000, 'buy_price' => 8000, 'min_stock' => 15, 'description' => 'PPI penekan produksi asam lambung dan GERD.'],
            ['name' => 'Lansoprazole 30mg', 'category' => 'Obat Keras', 'unit' => 'Strip', 'sell_price' => 16000, 'buy_price' => 11000, 'min_stock' => 10, 'description' => 'Proton pump inhibitor untuk tukak peptik.'],
            ['name' => 'Cetirizine 10mg', 'category' => 'Obat Keras', 'unit' => 'Strip', 'sell_price' => 8000, 'buy_price' => 5000, 'min_stock' => 15, 'description' => 'Antihistamin generasi kedua untuk rhinitis dan biduran.'],
            ['name' => 'Salbutamol 2mg', 'category' => 'Obat Keras', 'unit' => 'Strip', 'sell_price' => 5500, 'buy_price' => 3200, 'min_stock' => 15, 'description' => 'Bronkodilator untuk sesak napas dan asma.'],
            ['name' => 'Allopurinol 100mg', 'category' => 'Obat Keras', 'unit' => 'Strip', 'sell_price' => 7500, 'buy_price' => 4500, 'min_stock' => 15, 'description' => 'Pereda kadar asam urat tinggi dalam darah.'],

            // --- Vitamin & Suplemen ---
            ['name' => 'Enervon-C Multivitamin', 'category' => 'Vitamin & Suplemen', 'unit' => 'Strip', 'sell_price' => 7500, 'buy_price' => 5000, 'min_stock' => 20, 'description' => 'Kombinasi vitamin C 500mg dan vitamin B kompleks.'],
            ['name' => 'Imboost Force', 'category' => 'Vitamin & Suplemen', 'unit' => 'Strip', 'sell_price' => 48000, 'buy_price' => 39000, 'min_stock' => 10, 'description' => 'Echinacea & Black Elderberry untuk daya tahan tubuh.'],
            ['name' => 'Holisticare Ester C', 'category' => 'Vitamin & Suplemen', 'unit' => 'Botol', 'sell_price' => 55000, 'buy_price' => 44000, 'min_stock' => 10, 'description' => 'Vitamin C ester aman bagi lambung isi 30 tablet.'],
            ['name' => 'Redoxon Double Action', 'category' => 'Vitamin & Suplemen', 'unit' => 'Tube', 'sell_price' => 49000, 'buy_price' => 40000, 'min_stock' => 8, 'description' => 'Vitamin C 1000mg + Zinc effervescent rasa jeruk.'],
            ['name' => 'Sangobion Kapsul', 'category' => 'Vitamin & Suplemen', 'unit' => 'Strip', 'sell_price' => 19500, 'buy_price' => 15000, 'min_stock' => 15, 'description' => 'Zat besi dan multivitamin penambah darah.'],
            ['name' => 'CDR Effervescent 10s', 'category' => 'Vitamin & Suplemen', 'unit' => 'Tube', 'sell_price' => 52000, 'buy_price' => 42000, 'min_stock' => 10, 'description' => 'Kalsium, Vitamin D, C, B6 untuk kesehatan tulang.'],
            ['name' => 'Caviplex Multivitamin', 'category' => 'Vitamin & Suplemen', 'unit' => 'Box', 'sell_price' => 15000, 'buy_price' => 10500, 'min_stock' => 15, 'description' => 'Suplemen kaplet multivitamin dan mineral lengkap.'],
            ['name' => 'Curcuma Plus Sirup 60ml', 'category' => 'Vitamin & Suplemen', 'unit' => 'Botol', 'sell_price' => 18000, 'buy_price' => 14000, 'min_stock' => 10, 'description' => 'Minyak ikan dan temulawak penambah nafsu makan anak.'],
            ['name' => 'Biolysin Kids Sirup 60ml', 'category' => 'Vitamin & Suplemen', 'unit' => 'Botol', 'sell_price' => 21000, 'buy_price' => 16500, 'min_stock' => 8, 'description' => 'Multivitamin sirup dan asam amino lisin untuk anak.'],
            ['name' => 'Blackmores Multivitamins + Minerals', 'category' => 'Vitamin & Suplemen', 'unit' => 'Botol', 'sell_price' => 165000, 'buy_price' => 135000, 'min_stock' => 5, 'description' => 'Suplemen harian premium isi 30 tablet.'],

            // --- Alat Kesehatan & P3K ---
            ['name' => 'Betadine Antiseptic Solution 15ml', 'category' => 'Alat Kesehatan', 'unit' => 'Botol', 'sell_price' => 26500, 'buy_price' => 20500, 'min_stock' => 15, 'description' => 'Povidone iodine 10% antiseptik luka lecet dan sayat.', 'low_stock' => true], // Low Stock Flag
            ['name' => 'Dettol Antiseptic Liquid 95ml', 'category' => 'Alat Kesehatan', 'unit' => 'Botol', 'sell_price' => 36000, 'buy_price' => 29000, 'min_stock' => 10, 'description' => 'Cairan disinfektan dan pembersih higienis antiseptik.'],
            ['name' => 'Termometer Digital OneMed', 'category' => 'Alat Kesehatan', 'unit' => 'Pcs', 'sell_price' => 35000, 'buy_price' => 25000, 'min_stock' => 10, 'description' => 'Termometer badan digital akurat pengukur suhu tubuh.', 'low_stock' => true], // Low Stock Flag
            ['name' => 'Masker Medis Sensi 3-Ply 50s', 'category' => 'Alat Kesehatan', 'unit' => 'Box', 'sell_price' => 45000, 'buy_price' => 35000, 'min_stock' => 10, 'description' => 'Masker bedah 3 lapis proteksi droplet dan virus.'],
            ['name' => 'Kasa Steril Husada 16x16', 'category' => 'Alat Kesehatan', 'unit' => 'Box', 'sell_price' => 14000, 'buy_price' => 10000, 'min_stock' => 15, 'description' => 'Kain kasa steril penutup luka isi 10 sachet.'],
            ['name' => 'Alkohol Medis 70% 100ml', 'category' => 'Alat Kesehatan', 'unit' => 'Botol', 'sell_price' => 9500, 'buy_price' => 6500, 'min_stock' => 20, 'description' => 'Alkohol pembersih luka dan sterilisasi alat medis.'],
            ['name' => 'Rivanol Kompres 100ml', 'category' => 'Alat Kesehatan', 'unit' => 'Botol', 'sell_price' => 8000, 'buy_price' => 5500, 'min_stock' => 15, 'description' => 'Cairan kompres luka basah dan bisul.'],
            ['name' => 'Tensocrepe Perban Elastis 3 Inch', 'category' => 'Alat Kesehatan', 'unit' => 'Roll', 'sell_price' => 58000, 'buy_price' => 46000, 'min_stock' => 5, 'description' => 'Perban elastis berkualitas tinggi fiksasi cedera sendi.'],

            // --- Herbal ---
            ['name' => 'Tolak Angin Cair Sido Muncul', 'category' => 'Herbal', 'unit' => 'Sachet', 'sell_price' => 4800, 'buy_price' => 3300, 'min_stock' => 50, 'description' => 'Obat herbal terstandar untuk gejala masuk angin dan mual.'],
            ['name' => 'Antangin JRG Cair', 'category' => 'Herbal', 'unit' => 'Sachet', 'sell_price' => 4500, 'buy_price' => 3100, 'min_stock' => 50, 'description' => 'Herbal jahe merah, royal jelly, dan ginseng masuk angin.'],
            ['name' => 'Minyak Kayu Putih Cap Lang 60ml', 'category' => 'Herbal', 'unit' => 'Botol', 'sell_price' => 29500, 'buy_price' => 23500, 'min_stock' => 15, 'description' => 'Minyak kayu putih alami menghangatkan tubuh.'],
            ['name' => 'Minyak Kayu Putih Cap Lang 120ml', 'category' => 'Herbal', 'unit' => 'Botol', 'sell_price' => 54000, 'buy_price' => 44000, 'min_stock' => 10, 'description' => 'Minyak kayu putih ukuran keluarga 120ml.'],
            ['name' => 'Minyak Telon Lang Plus 60ml', 'category' => 'Herbal', 'unit' => 'Botol', 'sell_price' => 26000, 'buy_price' => 20500, 'min_stock' => 15, 'description' => 'Minyak telon anti nyamuk untuk bayi dan anak.'],
            ['name' => 'Madu TJ Murni 150g', 'category' => 'Herbal', 'unit' => 'Botol', 'sell_price' => 24000, 'buy_price' => 19000, 'min_stock' => 10, 'description' => 'Madu alami murni kaya vitamin dan enzim lebah.'],
            ['name' => 'Tolak Linu Herbal', 'category' => 'Herbal', 'unit' => 'Sachet', 'sell_price' => 4000, 'buy_price' => 2800, 'min_stock' => 30, 'description' => 'Herbal redakan pegal linu dan nyeri sendi.'],
            ['name' => 'Kiranti Sehat Datang Bulan 150ml', 'category' => 'Herbal', 'unit' => 'Botol', 'sell_price' => 9000, 'buy_price' => 7000, 'min_stock' => 15, 'description' => 'Minuman kunyit asam alami redakan nyeri haid.'],

            // --- Ibu & Anak ---
            ['name' => 'Sanmol Sirup Anak 60ml', 'category' => 'Ibu & Anak', 'unit' => 'Botol', 'sell_price' => 23000, 'buy_price' => 18000, 'min_stock' => 10, 'description' => 'Sirup penurun panas dan pereda demam bayi & anak.'],
            ['name' => 'Tempra Drops Paracetamol 15ml', 'category' => 'Ibu & Anak', 'unit' => 'Botol', 'sell_price' => 55000, 'buy_price' => 44000, 'min_stock' => 8, 'description' => 'Tetes demam bayi dengan pipet takar presisi.'],
            ['name' => 'Proris Sirup Ibuprofen 60ml', 'category' => 'Ibu & Anak', 'unit' => 'Botol', 'sell_price' => 34000, 'buy_price' => 27000, 'min_stock' => 10, 'description' => 'Ibuprofen suspensi demam tinggi dan radang anak.'],
            ['name' => 'Caladine Lotion Anti Gatal 60ml', 'category' => 'Ibu & Anak', 'unit' => 'Botol', 'sell_price' => 22000, 'buy_price' => 17500, 'min_stock' => 10, 'description' => 'Losion biang keringat dan gatal gigitan serangga.'],
            ['name' => 'Zwitsal Baby Powder 100g', 'category' => 'Ibu & Anak', 'unit' => 'Botol', 'sell_price' => 14500, 'buy_price' => 11000, 'min_stock' => 12, 'description' => 'Bedak bayi lembut wangi khas chamomile.'],
            ['name' => 'Cessa Baby Cough & Flu Roll On', 'category' => 'Ibu & Anak', 'unit' => 'Pcs', 'sell_price' => 38000, 'buy_price' => 30000, 'min_stock' => 8, 'description' => 'Essential oil roll-on redakan hidung tersumbat anak.'],
        ];

        // 4. Insert Products and Batches
        foreach ($products as $p) {
            $isLowStock = $p['low_stock'] ?? false;
            
            $product = Product::create([
                'category_id' => $categoryIds[$p['category']],
                'unit_id' => $unitIds[$p['unit']],
                'name' => $p['name'],
                'slug' => Str::slug($p['name']) . '-' . Str::random(5),
                'barcode' => '899' . str_pad((string)rand(100000000, 999999999), 10, '0', STR_PAD_LEFT),
                'min_stock' => $p['min_stock'],
                'sell_price' => $p['sell_price'],
                'purchase_price' => $p['buy_price'],
                'description' => $p['description'],
            ]);

            if ($isLowStock) {
                // Intentionally create low stock (e.g. 2-3 units) below min_stock
                $qty = rand(1, 3);
                Batch::create([
                    'product_id' => $product->id,
                    'batch_no' => 'BATCH-' . strtoupper(Str::random(6)),
                    'expired_date' => Carbon::now()->addMonths(rand(6, 18)),
                    'stock_in' => 50,
                    'stock_current' => $qty,
                    'buy_price' => $p['buy_price'],
                ]);
            } else {
                // Normal product: 1-2 batches with healthy stock
                $numBatches = rand(1, 2);
                for ($i = 0; $i < $numBatches; $i++) {
                    $stockCurrent = rand(30, 120);
                    $stockIn = $stockCurrent + rand(0, 30);
                    
                    // 1 in 20 batches near expiry (next 25-45 days) for realism
                    $expiredDate = (rand(1, 20) === 1) 
                        ? Carbon::now()->addDays(rand(20, 45))
                        : Carbon::now()->addMonths(rand(10, 28));

                    Batch::create([
                        'product_id' => $product->id,
                        'batch_no' => 'BATCH-' . strtoupper(Str::random(6)),
                        'expired_date' => $expiredDate,
                        'stock_in' => $stockIn,
                        'stock_current' => $stockCurrent,
                        'buy_price' => $p['buy_price'],
                    ]);
                }
            }
        }
    }
}
