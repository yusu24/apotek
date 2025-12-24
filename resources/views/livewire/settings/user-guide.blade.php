<div class="p-8 min-h-screen bg-slate-50/50">
    <div class="max-w-screen-2xl mx-auto">
        <!-- Header Section -->
        <div class="relative overflow-hidden bg-white rounded-3xl border border-slate-200 shadow-sm p-8 mb-10">
            <div class="absolute top-0 right-0 -tr-24 w-96 h-96 bg-blue-50/50 rounded-full blur-3xl"></div>
            <div class="relative flex flex-col lg:flex-row justify-between items-start lg:items-center gap-8">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-black uppercase tracking-widest rounded-full">Support Center</span>
                        <span class="text-slate-300">/</span>
                        <span class="text-slate-400 text-xs font-bold uppercase tracking-widest">Documentation</span>
                    </div>
                    <h1 class="text-4xl font-black text-slate-900 tracking-tight">Pusat Panduan Aplikasi</h1>
                    <p class="mt-2 text-slate-500 font-medium text-lg max-w-2xl text-balance">
                        Telusuri dokumentasi lengkap penggunaan sistem apotek Anda. Temukan solusi dan langkah-langkah operasional di sini.
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row gap-4 w-full lg:w-auto">
                    <div class="relative flex-1 sm:w-80">
                        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari panduan..." class="w-full pl-12 pr-4 py-4 bg-slate-50 border-slate-200 rounded-2xl text-slate-900 placeholder:text-slate-400 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all font-bold">
                        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <a href="{{ route('guide.handbook') }}" class="inline-flex items-center justify-center px-8 py-4 border border-transparent text-base font-black rounded-2xl shadow-xl shadow-blue-100 text-white bg-blue-600 hover:bg-blue-700 transform hover:scale-[1.02] active:scale-95 transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Download Handbook
                    </a>
                </div>
            </div>
        </div>

        <!-- Documentation Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            @forelse($guides as $guide)
            @php
                $colorClasses = [
                    'blue' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-600', 'border' => 'border-blue-100', 'icon_bg' => 'bg-blue-600'],
                    'indigo' => ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-600', 'border' => 'border-indigo-100', 'icon_bg' => 'bg-indigo-600'],
                    'green' => ['bg' => 'bg-green-50', 'text' => 'text-green-600', 'border' => 'border-green-100', 'icon_bg' => 'bg-green-600'],
                    'orange' => ['bg' => 'bg-orange-50', 'text' => 'text-orange-600', 'border' => 'border-orange-100', 'icon_bg' => 'bg-orange-600'],
                    'purple' => ['bg' => 'bg-purple-50', 'text' => 'text-purple-600', 'border' => 'border-purple-100', 'icon_bg' => 'bg-purple-600'],
                    'rose' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-600', 'border' => 'border-rose-100', 'icon_bg' => 'bg-rose-600'],
                    'cyan' => ['bg' => 'bg-cyan-50', 'text' => 'text-cyan-600', 'border' => 'border-cyan-100', 'icon_bg' => 'bg-cyan-600'],
                    'slate' => ['bg' => 'bg-slate-50', 'text' => 'text-slate-600', 'border' => 'border-slate-100', 'icon_bg' => 'bg-slate-600'],
                ][$guide['color']] ?? ['bg' => 'bg-slate-50', 'text' => 'text-slate-600', 'border' => 'border-slate-100', 'icon_bg' => 'bg-slate-600'];
            @endphp
            <div class="group relative bg-white rounded-3xl border border-slate-200 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 flex flex-col h-full overflow-hidden">
                <!-- Category Badge -->
                <div class="absolute top-4 right-4">
                    <span class="px-3 py-1 bg-white/80 backdrop-blur-md border {{ $colorClasses['border'] }} {{ $colorClasses['text'] }} text-[10px] font-black uppercase tracking-widest rounded-full shadow-sm">
                        {{ $guide['category'] }}
                    </span>
                </div>

                <div class="p-8 flex-1">
                    <!-- Icon -->
                    <div class="w-16 h-16 rounded-2xl {{ $colorClasses['bg'] }} {{ $colorClasses['text'] }} flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-500 shadow-inner">
                        @if($guide['icon'] === 'chart-bar')
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        @elseif($guide['icon'] === 'beaker')
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.691.31a2 2 0 01-1.783 0l-.691-.31a6 6 0 00-3.86-.517l-2.387.477a2 2 0 00-1.022.547l-1.167 1.167a2 2 0 00.12 2.872l5.474 4.562l6.09-5.075a2 2 0 00.315-1.921l-.9-2.7a2 2 0 00-2.022-1.328l-2.7.9a2 2 0 00-1.328 2.022l.9 2.7z"></path></svg>
                        @elseif($guide['icon'] === 'shopping-cart')
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        @elseif($guide['icon'] === 'archive-box')
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                        @elseif($guide['icon'] === 'truck')
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0zM13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path></svg>
                        @elseif($guide['icon'] === 'user-circle')
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        @elseif($guide['icon'] === 'cog-6-tooth')
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        @else
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        @endif
                    </div>
                    
                    <h3 class="text-2xl font-black text-slate-800 tracking-tight leading-tight mb-3 group-hover:text-blue-600 transition-colors">{{ $guide['title'] }}</h3>
                    <p class="text-slate-500 leading-relaxed font-medium line-clamp-3">
                        {{ $guide['description'] }}
                    </p>
                </div>

                <!-- Footer Section -->
                <div class="px-8 pb-8 flex flex-col gap-4">
                    <div class="flex items-center text-slate-400 text-[10px] font-black uppercase tracking-widest border-t border-slate-50 pt-6">
                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Updated {{ $guide['updated_at'] }}
                    </div>
                    
                    <div class="flex gap-3">
                        <a href="{{ route('guide.detail', ['slug' => $guide['slug']]) }}" class="flex-1 inline-flex items-center justify-center px-6 py-3.5 bg-slate-900 hover:bg-blue-600 text-white text-xs font-black uppercase tracking-wider rounded-2xl transition-all duration-300 transform group-hover:shadow-lg group-hover:shadow-blue-200">
                            Buka Panduan
                        </a>
                        <a href="{{ route('guide.detail', ['slug' => $guide['slug']]) }}?print=1" target="_blank" class="p-3.5 border-2 border-slate-100 text-slate-400 hover:border-red-500 hover:text-red-500 rounded-2xl transition-all duration-300" title="Cetak ke PDF">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full py-20 bg-white rounded-3xl border border-dashed border-slate-300 flex flex-col items-center justify-center text-center">
                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-black text-slate-800">Tidak ada panduan ditemukan</h3>
                <p class="text-slate-500 font-medium mt-1">Coba kata kunci lain untuk hasil yang lebih baik.</p>
                <button wire:click="$set('search', '')" class="mt-6 text-blue-600 font-black uppercase text-xs tracking-widest hover:underline">Reset Pencarian</button>
            </div>
            @endforelse
        </div>
    </div>
</div>
