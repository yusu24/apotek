<div class="p-6 min-h-screen bg-gray-50/50">
    <div class="max-w-screen-2xl mx-auto">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">User Guides</h1>
                <p class="mt-1 text-slate-500 font-medium tracking-wide">Browse documentation and download guides as PDF</p>
            </div>
            <a href="{{ route('guide.handbook') }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-bold rounded-xl shadow-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 transform hover:scale-[1.02]">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Download Complete Handbook (PDF)
            </a>
        </div>

        <!-- Documentation Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($guides as $guide)
            @php
                $colorClasses = [
                    'blue' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-600'],
                    'indigo' => ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-600'],
                    'green' => ['bg' => 'bg-green-50', 'text' => 'text-green-600'],
                    'orange' => ['bg' => 'bg-orange-50', 'text' => 'text-orange-600'],
                    'purple' => ['bg' => 'bg-purple-50', 'text' => 'text-purple-600'],
                    'rose' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-600'],
                    'cyan' => ['bg' => 'bg-cyan-50', 'text' => 'text-cyan-600'],
                    'slate' => ['bg' => 'bg-slate-50', 'text' => 'text-slate-600'],
                ][$guide['color']] ?? ['bg' => 'bg-slate-50', 'text' => 'text-slate-600'];
            @endphp
            <div class="group bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col h-full overflow-hidden">
                <div class="p-6 flex-1">
                    <!-- Icon & Title -->
                    <div class="flex items-start gap-4 mb-4">
                        <div class="p-3 rounded-lg {{ $colorClasses['bg'] }} {{ $colorClasses['text'] }} group-hover:scale-110 transition-transform duration-300">
                            @if($guide['icon'] === 'chart-bar')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                            @elseif($guide['icon'] === 'beaker')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.691.31a2 2 0 01-1.783 0l-.691-.31a6 6 0 00-3.86-.517l-2.387.477a2 2 0 00-1.022.547l-1.167 1.167a2 2 0 00.12 2.872l5.474 4.562l6.09-5.075a2 2 0 00.315-1.921l-.9-2.7a2 2 0 00-2.022-1.328l-2.7.9a2 2 0 00-1.328 2.022l.9 2.7z"></path></svg>
                            @elseif($guide['icon'] === 'shopping-cart')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            @elseif($guide['icon'] === 'archive-box')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                            @elseif($guide['icon'] === 'truck')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path></svg>
                            @elseif($guide['icon'] === 'user-circle')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            @elseif($guide['icon'] === 'cog-6-tooth')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            @else
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            @endif
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-slate-800 tracking-tight">{{ $guide['title'] }}</h3>
                            <div class="flex items-center mt-2 text-slate-400 text-xs font-semibold uppercase tracking-wider">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Updated {{ $guide['updated_at'] }}
                            </div>
                        </div>
                    </div>
                    
                    <p class="text-slate-600 leading-relaxed text-sm">
                        {{ $guide['description'] }}
                    </p>
                </div>

                <!-- Footer Buttons -->
                <div class="p-6 pt-0 border-t border-slate-50 mt-auto bg-slate-50/30 flex gap-3">
                    <a href="{{ route('guide.detail', ['slug' => $guide['slug']]) }}" class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-lg transition-colors duration-200">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        View Guide
                    </a>
                    <a href="{{ route('guide.detail', ['slug' => $guide['slug']]) }}?print=1" target="_blank" class="flex-1 inline-flex items-center justify-center px-4 py-2 border-2 border-red-100 text-red-600 hover:border-red-600 hover:bg-red-600 hover:text-white text-sm font-bold rounded-lg transition-all duration-200">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        PDF
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
