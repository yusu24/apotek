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
                <h2 class="text-lg font-black text-slate-900 uppercase tracking-tight">{{ $guide['title'] }}</h2>
            </div>
            
            <div class="flex items-center gap-4">
                <button onclick="window.print()" class="px-5 py-2.5 bg-slate-900 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-xl hover:bg-blue-600 hover:shadow-xl hover:shadow-blue-200 transition-all active:scale-95 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Simpan PDF
                </button>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="max-w-[1600px] mx-auto px-6 mt-12 grid grid-cols-1 lg:grid-cols-12 gap-12">
        
        <!-- Left Sidebar Navigation -->
        <div class="lg:col-span-3">
            <div class="sticky top-32 space-y-8 no-print" x-data="{ activeSection: 'overview' }">
                <div>
                    <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.3em] mb-6 px-4">Navigasi Panduan</h3>
                    <nav class="space-y-1">
                        <a href="#overview" @click="activeSection = 'overview'" :class="activeSection === 'overview' ? 'bg-blue-600 text-white shadow-lg shadow-blue-100' : 'text-slate-500 hover:bg-white hover:text-slate-900'" class="flex items-center gap-3 px-4 py-3 rounded-2xl transition-all font-bold text-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                            Ringkasan Modul
                        </a>
                        <a href="#procedures" @click="activeSection = 'procedures'" :class="activeSection === 'procedures' ? 'bg-blue-600 text-white shadow-lg shadow-blue-100' : 'text-slate-500 hover:bg-white hover:text-slate-900'" class="flex items-center gap-3 px-4 py-3 rounded-2xl transition-all font-bold text-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                            Langkah Prosedur
                        </a>
                        <a href="#forms" @click="activeSection = 'forms'" :class="activeSection === 'forms' ? 'bg-blue-600 text-white shadow-lg shadow-blue-100' : 'text-slate-500 hover:bg-white hover:text-slate-900'" class="flex items-center gap-3 px-4 py-3 rounded-2xl transition-all font-bold text-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            Detail Metadata
                        </a>
                    </nav>
                </div>

                <!-- Help Card -->
                <div class="p-6 bg-gradient-to-br from-indigo-600 to-blue-700 rounded-[2rem] text-white shadow-2xl shadow-blue-200 relative overflow-hidden group">
                    <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/10 rounded-full blur-2xl group-hover:scale-150 transition-transform"></div>
                    <div class="relative z-10">
                        <h4 class="font-black uppercase tracking-widest text-xs mb-2">Butuh Bantuan?</h4>
                        <p class="text-blue-100 text-[11px] leading-relaxed mb-4">Tim IT Support kami siap membantu Anda kapan pun.</p>
                        <a href="#" class="inline-block py-2.5 px-4 bg-white text-blue-600 text-[10px] font-black uppercase tracking-widest rounded-xl hover:shadow-lg transition-all">Hubungi Admin</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Center Content Base -->
        <div class="lg:col-span-6 space-y-16">
            
            <!-- Magazine-style Summary Section -->
            <section id="overview" class="scroll-mt-32">
                <div class="bg-white rounded-[2.5rem] border border-slate-200/60 shadow-xl shadow-slate-200/50 overflow-hidden">
                    <div class="p-1 relative overflow-hidden">
                        <div class="bg-slate-50 rounded-[2.2rem] p-10">
                            <div class="flex items-center gap-3 mb-8">
                                <span class="w-10 h-1 bg-blue-600 rounded-full"></span>
                                <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.3em]">Ringkasan Modul</h3>
                            </div>
                            <h2 class="text-4xl font-extrabold text-slate-900 tracking-tight leading-tight mb-6">
                                {{ $guide['title'] }}
                            </h2>
                            <p class="text-xl text-slate-600 leading-relaxed font-medium">
                                {{ $guide['description'] }}
                            </p>
                        </div>
                    </div>
                    
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



