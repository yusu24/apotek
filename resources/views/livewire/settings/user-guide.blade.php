<div class="min-h-screen bg-[#f8fafc] pb-20">
    <!-- Premium Header -->
    <div class="bg-white border-b border-slate-200/60 sticky top-0 z-50 backdrop-blur-md bg-white/80">
        <div class="max-w-[1600px] mx-auto px-6 h-20 flex items-center justify-between">
            <div>
                <div class="flex items-center gap-2.5 mb-1">
                    <span class="w-7 h-0.5 bg-blue-600 rounded-full"></span>
                    <h1 class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.25em]">Knowledge Base</h1>
                </div>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight">Pusat Panduan Sistem</h2>
            </div>

            <!-- Glassmorphism Search -->
            <div class="w-full max-w-md relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-slate-400 group-focus-within:text-blue-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari modul atau prosedur..." 
                    class="block w-full pl-12 pr-4 py-3 bg-slate-100/50 border-none rounded-xl focus:ring-2 focus:ring-blue-600/20 focus:bg-white transition-all duration-300 text-sm font-medium placeholder:text-slate-400 shadow-inner">
            </div>
        </div>
    </div>

    <div class="max-w-[1600px] mx-auto px-6 mt-10">
        @if(count($guides) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($guides as $guide)
                    @php
                        $colorClass = match($guide['color']) {
                            'blue' => 'from-blue-600 to-indigo-600 shadow-blue-200',
                            'green' => 'from-emerald-600 to-teal-600 shadow-emerald-200',
                            'purple' => 'from-violet-600 to-purple-600 shadow-violet-200',
                            'orange' => 'from-orange-500 to-red-500 shadow-orange-200',
                            'pink' => 'from-rose-500 to-pink-600 shadow-rose-200',
                            'indigo' => 'from-indigo-600 to-blue-700 shadow-indigo-200',
                            default => 'from-slate-600 to-slate-800 shadow-slate-200',
                        };
                        
                        $bgLight = match($guide['color']) {
                            'blue' => 'bg-blue-50/50 text-blue-600',
                            'green' => 'bg-emerald-50/50 text-emerald-600',
                            'purple' => 'bg-violet-50/50 text-violet-600',
                            'orange' => 'bg-orange-50/50 text-orange-600',
                            'pink' => 'bg-rose-50/50 text-rose-600',
                            'indigo' => 'bg-indigo-50/50 text-indigo-600',
                            default => 'bg-slate-50/50 text-slate-600',
                        };
                    @endphp

                    <div class="group bg-white rounded-2xl border border-slate-200/60 shadow-[0_4px_20px_rgba(0,0,0,0.03)] hover:shadow-[0_12px_40px_rgba(0,0,0,0.08)] hover:-translate-y-1.5 transition-all duration-400 flex flex-col h-full overflow-hidden relative">
                        <!-- Abstract Background Blob -->
                        <div class="absolute -right-8 -top-8 w-28 h-28 bg-slate-50 rounded-full opacity-40 group-hover:scale-125 transition-transform duration-500"></div>

                        <div class="p-6 pb-3 relative z-10">
                            <!-- Category Badge -->
                            <div class="flex items-center justify-between mb-6">
                                <span class="px-2.5 py-1 {{ $bgLight }} text-[9px] font-bold uppercase tracking-wider rounded-lg border border-current/10">
                                    {{ $guide['category'] }}
                                </span>
                                <div class="w-9 h-9 rounded-xl bg-white shadow-sm border border-slate-100 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white group-hover:border-blue-600 transition-all duration-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        @if($guide['icon'] === 'chart-bar')
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        @elseif($guide['icon'] === 'box')
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        @elseif($guide['icon'] === 'shopping-cart')
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        @elseif($guide['icon'] === 'refresh')
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        @elseif($guide['icon'] === 'truck')
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1m-4 0h1m-5 1a1 1 0 001-1h1m-5 1v2a1 1 0 01-1 1h-1a1 1 0 01-1-1v-2a1 1 0 011-1h1"></path>
                                        @elseif($guide['icon'] === 'document-report')
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        @elseif($guide['icon'] === 'user')
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        @elseif($guide['icon'] === 'cog')
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37a1.724 1.724 0 002.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        @endif
                                    </svg>
                                </div>
                            </div>
                            
                            <h3 class="text-xl font-black text-slate-800 tracking-tight leading-tight mb-2.5 group-hover:text-blue-600 transition-colors uppercase">{{ $guide['title'] }}</h3>
                            <p class="text-slate-500 leading-relaxed font-medium line-clamp-2 text-sm">
                                {{ $guide['description'] }}
                            </p>
                        </div>

                        <!-- Footer -->
                        <div class="p-6 pt-3 flex flex-col gap-3.5 relative z-10 mt-auto">
                            <div class="flex items-center gap-2 text-[9px] font-bold text-slate-400 uppercase tracking-wider">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                {{ $guide['updated_at'] }}
                            </div>
                            
                            <div class="flex gap-2.5">
                                <a href="{{ route('guide.detail', $guide['slug']) }}" 
                                    class="flex-1 py-2.5 px-3 bg-gradient-to-r {{ $colorClass }} text-white text-[10px] font-black uppercase tracking-wider text-center shadow-md hover:brightness-110 active:scale-[0.98] transition-all rounded-xl flex items-center justify-center gap-1.5">
                                    View Guide
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                                </a>
                                <a href="{{ route('guide.detail', $guide['slug']) }}?print=true" target="_blank" 
                                    class="w-11 h-11 flex items-center justify-center bg-slate-50 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-all rounded-xl border border-slate-200/60">
                                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-3xl p-16 text-center border-2 border-dashed border-slate-200">
                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-5">
                    <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="text-2xl font-black text-slate-800 mb-2 uppercase tracking-tight">Tidak ada hasil</h3>
                <p class="text-slate-500 mb-7 font-medium">Coba gunakan kata kunci lain untuk pencarian Anda.</p>
                <button wire:click="$set('search', '')" class="px-7 py-3 bg-blue-600 text-white rounded-xl font-bold uppercase tracking-wider text-xs hover:bg-blue-700 hover:shadow-md transition-all">Reset Pencarian</button>
            </div>
        @endif
    </div>
</div>
