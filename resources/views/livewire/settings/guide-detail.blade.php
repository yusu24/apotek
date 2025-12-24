<div class="py-12 px-6 lg:px-12 bg-slate-50/50 min-h-screen" x-data="{ activeSection: 'overview' }">
    <div class="max-w-screen-2xl mx-auto">
        
        <!-- Header / Back Navigation -->
        <div class="mb-12 flex flex-col md:flex-row md:items-center justify-between gap-6 no-print">
            <div class="flex items-center gap-6">
                <a href="{{ route('guide.index') }}" class="group p-4 bg-white border border-slate-200 rounded-3xl text-slate-400 hover:text-blue-600 hover:border-blue-200 hover:shadow-xl hover:shadow-blue-50 transition-all">
                    <svg class="w-6 h-6 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-[10px] font-black text-blue-600 uppercase tracking-widest">Dokumentasi Modul</span>
                    </div>
                    <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight uppercase">{{ $guide['title'] }}</h2>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <button onclick="window.print()" class="bg-white text-slate-900 border-2 border-slate-200 px-6 py-3.5 rounded-2xl font-bold hover:bg-slate-50 transition-all flex items-center gap-2 shadow-sm">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Simpan PDF
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-4 gap-12">
            
            <!-- Left Sticky Sidebar (Navigation) -->
            <div class="hidden xl:block">
                <div class="sticky top-24 space-y-8 no-print">
                    <nav class="space-y-1">
                        <p class="px-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-4">Navigasi</p>
                        <a href="#overview" @click="activeSection = 'overview'" :class="activeSection === 'overview' ? 'bg-blue-600 text-white shadow-lg shadow-blue-100' : 'text-slate-500 hover:bg-white hover:text-slate-900'" class="flex items-center gap-3 px-4 py-3 rounded-2xl font-bold transition-all decoration-0 flex items-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
                            Ringkasan
                        </a>
                        @if(!empty($guide['procedures']))
                        <a href="#procedures" @click="activeSection = 'procedures'" :class="activeSection === 'procedures' ? 'bg-blue-600 text-white shadow-lg shadow-blue-100' : 'text-slate-500 hover:bg-white hover:text-slate-900'" class="flex items-center gap-3 px-4 py-3 rounded-2xl font-bold transition-all decoration-0 flex items-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                            Prosedur
                        </a>
                        @endif
                        @if(!empty($guide['form_fields']))
                        <a href="#forms" @click="activeSection = 'forms'" :class="activeSection === 'forms' ? 'bg-blue-600 text-white shadow-lg shadow-blue-100' : 'text-slate-500 hover:bg-white hover:text-slate-900'" class="flex items-center gap-3 px-4 py-3 rounded-2xl font-bold transition-all decoration-0 flex items-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            Formulir
                        </a>
                        @endif
                    </nav>

                    <div class="p-8 bg-slate-900 rounded-3xl text-white shadow-xl relative overflow-hidden group">
                        <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-blue-600/10 rounded-full blur-2xl"></div>
                        <h4 class="text-xs font-bold uppercase tracking-widest mb-4 opacity-50">Butuh Bantuan?</h4>
                        <p class="text-sm font-medium leading-relaxed mb-6">Hubungi tim IT Support untuk bantuan teknis.</p>
                        <a href="#" class="inline-flex items-center text-xs font-bold uppercase tracking-widest text-blue-400 hover:text-blue-300">
                            Support Chat
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="xl:col-span-2 space-y-12">

                <!-- Golden Rules Widget -->
                @if(!empty($guide['golden_rules']))
                <div class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden no-print">
                    <div class="p-8 relative">
                        <div class="absolute right-6 top-6 opacity-[0.03] pointer-events-none">
                            <svg class="w-24 h-24 text-blue-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        </div>
                        <div class="relative z-10">
                            <div class="flex items-center gap-3 mb-8">
                                <span class="px-3 py-1 bg-blue-50 text-blue-600 text-[10px] font-bold uppercase tracking-widest rounded-lg border border-blue-100">Aturan Emas</span>
                                <h4 class="text-lg font-bold text-slate-900 uppercase tracking-tight">3 Kunci Utama Modul Ini</h4>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                                @foreach($guide['golden_rules'] as $rule)
                                <div class="flex gap-4">
                                    <div class="w-6 h-6 rounded-lg bg-blue-50 text-blue-600 flex-shrink-0 flex items-center justify-center">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <p class="text-sm font-medium text-slate-600 leading-relaxed">{!! str_replace(['**', '##'], ['<span class="text-slate-900 font-bold">', '</span>'], $rule) !!}</p>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                
                <!-- Interface Preview Section -->
                <section id="overview" class="scroll-mt-24 space-y-8">
                    <div class="bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-sm group">
                        <div class="bg-slate-50/50 px-8 py-4 border-b border-slate-100 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="flex gap-1.5">
                                    <div class="w-2.5 h-2.5 rounded-full bg-slate-200"></div>
                                    <div class="w-2.5 h-2.5 rounded-full bg-slate-200"></div>
                                    <div class="w-2.5 h-2.5 rounded-full bg-slate-200"></div>
                                </div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-4">Antarmuka Sistem</span>
                            </div>
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                Versi 2.1
                            </div>
                        </div>
                        <div class="p-4">
                            @if(file_exists(public_path('images/guide/' . $guide['image'])))
                                <div class="rounded-2xl overflow-hidden border border-slate-100">
                                    <img src="{{ asset('images/guide/' . $guide['image']) }}" alt="{{ $guide['title'] }}" class="w-full">
                                </div>
                            @else
                                <div class="aspect-video bg-slate-50 rounded-2xl flex items-center justify-center border-2 border-dashed border-slate-200">
                                    <div class="text-center">
                                        <svg class="w-10 h-10 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        <p class="text-slate-300 font-bold uppercase text-[9px] tracking-widest">Screenshot Belum Diupload</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="px-10 py-10 bg-white border-t border-slate-100">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="w-6 h-1 bg-blue-600 rounded-full"></span>
                                <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Fungsi Utama</h3>
                            </div>
                            <p class="text-xl text-slate-600 leading-relaxed font-medium">{{ $guide['description'] }}</p>
                        </div>
                    </div>
                </section>

                <!-- Procedures Section -->
                @if(!empty($guide['procedures']))
                <section id="procedures" class="scroll-mt-24 space-y-8">
                    <div class="flex items-center gap-4 px-2">
                        <div class="w-10 h-10 bg-blue-600 text-white rounded-xl flex items-center justify-center shadow-lg shadow-blue-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 uppercase tracking-tight">Prosedur Operasional</h3>
                    </div>
                    
                    <div class="space-y-4">
                        @foreach($guide['procedures'] as $index => $proc)
                        <div class="group bg-white border border-slate-200 rounded-3xl p-8 hover:border-blue-400 transition-all duration-300">
                            <div class="flex gap-8">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-50 text-slate-300 group-hover:bg-blue-600 group-hover:text-white flex items-center justify-center text-xl font-black transition-all">
                                        {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-lg font-bold text-slate-900 mb-2 group-hover:text-blue-600 transition-colors uppercase tracking-tight">{{ $proc['title'] }}</h4>
                                    <p class="text-base text-slate-500 leading-relaxed font-medium">{!! str_replace(['**', '##'], ['<b class="text-slate-900">', '</b>'], $proc['desc']) !!}</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </section>
                @endif

                <!-- Form Fields Section -->
                @if(!empty($guide['form_fields']))
                <section id="forms" class="scroll-mt-24 space-y-8">
                    <div class="flex items-center gap-4 px-2">
                        <div class="w-10 h-10 bg-indigo-600 text-white rounded-xl flex items-center justify-center shadow-lg shadow-indigo-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 uppercase tracking-tight">Detail Metadata Formulir</h3>
                    </div>

                    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-[#0f172a] text-white">
                                        <th class="px-8 py-5 text-[10px] font-bold uppercase tracking-widest text-slate-400">Field</th>
                                        <th class="px-8 py-5 text-[10px] font-bold uppercase tracking-widest text-slate-400">Keterangan Pengisian</th>
                                        <th class="px-8 py-5 text-[10px] font-bold uppercase tracking-widest text-center text-slate-400">Sifat</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($guide['form_fields'] as $field)
                                    <tr class="group hover:bg-slate-50/50 transition-all duration-200">
                                        <td class="px-8 py-6">
                                            <span class="font-bold text-slate-900 group-hover:text-indigo-600 transition-colors uppercase text-sm tracking-tight">{{ $field['name'] }}</span>
                                        </td>
                                        <td class="px-8 py-6 text-sm text-slate-500 leading-relaxed font-medium">
                                            {{ $field['description'] }}
                                        </td>
                                        <td class="px-8 py-6 text-center">
                                            @if($field['required'])
                                                <span class="px-3 py-1 bg-rose-50 text-rose-600 text-[9px] font-bold uppercase tracking-widest rounded-lg border border-rose-100 shadow-sm">Wajib</span>
                                            @else
                                                <span class="px-3 py-1 bg-slate-100 text-slate-400 text-[9px] font-bold uppercase tracking-widest rounded-lg">Opsional</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
                @endif
            </div>

            <!-- Right Sidebar (Auxiliary Info) -->
            <div class="space-y-6">
                
                <!-- Sub Menus Card -->
                <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden no-print">
                    <div class="p-6 bg-blue-600 text-white">
                        <h4 class="text-sm font-bold uppercase tracking-widest">Sub-Menu Terkait</h4>
                    </div>
                    <div class="p-3 divide-y divide-slate-50">
                        @foreach($guide['sub_menus'] as $sub)
                        <div class="p-4 group">
                            <h5 class="font-bold text-slate-900 text-xs mb-1 group-hover:text-blue-600 transition-colors uppercase">{{ $sub['name'] }}</h5>
                            <p class="text-slate-500 text-[11px] leading-relaxed font-medium">{{ $sub['func'] }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Action Buttons Card -->
                <div class="bg-[#0f172a] rounded-3xl shadow-xl overflow-hidden no-print">
                    <div class="p-6 border-b border-white/5">
                        <h4 class="text-xs font-bold text-white uppercase tracking-widest">Fungsi Tombol</h4>
                    </div>
                    <div class="p-3">
                        @foreach($guide['buttons'] as $btn)
                        <div class="p-4 flex items-start gap-4 rounded-2xl hover:bg-white/5 transition-colors group">
                            <div class="p-2 bg-blue-600 text-white rounded-lg flex-shrink-0">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </div>
                            <div>
                                <h5 class="font-bold text-white text-[11px] mb-1 uppercase tracking-tight group-hover:text-blue-400 transition-colors">{{ $btn['label'] }}</h5>
                                <p class="text-slate-400 text-[10px] leading-relaxed font-medium">{{ $btn['func'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Pro Tip Card -->
                <div class="p-8 bg-amber-50 border border-amber-100 rounded-3xl relative overflow-hidden group no-print">
                    <div class="relative z-10">
                        <div class="w-10 h-10 bg-amber-500 text-white rounded-xl flex items-center justify-center shadow-lg shadow-amber-200 mb-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                        </div>
                        <h5 class="text-amber-900 font-bold uppercase text-[10px] tracking-widest mb-1">Tips Efisiensi</h5>
                        <p class="text-amber-800 text-sm font-medium leading-relaxed">Gunakan fitur cetak untuk dokumentasi fisik bagi karyawan baru Anda.</p>
                    </div>
                </div>

            </div>
        </div>

    </div>

    @push('styles')
    <style>
        html { scroll-behavior: smooth; }
        @media print {
            .no-print { display: none !important; }
            .grid { display: block !important; }
            .rounded-3xl { border-radius: 0.75rem !important; }
            section { page-break-inside: avoid; margin-bottom: 3rem !important; }
            body { background: white !important; }
            .py-12 { padding-top: 0 !important; }
            .shadow-sm, .shadow-lg, .shadow-xl { shadow: none !important; border: 1px solid #e2e8f0 !important; }
        }
    </style>
    @endpush
</div>



