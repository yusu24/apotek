<x-app-layout>
    <div class="min-h-screen bg-slate-50 flex flex-col lg:flex-row">
        
        <!-- Sidebar Navigation (Sticky) -->
        <aside class="lg:w-80 bg-white border-r border-slate-200 lg:sticky lg:top-0 lg:h-screen overflow-y-auto no-print">
            <div class="p-8">
                <div class="flex items-center gap-3 mb-10">
                    <div class="p-2.5 bg-blue-600 rounded-xl shadow-lg shadow-blue-200 text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </div>
                    <h2 class="text-xl font-bold text-slate-900 tracking-tight">Handbooks</h2>
                </div>

                <nav class="space-y-1.5">
                    <p class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mb-3">Getting Started</p>
                    <a href="#quickstart" class="group flex items-center px-4 py-3 text-sm font-bold text-slate-600 rounded-xl hover:bg-blue-50 hover:text-blue-600 transition-all">
                        Quick Start Guide
                    </a>
                    <a href="#login" class="group flex items-center px-4 py-3 text-sm font-bold text-slate-600 rounded-xl hover:bg-blue-50 hover:text-blue-600 transition-all">
                        Login & Access
                    </a>

                    <p class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mt-8 mb-3">System Modules</p>
                    <a href="#dashboard" class="group flex items-center px-4 py-3 text-sm font-bold text-slate-600 rounded-xl hover:bg-blue-50 hover:text-blue-600 transition-all">
                        Dashboard Utama
                    </a>
                    <a href="#master" class="group flex items-center px-4 py-3 text-sm font-bold text-slate-600 rounded-xl hover:bg-blue-50 hover:text-blue-600 transition-all">
                        Data Master Obat
                    </a>
                    <a href="#stock" class="group flex items-center px-4 py-3 text-sm font-bold text-slate-600 rounded-xl hover:bg-blue-50 hover:text-blue-600 transition-all">
                        Stok & Inventori
                    </a>
                    <a href="#pos" class="group flex items-center px-4 py-3 text-sm font-bold text-slate-600 rounded-xl hover:bg-blue-50 hover:text-blue-600 transition-all">
                        Kasir (POS)
                    </a>
                    <a href="#procurement" class="group flex items-center px-4 py-3 text-sm font-bold text-slate-600 rounded-xl hover:bg-blue-50 hover:text-blue-600 transition-all">
                        Pengadaan (PO)
                    </a>
                    <a href="#reports" class="group flex items-center px-4 py-3 text-sm font-bold text-slate-600 rounded-xl hover:bg-blue-50 hover:text-blue-600 transition-all">
                        Laporan Keuangan
                    </a>

                    <p class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mt-8 mb-3">Administration</p>
                    <a href="#profile" class="group flex items-center px-4 py-3 text-sm font-bold text-slate-600 rounded-xl hover:bg-blue-50 hover:text-blue-600 transition-all">
                        Profil & Keamanan
                    </a>
                    <a href="#settings" class="group flex items-center px-4 py-3 text-sm font-bold text-slate-600 rounded-xl hover:bg-blue-50 hover:text-blue-600 transition-all">
                        Manajemen User
                    </a>
                </nav>

                <div class="mt-12 pt-8 border-t border-slate-100">
                    <button onclick="window.print()" class="w-full flex items-center justify-center gap-2 bg-slate-900 text-white px-4 py-3.5 rounded-2xl hover:bg-slate-800 font-bold transition-all shadow-xl active:scale-95">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        Export to PDF
                    </button>
                    <a href="{{ route('guide.index') }}" class="w-full mt-4 flex items-center justify-center gap-2 bg-white border border-slate-200 text-slate-600 px-4 py-3.5 rounded-2xl hover:bg-slate-50 font-bold transition-all shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Kembali Ke Hub
                    </a>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 overflow-y-auto">
            <div class="max-w-screen-lg mx-auto p-6 md:p-12 xl:p-24 space-y-32">
                
                <!-- Quick Start -->
                <section id="quickstart" class="scroll-mt-32">
                    <div class="mb-12">
                        <h1 class="text-6xl font-bold text-slate-900 tracking-tighter mb-6">BUKU PANDUAN PENGGUNA</h1>
                        <p class="text-xl text-slate-500 max-w-2xl leading-relaxed font-medium">Panduan lengkap pengoperasian sistem informasi apotek dengan standar operasional terbaru.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-16">
                        <div class="p-8 bg-white border border-slate-200 rounded-[2.5rem] shadow-sm hover:shadow-xl transition-all">
                            <h3 class="text-xl font-bold text-slate-900 mb-4">Mulai Cepat</h3>
                            <p class="text-slate-500 text-sm leading-relaxed mb-6">Pelajari alur dasar dari login hingga transaksi pertama Anda dalam kurang dari 5 menit.</p>
                            <a href="#login" class="inline-flex items-center text-blue-600 font-bold text-sm uppercase tracking-wider group">
                                Buka Langkah 1 
                                <svg class="w-4 h-4 ml-2 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                            </a>
                        </div>
                        <div class="p-8 bg-blue-600 rounded-[2.5rem] shadow-2xl shadow-blue-200 text-white">
                            <h3 class="text-xl font-bold mb-4">Tips Efisiensi</h3>
                            <p class="text-blue-100 text-sm leading-relaxed mb-6">Gunakan fitur shortcut dan pencarian cepat untuk melayani pelanggan lebih responsif.</p>
                            <a href="#pos" class="inline-flex items-center text-white font-bold text-sm uppercase tracking-wider group">
                                Lihat Panduan POS
                                <svg class="w-4 h-4 ml-2 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                            </a>
                        </div>
                    </div>
                </section>

                <!-- Modular Sections Renderer -->
                @php
                    $sections = [
                        [
                            'id' => 'login',
                            'title' => 'Login & Akses Sistem',
                            'desc' => 'Setiap armada apotek harus menggunakan akun resmi yang telah didaftarkan untuk menjamin akurasi data transaksi.',
                            'steps' => [
                                'Masukkan Email & Password yang diberikan admin.',
                                'Pastikan koneksi internet stabil untuk sinkronisasi data.',
                                'Sistem akan mengarahkan Anda ke Dashboard sesuai hak akses Anda.'
                            ],
                            'tip' => 'Jangan pernah memberikan password Anda kepada siapapun, termasuk sesama rekan kerja.'
                        ],
                        [
                            'id' => 'dashboard',
                            'title' => 'Analitik Dashboard',
                            'image' => 'dashboard.png',
                            'desc' => 'Halaman utama yang menyajikan ringkasan performa apotek secara akurat dan real-time.',
                            'points' => [
                                'Total Penjualan Hari Ini: Akumulasi transaksi tunai & non-tunai.',
                                'Stok Kritis: Notifikasi obat yang wajib segera dipesan.',
                                'Grafik Pendapatan: Tren penjualan dalam 7 hari terakhir.'
                            ]
                        ],
                        [
                            'id' => 'master',
                            'title' => 'Manajemen Data Master',
                            'image' => 'product.png',
                            'desc' => 'Database pusat obat-obatan. Di sini Anda mengelola identitas produk, kategori, dan satuan.',
                            'steps' => [
                                'Gunakan Scan Barcode untuk input yang lebih cepat.',
                                'Atur Konversi Satuan (misal: Box ke Tablet) untuk mendukung penjualan ecer.',
                                'Set Minimum Stock untuk memicu alarm stok di dashboard.'
                            ]
                        ],
                        [
                            'id' => 'stock',
                            'title' => 'Monitoring Stok & Batch',
                            'image' => 'stock.png',
                            'desc' => 'Lacak setiap unit obat berdasarkan nomor batch dan tanggal kadaluarsa (FEFO/FIFO).',
                            'tip' => 'Lakukan Stok Opname secara berkala setiap akhir bulan untuk meminimalkan selisih barang.'
                        ],
                        [
                            'id' => 'pos',
                            'title' => 'Transaksi Kasir (POS)',
                            'image' => 'pos.png',
                            'desc' => 'Antarmuka penjualan ritel yang dioptimalkan untuk kecepatan pelayanan di gerai apotek.',
                            'steps' => [
                                'Pilih produk dengan scan atau ketik manual.',
                                'Tentukan diskon per item atau diskon global jika diizinkan.',
                                'Klik "Bayar" dan pilih metode pembayaran (Cash/QRIS/Transfer).'
                            ]
                        ],
                        [
                            'id' => 'procurement',
                            'title' => 'Pengadaan (Purchase Order)',
                            'image' => 'procurement.png',
                            'desc' => 'Kelola pesanan barang ke supplier dan sinkronisasi dengan penerimaan stok gudang.',
                            'tip' => 'Pastikan harga beli diupdate saat penerimaan barang jika ada perubahan dari supplier.'
                        ],
                        [
                            'id' => 'reports',
                            'title' => 'Laporan Laba Rugi',
                            'image' => 'finance.png',
                            'desc' => 'Laporan komprehensif yang menghitung realitas finansial operasional apotek Anda.',
                            'points' => [
                                'Laba Kotor: Pendapatan dikurangi Harga Pokok Penjualan (HPP).',
                                'Beban Operasional: Pengeluaran rutin di luar stok barang.',
                                'Laba Bersih: Angka final keuntungan yang didapatkan.'
                            ]
                        ],
                        [
                            'id' => 'profile',
                            'title' => 'Profil & Keamanan Akun',
                            'image' => 'profile.png',
                            'desc' => 'Pengaturan identitas personal user yang sedang login di sistem.'
                        ],
                        [
                            'id' => 'settings',
                            'title' => 'Manajemen Hak Akses',
                            'image' => 'settings.png',
                            'desc' => 'Pengaturan RBAC (Role Based Access Control) untuk membatasi menu bagi setiap user.',
                            'tip' => 'Berikan role "Kasir" hanya untuk fungsi penjualan, dan "Admin" untuk akses laporan.'
                        ]
                    ];
                @endphp

                @foreach($sections as $index => $section)
                <section id="{{ $section['id'] }}" class="scroll-mt-32 space-y-12">
                    <div class="max-w-3xl">
                        <div class="flex items-center gap-5 mb-6">
                            <span class="text-sm font-bold text-blue-600 uppercase tracking-[0.3em]">Module 0{{ $index + 1 }}</span>
                            <div class="h-px flex-1 bg-slate-200"></div>
                        </div>
                        <h2 class="text-4xl font-bold text-slate-900 leading-none mb-6 tracking-tight uppercase">{{ $section['title'] }}</h2>
                        <p class="text-lg text-slate-600 leading-relaxed font-medium">{{ $section['desc'] }}</p>
                    </div>

                    @if(isset($section['image']))
                    <div class="relative group">
                        <div class="absolute -inset-1 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-[3rem] blur opacity-10 group-hover:opacity-20 transition duration-1000 group-hover:duration-200"></div>
                        <div class="relative bg-white border border-slate-200 rounded-[2.5rem] overflow-hidden shadow-2xl shadow-slate-200/50">
                            <img src="{{ asset('images/guide/' . $section['image']) }}" alt="{{ $section['title'] }}" class="w-full object-cover">
                            <div class="absolute bottom-6 right-8 bg-slate-900/80 backdrop-blur-md px-5 py-2.5 rounded-2xl text-white text-[10px] font-bold uppercase tracking-widest border border-white/10 no-print">
                                Real System Interface
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                        @if(isset($section['steps']))
                        <div class="space-y-6">
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest">Cara Operasional</h4>
                            <ul class="space-y-4">
                                @foreach($section['steps'] as $step)
                                <li class="flex items-start gap-4">
                                    <div class="mt-1 w-5 h-5 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center flex-shrink-0 text-[10px] font-bold">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <span class="text-slate-700 font-bold leading-tight">{{ $step }}</span>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        @if(isset($section['points']))
                        <div class="space-y-6">
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest">Fitur Utama</h4>
                            <ul class="space-y-4">
                                @foreach($section['points'] as $point)
                                <li class="flex items-start gap-4 p-5 bg-white border border-slate-100 rounded-3xl shadow-sm">
                                    <div class="mt-0.5 p-2 bg-slate-50 border border-slate-100 rounded-xl">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <span class="text-slate-600 text-sm font-medium leading-relaxed">{{ $point }}</span>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                    </div>

                    @if(isset($section['tip']))
                    <div class="p-8 bg-blue-50 border border-blue-100 rounded-[2.5rem] flex items-center gap-6">
                        <div class="w-16 h-16 bg-blue-600 text-white rounded-2xl flex items-center justify-center flex-shrink-0 shadow-lg shadow-blue-200">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                        <div>
                            <h5 class="text-sm font-bold text-blue-900 uppercase tracking-widest mb-1">Penting</h5>
                            <p class="text-blue-700 font-bold italic leading-snug">{{ $section['tip'] }}</p>
                        </div>
                    </div>
                    @endif
                </section>
                @endforeach

                <footer class="mt-32 pt-16 border-t border-slate-200 text-center pb-20">
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-[0.5em]">Tangguh Kreatifindo &bull; Apotek POS v1.0</p>
                </footer>

            </div>
        </main>
    </div>

    <style>
        html {
            scroll-behavior: smooth;
        }

        /* Improved Print Styling */
        @media print {
            .no-print { display: none !important; }
            aside { display: none !important; }
            main { padding: 0 !important; }
            .guide-content { max-width: 100% !important; }
            section { page-break-after: always; padding-top: 4rem !important; }
            h1 { font-size: 3rem !important; }
            h2 { font-size: 2.5rem !important; }
            img { border-radius: 1rem !important; border: 1px solid #e2e8f0 !important; }
            .p-8 { padding: 1.5rem !important; }
            .rounded-\[2\.5rem\] { border-radius: 1.5rem !important; }
        }

        /* Active Nav Highlights */
        nav a.active {
            background-color: #eff6ff;
            color: #2563eb;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.05);
        }

        /* Custom Scrollbar for Sidebar */
        aside::-webkit-scrollbar { width: 6px; }
        aside::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sections = document.querySelectorAll('section[id]');
            const navLinks = document.querySelectorAll('nav a');

            function syncActiveLink() {
                let scrollY = window.pageYOffset;
                
                sections.forEach(current => {
                    const sectionHeight = current.offsetHeight;
                    const sectionTop = current.offsetTop - 200;
                    const sectionId = current.getAttribute('id');

                    if (scrollY > sectionTop && scrollY <= sectionTop + sectionHeight) {
                        navLinks.forEach(link => {
                            link.classList.remove('active');
                            if (link.getAttribute('href') === '#' + sectionId) {
                                link.classList.add('active');
                            }
                        });
                    }
                });
            }

            window.addEventListener('scroll', syncActiveLink);
            syncActiveLink();

            // Handle Print Parameter
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('print') === '1') {
                setTimeout(() => { window.print(); }, 1500);
            }
        });
    </script>
</x-app-layout>
