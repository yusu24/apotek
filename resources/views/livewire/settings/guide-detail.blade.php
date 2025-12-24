<div class="py-12 px-6">
    <div class="max-w-screen-2xl mx-auto">
        
        <!-- Header / Back Navigation -->
        <div class="mb-8 flex items-center justify-between no-print">
            <div class="flex items-center gap-4">
                <a href="{{ route('guide.index') }}" class="p-3 bg-white border border-slate-200 rounded-2xl text-slate-500 hover:text-blue-600 hover:border-blue-200 transition-all shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <div>
                    <h2 class="text-3xl font-black text-slate-900 tracking-tight uppercase">{{ $guide['title'] }}</h2>
                    <p class="text-slate-500 font-medium tracking-wide">Panduan penggunaan modul secara mendalam</p>
                </div>
            </div>
            <button onclick="window.print()" class="bg-slate-900 text-white px-6 py-3 rounded-2xl font-bold hover:bg-slate-800 transition-all flex items-center gap-2 shadow-xl">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak PDF
            </button>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-10">
            
            <!-- Visual Reference (Image) -->
            <div class="xl:col-span-2 space-y-8">
                <div class="bg-white border-2 border-slate-100 rounded-[2.5rem] overflow-hidden shadow-2xl">
                    <div class="bg-slate-50 px-8 py-4 border-b border-slate-100 flex items-center justify-between">
                        <span class="text-xs font-black text-slate-400 uppercase tracking-widest">Interface Preview</span>
                        <div class="flex gap-2">
                            <div class="w-3 h-3 rounded-full bg-red-100"></div>
                            <div class="w-3 h-3 rounded-full bg-yellow-100"></div>
                            <div class="w-3 h-3 rounded-full bg-green-100"></div>
                        </div>
                    </div>
                    @if(file_exists(public_path('images/guide/' . $guide['image'])))
                        <img src="{{ asset('images/guide/' . $guide['image']) }}" alt="{{ $guide['title'] }}" class="w-full">
                    @else
                        <div class="aspect-video bg-slate-50 flex items-center justify-center text-slate-300 font-bold italic">
                            Screenshot Not Available
                        </div>
                    @endif
                </div>

                <!-- Long Description / Overview -->
                <div class="bg-white p-10 rounded-[2.5rem] border border-slate-100 shadow-sm space-y-6">
                    <h3 class="text-2xl font-black text-slate-900 uppercase">Ringkasan Modul</h3>
                    <p class="text-lg text-slate-600 leading-relaxed">{{ $guide['description'] }}</p>
                </div>

                <!-- Procedures / Step-by-Step -->
                @if(!empty($guide['procedures']))
                <div class="bg-white p-10 rounded-[2.5rem] border border-slate-100 shadow-sm space-y-10">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center shadow-inner">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-2xl font-black text-slate-900 uppercase leading-none">Prosedur Standar</h3>
                                <p class="text-slate-400 text-sm font-bold mt-1 uppercase tracking-widest">Step-by-Step Guide</p>
                            </div>
                        </div>
                        <div class="hidden md:flex items-center gap-2 px-4 py-2 bg-slate-50 rounded-xl text-slate-400 text-xs font-bold uppercase">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            Latest Methods
                        </div>
                    </div>
                    
                    <div class="space-y-12 relative before:absolute before:left-[1.75rem] before:top-4 before:bottom-4 before:w-1 before:bg-gradient-to-b before:from-emerald-50 before:via-slate-100 before:to-emerald-50">
                        @foreach($guide['procedures'] as $index => $proc)
                        <div class="relative pl-20 group">
                            <div class="absolute left-0 top-0 w-14 h-14 bg-white border-[6px] border-slate-50 text-slate-900 rounded-2xl flex items-center justify-center font-black text-xl shadow-md z-10 group-hover:border-emerald-100 group-hover:text-emerald-600 transition-all duration-300 transform group-hover:scale-110">
                                {{ $index + 1 }}
                            </div>
                            <div class="bg-slate-50/50 p-6 rounded-3xl border border-transparent group-hover:border-emerald-100 group-hover:bg-white transition-all duration-300">
                                <h4 class="text-xl font-black text-slate-900 mb-2">{{ $proc['title'] }}</h4>
                                <p class="text-slate-600 leading-relaxed font-medium">{{ $proc['desc'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Form Fields Explanation -->
                @if(!empty($guide['form_fields']))
                <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
                    <div class="p-10 border-b border-slate-50 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center shadow-inner">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-2xl font-black text-slate-900 uppercase leading-none">Detail Formulir</h3>
                                <p class="text-slate-400 text-sm font-bold mt-1 uppercase tracking-widest">Input & Data Logic</p>
                            </div>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/80">
                                    <th class="px-10 py-6 text-xs font-black text-slate-400 uppercase tracking-widest">Field / Input</th>
                                    <th class="px-10 py-6 text-xs font-black text-slate-400 uppercase tracking-widest">Keterangan Fungsi</th>
                                    <th class="px-10 py-6 text-xs font-black text-slate-400 uppercase tracking-widest text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach($guide['form_fields'] as $field)
                                <tr class="group hover:bg-blue-50/30 transition-all duration-200">
                                    <td class="px-10 py-8">
                                        <div class="flex flex-col">
                                            <span class="font-black text-slate-900 text-lg group-hover:text-blue-600 transition-colors">{{ $field['name'] }}</span>
                                            <span class="text-[10px] font-black text-slate-300 uppercase tracking-tighter mt-1 italic">System ID: {{ Str::slug($field['name']) }}</span>
                                        </div>
                                    </td>
                                    <td class="px-10 py-8 text-base text-slate-600 leading-relaxed font-medium">
                                        {{ $field['description'] }}
                                    </td>
                                    <td class="px-10 py-8 text-center">
                                        @if($field['required'])
                                            <span class="inline-flex items-center px-4 py-1.5 rounded-xl text-xs font-black bg-red-50 text-red-600 border border-red-100 shadow-sm">
                                                WAJIB
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-4 py-1.5 rounded-xl text-xs font-black bg-slate-100 text-slate-400 border border-slate-200 shadow-sm">
                                                OPSIONAL
                                            </span>
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

            <!-- Detailed Breakdowns -->
            <div class="space-y-8">
                
                <!-- Sub Menus Table -->
                <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
                    <div class="p-8 bg-blue-600">
                        <h4 class="text-lg font-black text-white uppercase tracking-tight">Daftar Sub-Menu</h4>
                        <p class="text-blue-100 text-xs">Menu terkait di dalam modul ini</p>
                    </div>
                    <div class="divide-y divide-slate-50">
                        @foreach($guide['sub_menus'] as $sub)
                        <div class="p-6 hover:bg-slate-50 transition-colors">
                            <h5 class="font-black text-slate-900 text-sm mb-1">{{ $sub['name'] }}</h5>
                            <p class="text-slate-500 text-xs leading-relaxed font-medium">{{ $sub['func'] }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Buttons Guide Table -->
                <div class="bg-slate-900 rounded-[2.5rem] shadow-xl overflow-hidden">
                    <div class="p-8 border-b border-slate-800">
                        <h4 class="text-lg font-black text-white uppercase tracking-tight">Fungsi Tombol</h4>
                        <p class="text-slate-400 text-xs">Penjelasan setiap tombol aksi</p>
                    </div>
                    <div class="divide-y divide-slate-800">
                        @foreach($guide['buttons'] as $btn)
                        <div class="p-6 flex items-start gap-4 hover:bg-slate-800 transition-colors">
                            <div class="p-2 bg-blue-600/20 text-blue-400 rounded-xl flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </div>
                            <div>
                                <h5 class="font-black text-white text-sm mb-1">{{ $btn['label'] }}</h5>
                                <p class="text-slate-400 text-xs leading-relaxed font-medium">{{ $btn['func'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Pro Tip Card -->
                <div class="p-8 bg-indigo-50 border-2 border-indigo-100 rounded-[2.5rem] space-y-4">
                    <div class="w-12 h-12 bg-indigo-600 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                    </div>
                    <div>
                        <h5 class="text-indigo-900 font-black uppercase text-xs tracking-widest mb-1">Tips Efisiensi</h5>
                        <p class="text-indigo-800 text-sm font-bold leading-snug">Gunakan tombol cetak untuk menyimpan panduan ini secara offline guna pelatihan karyawan baru.</p>
                    </div>
                </div>

            </div>
        </div>

    </div>

    @push('styles')
    <style>
        @media print {
            .no-print { display: none !important; }
            .grid { display: block !important; }
            .rounded-\[2\.5rem\] { border-radius: 1.5rem !important; }
            .xl\:col-span-2 { margin-bottom: 2rem; }
        }
    </style>
    @endpush
</div>
