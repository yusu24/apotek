<div class="p-4 md:p-6 text-gray-800 font-sans">
    <div class="grid grid-cols-1 md:grid-cols-12 gap-6 md:gap-8">
        <!-- Mobile Back Button -->
        <div class="md:hidden col-span-1">
            <a href="{{ route('guide.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-900 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                <span class="font-medium">Kembali ke Panduan</span>
            </a>
        </div>

        <!-- Sidebar Navigation -->
        <div class="md:col-span-3 hidden md:block">
            <div class="sticky top-6 space-y-4">
                <a href="{{ route('guide.index') }}" class="inline-flex items-center gap-2.5 text-gray-500 hover:text-blue-600 transition-colors group">
                    <span class="w-7 h-7 rounded-full bg-gray-100 group-hover:bg-blue-50 flex items-center justify-center transition-colors flex-shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    </span>
                    <span class="font-semibold text-sm">Semua Panduan</span>
                </a>

                <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
                    <h3 class="text-[11px] font-bold text-gray-400 mb-3 uppercase tracking-wider">Di Halaman Ini</h3>
                    <nav class="space-y-0.5">
                        <a href="#overview" class="flex items-center gap-2.5 px-3 py-2 text-xs font-medium text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-colors border-l-2 border-transparent hover:border-blue-500">
                            <svg class="w-3.5 h-3.5 flex-shrink-0 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Ringkasan
                        </a>
                        @if(!empty($guide['screenshots']))
                        <a href="#screenshots" class="flex items-center gap-2.5 px-3 py-2 text-xs font-medium text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-colors border-l-2 border-transparent hover:border-blue-500">
                            <svg class="w-3.5 h-3.5 flex-shrink-0 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M14 8h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            Tampilan
                        </a>
                        @endif
                        @if(!empty($guide['golden_rules']))
                        <a href="#rules" class="flex items-center gap-2.5 px-3 py-2 text-xs font-medium text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-colors border-l-2 border-transparent hover:border-blue-500">
                            <svg class="w-3.5 h-3.5 flex-shrink-0 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            Aturan Penting
                        </a>
                        @endif
                        @if(!empty($guide['procedures']))
                        <a href="#procedures" class="flex items-center gap-2.5 px-3 py-2 text-xs font-medium text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-colors border-l-2 border-transparent hover:border-blue-500">
                            <svg class="w-3.5 h-3.5 flex-shrink-0 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                            Prosedur
                        </a>
                        @endif
                        @if(!empty($guide['form_fields']))
                        <a href="#forms" class="flex items-center gap-2.5 px-3 py-2 text-xs font-medium text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-colors border-l-2 border-transparent hover:border-blue-500">
                            <svg class="w-3.5 h-3.5 flex-shrink-0 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Formulir
                        </a>
                        @endif
                    </nav>
                </div>

                @if($slug !== 'user-manual')
                <a href="{{ route('guide.detail', 'user-manual') }}" class="flex items-center gap-3 bg-gradient-to-br from-blue-600 to-indigo-700 text-white rounded-2xl p-4 shadow-sm hover:shadow-md transition-shadow">
                    <div class="w-9 h-9 rounded-xl bg-white/15 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-xs font-bold leading-tight">Buku Panduan Lengkap</div>
                        <div class="text-[10px] text-blue-100 mt-0.5">Lihat alur semua modul</div>
                    </div>
                </a>
                @endif
            </div>
        </div>

        <!-- Main Content -->
        <div class="md:col-span-9 col-span-1">
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">

                <!-- Header Band -->
                <div class="bg-gradient-to-br from-slate-50 to-blue-50/60 border-b border-gray-100 px-5 md:px-8 py-6 md:py-8" id="overview">
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                        <div class="min-w-0">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-600 text-white text-[10px] font-bold uppercase tracking-wider mb-3 shadow-sm">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                                {{ $guide['category'] ?? 'Panduan' }}
                            </span>
                            <h1 class="text-lg md:text-2xl font-bold text-gray-900">{{ $guide['title'] }}</h1>
                            <p class="text-gray-500 mt-2 text-xs md:text-sm max-w-2xl leading-relaxed">{{ $guide['description'] }}</p>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            @if($slug === 'user-manual')
                            <a href="{{ route('pdf.user-manual') }}" class="btn btn-export-pdf btn-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                                <span class="hidden sm:inline">Export PDF</span>
                            </a>
                            @endif
                            <button onclick="window.print()" class="btn btn-secondary btn-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                <span>Print</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="px-5 md:px-8 py-6 md:py-8">

                    <!-- Image & Info Panels -->
                    <div class="flex flex-col lg:flex-row gap-6 md:gap-8 mb-10">
                        @if(file_exists(public_path('images/guide/' . ($guide['image'] ?? ''))))
                        <div class="flex-1 min-w-0">
                            <div class="rounded-2xl border border-gray-200 shadow-sm overflow-hidden bg-gray-50">
                                <div class="flex items-center gap-1.5 px-4 py-2.5 bg-gray-100 border-b border-gray-200">
                                    <span class="w-2.5 h-2.5 rounded-full bg-red-400"></span>
                                    <span class="w-2.5 h-2.5 rounded-full bg-yellow-400"></span>
                                    <span class="w-2.5 h-2.5 rounded-full bg-green-400"></span>
                                </div>
                                <img src="{{ asset('images/guide/' . $guide['image']) }}" class="w-full h-auto object-cover">
                            </div>
                        </div>
                        @endif

                        @if(!empty($guide['buttons']) || !empty($guide['sub_menus']))
                        <div class="lg:w-80 flex-shrink-0 space-y-4">
                            @if(!empty($guide['buttons']))
                            <div class="bg-gray-50 rounded-xl border border-gray-100 overflow-hidden">
                                <h4 class="text-[11px] font-bold text-gray-500 uppercase tracking-wide px-5 pt-4 pb-2 flex items-center gap-2">
                                    <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path></svg>
                                    Tombol & Aksi
                                </h4>
                                <div class="divide-y divide-gray-100">
                                    @foreach($guide['buttons'] as $btn)
                                    <div class="px-5 py-3">
                                        <div class="font-bold text-gray-800 text-xs">{{ $btn['label'] }}</div>
                                        <div class="text-gray-500 text-xs mt-0.5 leading-relaxed">{{ $btn['func'] }}</div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            @if(!empty($guide['sub_menus']))
                            <div class="bg-gray-50 rounded-xl border border-gray-100 overflow-hidden">
                                <h4 class="text-[11px] font-bold text-gray-500 uppercase tracking-wide px-5 pt-4 pb-2 flex items-center gap-2">
                                    <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
                                    Sub-Menu
                                </h4>
                                <div class="divide-y divide-gray-100">
                                    @foreach($guide['sub_menus'] as $sub)
                                    <div class="px-5 py-3">
                                        <div class="font-bold text-gray-800 text-xs">{{ $sub['name'] }}</div>
                                        <div class="text-gray-500 text-xs mt-0.5 leading-relaxed">{{ $sub['func'] }}</div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>

                    <!-- Screenshots Gallery -->
                    @if(!empty($guide['screenshots']))
                    <div id="screenshots" class="mb-10 scroll-mt-6">
                        <h3 class="text-base font-bold text-gray-900 mb-6 flex items-center gap-2.5">
                            <span class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M14 8h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </span>
                            Galeri Tampilan
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($guide['screenshots'] as $shot)
                            <div class="space-y-3">
                                <div class="rounded-xl overflow-hidden border border-gray-200 shadow-sm bg-gray-50 group">
                                    <img src="{{ asset('images/guide/' . $shot['src']) }}" class="w-full h-48 object-cover object-top transition-transform duration-500 group-hover:scale-105" alt="{{ $shot['caption'] }}">
                                </div>
                                <p class="text-sm text-gray-500 italic text-center">{{ $shot['caption'] }}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Golden Rules -->
                    @if(!empty($guide['golden_rules']))
                    <div id="rules" class="mb-10 scroll-mt-6">
                        <div class="bg-amber-50 border border-amber-200 rounded-xl p-5 md:p-6">
                            <h3 class="text-sm font-bold text-amber-900 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                Aturan Penting
                            </h3>
                            <div class="grid gap-3">
                                @foreach($guide['golden_rules'] as $index => $rule)
                                <div class="flex gap-3.5 bg-white/60 rounded-lg p-3">
                                    <span class="flex-shrink-0 w-6 h-6 bg-amber-200 text-amber-800 rounded-full flex items-center justify-center text-xs font-bold">{{ $index + 1 }}</span>
                                    <span class="text-xs text-gray-700 leading-relaxed pt-0.5">{!! str_replace(['**', '##'], ['<strong>', '</strong>'], $rule) !!}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Procedures -->
                    @if(!empty($guide['procedures']))
                    <div id="procedures" class="mb-10 scroll-mt-6">
                        <h3 class="text-base font-bold text-gray-900 mb-6 flex items-center gap-2.5">
                            <span class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </span>
                            Prosedur Penggunaan
                        </h3>
                        <div class="space-y-6">
                            @foreach($guide['procedures'] as $index => $proc)
                            <div class="flex gap-4">
                                <div class="flex-shrink-0 w-8 h-8 bg-gray-900 text-white rounded-lg flex items-center justify-center font-bold text-sm shadow-md">
                                    {{ $index + 1 }}
                                </div>
                                <div class="min-w-0 pt-0.5">
                                    <h4 class="text-sm font-bold text-gray-900 mb-1">{{ $proc['title'] }}</h4>
                                    <p class="text-xs text-gray-600 leading-relaxed">{!! nl2br(str_replace(['**', '##'], ['<strong>', '</strong>'], $proc['desc'])) !!}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Form Fields -->
                    @if(!empty($guide['form_fields']))
                    <div id="forms" class="scroll-mt-6">
                        <h3 class="text-base font-bold text-gray-900 mb-6 flex items-center gap-2.5">
                            <span class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </span>
                            Penjelasan Formulir
                        </h3>
                        <div class="overflow-x-auto border border-gray-200 rounded-xl">
                            <table class="w-full text-xs text-left">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="px-6 py-4 font-bold text-gray-900 w-1/4">Nama Field</th>
                                        <th class="px-6 py-4 font-bold text-gray-900">Keterangan</th>
                                        <th class="px-6 py-4 font-bold text-gray-900 text-center w-24">Wajib?</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($guide['form_fields'] as $field)
                                    <tr class="hover:bg-gray-50/50">
                                        <td class="px-6 py-4 font-bold text-gray-800">{{ $field['name'] }}</td>
                                        <td class="px-6 py-4 text-gray-600 leading-relaxed">{{ $field['description'] }}</td>
                                        <td class="px-6 py-4 text-center">
                                            @if($field['required'])
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Ya</span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Tidak</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        @media print {
            .sticky { position: relative !important; }
            button { display: none !important; }
        }
    </style>
    @endpush
</div>
