<div class="p-6 text-gray-800 font-sans">
    <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
        <!-- Mobile Back Button -->
        <div class="md:hidden col-span-1 mb-4">
            <a href="{{ route('guide.index') }}" class="flex items-center gap-2 text-gray-500 hover:text-gray-900 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                <span class="font-medium">Kembali</span>
            </a>
        </div>
            
            <!-- Sidebar Navigation -->
            <div class="md:col-span-3 hidden md:block">
                <div class="sticky top-6">
                    <a href="{{ route('guide.index') }}" class="flex items-center gap-2 text-gray-500 hover:text-gray-900 mb-6 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        <span class="font-medium">Kembali</span>
                    </a>

                    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                        <h3 class="text-sm font-bold text-gray-900 mb-4 uppercase tracking-wider">Navigasi</h3>
                        <nav class="space-y-1">
                            <a href="#overview" class="block px-3 py-2 text-sm font-medium text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-colors">Ringkasan</a>
                            @if(!empty($guide['screenshots']))
                            <a href="#screenshots" class="block px-3 py-2 text-sm font-medium text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-colors">Tampilan</a>
                            @endif
                            @if(!empty($guide['golden_rules']))
                            <a href="#rules" class="block px-3 py-2 text-sm font-medium text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-colors">Aturan Penting</a>
                            @endif
                            @if(!empty($guide['procedures']))
                            <a href="#procedures" class="block px-3 py-2 text-sm font-medium text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-colors">Prosedur</a>
                            @endif
                            @if(!empty($guide['form_fields']))
                            <a href="#forms" class="block px-3 py-2 text-sm font-medium text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-colors">Formulir</a>
                            @endif
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Main Content (Single Card) -->
            <div class="md:col-span-9 col-span-1">
                <div class="bg-white border border-gray-200 rounded-2xl p-8 shadow-sm">
                    
                    <!-- Title & Actions -->
                    <div class="flex items-start justify-between mb-8" id="overview">
                        <div>
                            <span class="inline-block px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-[10px] font-bold uppercase tracking-wider border border-blue-100 mb-3">
                                {{ $guide['category'] ?? 'Panduan' }}
                            </span>
                            <h1 class="text-xl md:text-3xl font-bold text-gray-900">{{ $guide['title'] }}</h1>
                            <p class="text-gray-500 mt-2 text-sm md:text-lg">{{ $guide['description'] }}</p>
                        </div>
                        <div class="flex gap-2">
                            @if($slug === 'user-manual')
                            <a href="{{ route('pdf.user-manual') }}" 
                               class="flex-shrink-0 px-4 py-2 bg-blue-900 text-white rounded-lg hover:bg-blue-800 shadow-md font-bold text-sm flex items-center justify-center gap-2 transition duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span>Export PDF</span>
                            </a>
                            @endif
                            <button onclick="window.print()" 
                                    class="flex-shrink-0 px-4 py-2 bg-gray-100 text-gray-700 text-sm font-bold rounded-xl hover:bg-gray-200 transition-colors flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                Print
                            </button>
                        </div>
                    </div>

                    <!-- Image & Info (Layout Split within card) -->
                    <div class="flex flex-col lg:flex-row gap-8 mb-10">
                        @if(file_exists(public_path('images/guide/' . ($guide['image'] ?? ''))))
                        <div class="flex-1">
                            <div class="rounded-xl overflow-hidden border border-gray-100 shadow-inner">
                                <img src="{{ asset('images/guide/' . $guide['image']) }}" class="w-full h-auto object-cover">
                            </div>
                        </div>
                        @endif

                        @if(!empty($guide['buttons']) || !empty($guide['sub_menus']))
                        <div class="lg:w-80 space-y-6">
                            @if(!empty($guide['buttons']))
                            <div class="bg-gray-50 rounded-xl p-5 border border-gray-100">
                                <h4 class="text-xs font-bold text-gray-900 uppercase tracking-wide mb-3">Tombol & Aksi</h4>
                                <div class="space-y-3">
                                    @foreach($guide['buttons'] as $btn)
                                    <div>
                                        <div class="font-bold text-gray-800 text-sm">{{ $btn['label'] }}</div>
                                        <div class="text-gray-500 text-xs mt-0.5">{{ $btn['func'] }}</div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            @if(!empty($guide['sub_menus']))
                            <div class="bg-gray-50 rounded-xl p-5 border border-gray-100">
                                <h4 class="text-xs font-bold text-gray-900 uppercase tracking-wide mb-3">Sub-Menu</h4>
                                <div class="space-y-3">
                                    @foreach($guide['sub_menus'] as $sub)
                                    <div>
                                        <div class="font-bold text-gray-800 text-sm">{{ $sub['name'] }}</div>
                                        <div class="text-gray-500 text-xs mt-0.5">{{ $sub['func'] }}</div>
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
                    <div id="screenshots" class="mb-10 SCROLL-MARGIN-TOP">
                        <h3 class="text-xl font-bold text-gray-900 mb-6 pb-2 border-b border-gray-100">Galeri Tampilan</h3>
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
                    <div id="rules" class="mb-10">
                        <div class="bg-blue-50 border border-blue-100 rounded-xl p-6">
                            <h3 class="text-lg font-bold text-blue-900 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Aturan Penting
                            </h3>
                            <div class="grid gap-3">
                                @foreach($guide['golden_rules'] as $index => $rule)
                                <div class="flex gap-4">
                                    <span class="flex-shrink-0 w-6 h-6 bg-blue-200 text-blue-800 rounded-full flex items-center justify-center text-xs font-bold">{{ $index + 1 }}</span>
                                    <span class="text-gray-700">{!! str_replace(['**', '##'], ['<strong>', '</strong>'], $rule) !!}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Procedures -->
                    @if(!empty($guide['procedures']))
                    <div id="procedures" class="mb-10">
                        <h3 class="text-xl font-bold text-gray-900 mb-6 pb-2 border-b border-gray-100">Prosedur Penggunaan</h3>
                        <div class="space-y-6">
                            @foreach($guide['procedures'] as $index => $proc)
                            <div class="flex gap-5">
                                <div class="flex-shrink-0 w-8 h-8 bg-gray-900 text-white rounded-lg flex items-center justify-center font-bold text-sm shadow-md">
                                    {{ $index + 1 }}
                                </div>
                                <div>
                                    <h4 class="text-lg font-bold text-gray-900 mb-1">{{ $proc['title'] }}</h4>
                                    <p class="text-gray-600 leading-relaxed">{!! str_replace(['**', '##'], ['<strong>', '</strong>'], $proc['desc']) !!}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Form Fields -->
                    @if(!empty($guide['form_fields']))
                    <div id="forms">
                        <h3 class="text-xl font-bold text-gray-900 mb-6 pb-2 border-b border-gray-100">Penjelasan Formulir</h3>
                        <div class="overflow-x-auto border border-gray-200 rounded-xl">
                            <table class="w-full text-sm text-left">
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


    @push('styles')
    <style>
        @media print {
            .sticky { position: relative !important; }
            button { display: none !important; }
        }
    </style>
    @endpush
</div>
