<div class="min-h-screen bg-[#f8fafc] pb-20 font-sans selection:bg-blue-100 selection:text-blue-700">
    <!-- Premium Sticky Header -->
    <div class="bg-white/80 backdrop-blur-xl border-b border-slate-200/60 sticky top-0 z-[100] no-print">
        <div class="max-w-[1600px] mx-auto px-6 h-20 flex items-center justify-between">
            <div class="flex items-center gap-6">
                <a href="{{ route('guide.index') }}" class="group flex items-center gap-2 text-slate-400 hover:text-blue-600 transition-all font-bold uppercase text-[10px] tracking-widest">
                    <div class="w-8 h-8 rounded-full border border-slate-200 flex items-center justify-center group-hover:border-blue-200 group-hover:bg-blue-50 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                    </div>
                    Kembali
                </a>
                <div class="h-6 w-px bg-slate-200"></div>
                <h2 class="text-base font-bold text-slate-900 tracking-tight">{{ $guide['title'] }}</h2>
            </div>
            
            <div class="flex items-center gap-4">
                <button onclick="window.print()" class="px-5 py-2.5 bg-blue-600 text-white text-[10px] font-black uppercase tracking-[0.15em] rounded-xl hover:bg-blue-700 hover:shadow-lg hover:shadow-blue-200 transition-all active:scale-95 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Simpan PDF
                </button>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="max-w-[1600px] mx-auto px-6 mt-10 grid grid-cols-1 lg:grid-cols-12 gap-10">
        
        <!-- Left Sidebar Navigation -->
        <div class="lg:col-span-3">
            <div class="sticky top-32 space-y-6 no-print" x-data="{ activeSection: 'overview' }">
                <div>
                    <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.25em] mb-5 px-3">Navigasi Panduan</h3>
                    <nav class="space-y-1.5">
                        <a href="#overview" @click="activeSection = 'overview'" :class="activeSection === 'overview' ? 'bg-blue-600 text-white shadow-md' : 'text-slate-600 hover:bg-white hover:text-slate-900 hover:shadow-sm'" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all font-semibold text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                            Ringkasan
                        </a>
                        <a href="#procedures" @click="activeSection = 'procedures'" :class="activeSection === 'procedures' ? 'bg-blue-600 text-white shadow-md' : 'text-slate-600 hover:bg-white hover:text-slate-900 hover:shadow-sm'" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all font-semibold text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                            Prosedur
                        </a>
                        <a href="#forms" @click="activeSection = 'forms'" :class="activeSection === 'forms' ? 'bg-blue-600 text-white shadow-md' : 'text-slate-600 hover:bg-white hover:text-slate-900 hover:shadow-sm'" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all font-semibold text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            Formulir
                        </a>
                    </nav>
                </div>

                <!-- Help Card -->
                <div class="p-5 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl text-white shadow-lg relative overflow-hidden">
                    <div class="absolute -right-3 -bottom-3 w-20 h-20 bg-white/10 rounded-full blur-xl"></div>
                    <div class="relative">
                        <h4 class="font-bold uppercase text-[10px] tracking-widest mb-1.5 opacity-90">Butuh Bantuan?</h4>
                        <p class="text-white/90 text-xs leading-relaxed mb-3">Tim IT Support siap membantu Anda.</p>
                        <a href="#" class="inline-block py-2 px-3.5 bg-white text-blue-600 text-[9px] font-bold uppercase tracking-wider rounded-lg hover:shadow-md transition-all">Hubungi Admin</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Center Content Base -->
        <div class="lg:col-span-6 space-y-10">
            
            <!-- Magazine-style Summary Section -->
            <section id="overview" class="scroll-mt-28">
                <div class="bg-white rounded-3xl border border-slate-200/60 shadow-lg overflow-hidden">
                    <div class="p-8">
                        <div class="flex items-center gap-2.5 mb-6">
                            <span class="w-8 h-0.5 bg-blue-600 rounded-full"></span>
                            <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.25em]">Ringkasan Modul</h3>
                        </div>
                        <h2 class="text-3xl font-black text-slate-900 tracking-tight leading-tight mb-4">
                            {{ $guide['title'] }}
                        </h2>
                        <p class="text-lg text-slate-600 leading-relaxed font-medium">
                            {{ $guide['description'] }}
                        </p>
                    </div>
                    
                    <!-- Golden Rules Widget -->
                    @if(!empty($guide['golden_rules']))
                    <div class="px-8 pb-8 pt-4 border-t border-slate-100">
                        <div class="relative">
                            <div class="flex items-center gap-2.5 mb-6">
                                <span class="px-2.5 py-1 bg-amber-50 text-amber-600 text-[9px] font-bold uppercase tracking-wider rounded-lg border border-amber-100">Golden Rules</span>
                                <h4 class="text-sm font-bold text-slate-900 tracking-tight">3 Kunci Utama</h4>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                @foreach($guide['golden_rules'] as $rule)
                                <div class="flex gap-3 p-4 rounded-2xl bg-slate-50/50 border border-slate-100 hover:border-blue-100 hover:bg-blue-50/30 transition-all">
                                    <div class="w-5 h-5 rounded-lg bg-blue-600 text-white flex-shrink-0 flex items-center justify-center mt-0.5">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <p class="text-sm font-medium text-slate-700 leading-relaxed">{!! str_replace(['**', '##'], ['<span class="text-slate-900 font-semibold">', '</span>'], $rule) !!}</p>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </section>

            <!-- Procedures Section -->
            @if(!empty($guide['procedures']))
            <section id="procedures" class="scroll-mt-28 space-y-6">
                <div class="flex items-center gap-3 px-1">
                    <div class="w-9 h-9 bg-blue-600 text-white rounded-xl flex items-center justify-center shadow-md">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                    </div>
                    <h3 class="text-xl font-black text-slate-900 tracking-tight uppercase">Prosedur Operasional</h3>
                </div>
                
                <div class="space-y-3">
                    @foreach($guide['procedures'] as $index => $proc)
                    <div class="group bg-white border border-slate-200/60 rounded-2xl p-6 hover:border-blue-300 hover:shadow-md transition-all duration-300">
                        <div class="flex gap-5">
                            <div class="flex-shrink-0">
                                <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-400 group-hover:bg-blue-600 group-hover:text-white flex items-center justify-center text-lg font-black transition-all">
                                    {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                                </div>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-base font-bold text-slate-900 mb-2 group-hover:text-blue-600 transition-colors uppercase tracking-tight">{{ $proc['title'] }}</h4>
                                <p class="text-sm text-slate-600 leading-relaxed font-medium">{!! str_replace(['**', '##'], ['<span class="text-slate-900 font-semibold">', '</span>'], $proc['desc']) !!}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </section>
            @endif

            <!-- Form Fields Section -->
            @if(!empty($guide['form_fields']))
            <section id="forms" class="scroll-mt-28 space-y-6">
                <div class="flex items-center gap-3 px-1">
                    <div class="w-9 h-9 bg-indigo-600 text-white rounded-xl flex items-center justify-center shadow-md">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </div>
                    <h3 class="text-xl font-black text-slate-900 tracking-tight uppercase">Detail Formulir</h3>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200/60 shadow-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr style="background-color: #1e293b !important;" class="text-white">
                                    <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-wider text-slate-300">Field</th>
                                    <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-wider text-slate-300">Keterangan</th>
                                    <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-wider text-center text-slate-300">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($guide['form_fields'] as $field)
                                <tr class="group hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <span class="text-sm font-bold text-slate-900 group-hover:text-indigo-600 transition-colors uppercase tracking-tight">{{ $field['name'] }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600 leading-relaxed font-medium">
                                        {{ $field['description'] }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($field['required'])
                                            <span class="px-3 py-1 bg-rose-50 text-rose-600 text-[9px] font-bold uppercase tracking-wider rounded-lg border border-rose-100">Wajib</span>
                                        @else
                                            <span class="px-3 py-1 bg-slate-100 text-slate-500 text-[9px] font-bold uppercase tracking-wider rounded-lg">Opsional</span>
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

        <!-- Right Sidebar (Metadata & Buttons) -->
        <div class="lg:col-span-3 space-y-6 no-print">
            
            <!-- Interface Snapshot -->
            <div class="bg-white p-1.5 rounded-2xl border border-slate-200/60 shadow-lg">
                <div class="aspect-[4/3] rounded-xl overflow-hidden border border-slate-100 bg-slate-50 relative group">
                    @if(file_exists(public_path('images/guide/' . $guide['image'])))
                        <img src="{{ asset('images/guide/' . $guide['image']) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @else
                        <div class="w-full h-full flex flex-col items-center justify-center text-slate-300">
                            <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span class="text-[9px] font-bold uppercase tracking-wider">No Preview</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Dark Action Panel -->
            <div style="background-color: #1e293b !important;" class="rounded-2xl shadow-lg overflow-hidden">
                <div class="px-5 py-4 border-b border-white/10">
                    <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Fungsi Tombol</h4>
                </div>
                <div class="p-2 space-y-1">
                    @foreach($guide['buttons'] as $btn)
                    <div class="px-3 py-2.5 rounded-xl hover:bg-white/5 transition-all flex items-start gap-3 group/btn">
                        <div class="w-7 h-7 rounded-lg bg-blue-600/10 text-blue-400 flex-shrink-0 flex items-center justify-center group-hover/btn:bg-blue-600 group-hover/btn:text-white transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                        <div>
                            <h5 class="text-white text-[11px] font-bold uppercase tracking-tight mb-0.5 group-hover/btn:text-blue-300 transition-colors">{{ $btn['label'] }}</h5>
                            <p class="text-slate-400 text-[10px] font-medium leading-relaxed">{{ $btn['func'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Sub Menus -->
            <div class="bg-white rounded-2xl border border-slate-200/60 shadow-lg overflow-hidden">
                <div class="px-5 py-4 bg-blue-600 text-white">
                    <h4 class="text-[10px] font-bold uppercase tracking-wider">Sub-Menu Terkait</h4>
                </div>
                <div class="p-2 space-y-1">
                    @foreach($guide['sub_menus'] as $sub)
                    <div class="px-3 py-2.5 rounded-xl hover:bg-slate-50 transition-colors group/menu">
                        <h5 class="text-xs font-bold text-slate-900 group-hover/menu:text-blue-600 transition-colors uppercase tracking-tight mb-0.5">{{ $sub['name'] }}</h5>
                        <p class="text-[11px] text-slate-600 font-medium leading-relaxed">{{ $sub['func'] }}</p>
                    </div>
                    @endforeach
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
            .rounded-2xl, .rounded-3xl { border-radius: 0.5rem !important; }
            section { page-break-inside: avoid; margin-bottom: 2rem !important; }
            body { background: white !important; }
            .shadow-sm, .shadow-md, .shadow-lg { box-shadow: none !important; border: 1px solid #e2e8f0 !important; }
        }
    </style>
    @endpush>
</div>
