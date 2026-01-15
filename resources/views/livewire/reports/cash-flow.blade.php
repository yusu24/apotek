<div class="p-6 space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 leading-tight">
                Laporan Arus Kas
            </h2>
            <p class="text-sm text-gray-500 mt-1">Laporan Arus Kas Masuk & Keluar • Terupdate: {{ now()->format('H:i:s') }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-2 sm:gap-3 w-full md:w-auto">
             <button wire:click="setThisMonth" class="btn btn-secondary">
                Bulan Ini
            </button>
            <button wire:click="setLastMonth" class="btn btn-secondary">
                Bulan Lalu
            </button>
            <button wire:click="setThisYear" class="btn btn-secondary">
                Tahun Ini
            </button>
        </div>
    </div>

    {{-- Period Filter --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6 items-end">
             <div>
                <label class="block text-sm font-bold text-gray-700 mb-2 uppercase text-[10px]">Mulai Tanggal</label>
                <input type="date" wire:model.live="startDate" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 font-medium">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2 uppercase text-[10px]">Sampai Tanggal</label>
                <input type="date" wire:model.live="endDate" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 font-medium">
            </div>
             <div class="flex gap-2">
                  <a href="{{ route('pdf.cash-flow', ['startDate' => $startDate, 'endDate' => $endDate]) }}" target="_blank" class="btn btn-lg btn-primary bg-gray-900 hover:bg-black text-white flex items-center gap-2 justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    <span>Print / Download PDF</span>
                </a>
            </div>
        </div>
    </div>

    @if(!empty($data))
    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
        {{-- Operating --}}
        <div class="rounded-xl shadow-lg p-5 text-white transform hover:scale-[1.05] transition-all duration-300 border-b-4" style="background-color: #0e7490; border-color: #155e75;">
             <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-white/20 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <p class="text-[10px] font-bold uppercase opacity-80">Operasional</p>
            </div>
            <p class="text-xl font-bold">{{ format_accounting($data['net_cash_operating']) }}</p>
             <p class="text-[10px] text-white/60 mt-1 italic">Kas dari Operasional</p>
        </div>

        {{-- Investing --}}
        <div class="rounded-xl shadow-lg p-5 text-white transform hover:scale-[1.05] transition-all duration-300 border-b-4" style="background-color: #b45309; border-color: #92400e;">
             <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-white/20 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <p class="text-[10px] font-bold uppercase opacity-80">Investasi</p>
            </div>
            <p class="text-xl font-bold">{{ format_accounting($data['net_cash_investing']) }}</p>
             <p class="text-[10px] text-white/60 mt-1 italic">Kas dari Investasi</p>
        </div>

        {{-- Financing --}}
         <div class="rounded-xl shadow-lg p-5 text-white transform hover:scale-[1.05] transition-all duration-300 border-b-4" style="background-color: #4338ca; border-color: #3730a3;">
             <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-white/20 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <p class="text-[10px] font-bold uppercase opacity-80">Pendanaan</p>
            </div>
            <p class="text-xl font-bold">{{ format_accounting($data['net_cash_financing']) }}</p>
             <p class="text-[10px] text-white/60 mt-1 italic">Kas dari Pendanaan</p>
        </div>

        {{-- Net Increase --}}
        <div class="rounded-xl shadow-lg p-5 text-white transform hover:scale-[1.05] transition-all duration-300 border-b-4" style="background-color: {{ $data['net_increase'] >= 0 ? '#059669' : '#b91c1c' }}; border-color: {{ $data['net_increase'] >= 0 ? '#047857' : '#991b1b' }};">
             <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-white/20 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                </div>
                <p class="text-[10px] font-bold uppercase opacity-80">Kenaikan Bersih</p>
            </div>
            <p class="text-xl font-bold">{{ format_accounting($data['net_increase']) }}</p>
             <p class="text-[10px] text-white/60 mt-1 italic">Total Kenaikan/Penurunan</p>
        </div>

        {{-- Ending Balance --}}
         <div class="rounded-xl shadow-lg p-5 text-gray-800 bg-white transform hover:scale-[1.05] transition-all duration-300 border-b-4 border-gray-300">
             <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-gray-100 rounded-lg text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                </div>
                <p class="text-[10px] font-bold uppercase opacity-60">Saldo Akhir</p>
            </div>
            <p class="text-xl font-bold text-gray-900">{{ format_accounting($data['ending_balance']) }}</p>
             <p class="text-[10px] text-gray-500 mt-1 italic">Posisi Kas Akhir</p>
        </div>
    </div>
    
    {{-- Main Table --}}
    <div class="bg-white rounded-lg shadow-md overflow-hidden print:hidden">
        <div class="bg-gray-900 px-6 py-4">
            <h3 class="text-xl font-bold text-white uppercase">DETAIL ARUS KAS</h3>
             <p class="text-sm text-gray-300 mt-1 italic">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
        </div>

        <div class="p-6 space-y-6">
            {{-- Operating --}}
            <div>
                 <h4 class="text-base font-bold text-gray-800 uppercase mb-3 pb-2 border-b-2 border-gray-300">AKTIVITAS OPERASIONAL</h4>
                 <div class="space-y-2 ml-4">
                     {{-- Rows --}}
                     <div class="flex justify-between items-center py-2 hover:bg-gray-50 px-2 rounded">
                        <span class="text-sm text-gray-700">Penerimaan dari pelanggan</span>
                        <span class="text-sm font-bold text-gray-900">{{ format_accounting($data['receipts_from_customers']) }}</span>
                    </div>
                     <div class="flex justify-between items-center py-2 hover:bg-gray-50 px-2 rounded">
                        <span class="text-sm text-gray-700">Pembayaran ke pemasok</span>
                        <span class="text-sm font-bold text-gray-900">{{ format_accounting($data['payments_to_suppliers']) }}</span>
                    </div>
                     <div class="flex justify-between items-center py-2 hover:bg-gray-50 px-2 rounded">
                        <span class="text-sm text-gray-700">Pengeluaran operasional</span>
                        <span class="text-sm font-bold text-gray-900">{{ format_accounting($data['payments_for_expenses']) }}</span>
                    </div>
                     <div class="flex justify-between items-center py-2 hover:bg-gray-50 px-2 rounded">
                        <span class="text-sm text-gray-700">Pendapatan lainnya</span>
                        <span class="text-sm font-bold text-gray-900">{{ format_accounting($data['other_operating']) }}</span>
                    </div>
                 </div>
                 <div class="flex justify-between items-center mt-3 pt-3 ml-4 border-t border-gray-300">
                    <span class="text-base font-bold text-gray-800">Total Kas Bersih dari Operasional</span>
                    <span class="text-lg font-bold text-blue-700">{{ format_accounting($data['net_cash_operating']) }}</span>
                </div>
            </div>

            {{-- Investing --}}
            <div>
                 <h4 class="text-base font-bold text-gray-800 uppercase mb-3 pb-2 border-b-2 border-gray-300">AKTIVITAS INVESTASI</h4>
                 <div class="space-y-2 ml-4">
                     {{-- Rows --}}
                     <div class="flex justify-between items-center py-2 hover:bg-gray-50 px-2 rounded">
                        <span class="text-sm text-gray-700">Perolehan/Penjualan Aset</span>
                        <span class="text-sm font-bold text-gray-900">{{ format_accounting($data['sale_assets'] + $data['purchase_assets']) }}</span>
                    </div>
                     <div class="flex justify-between items-center py-2 hover:bg-gray-50 px-2 rounded">
                        <span class="text-sm text-gray-700">Investasi Lainnya</span>
                        <span class="text-sm font-bold text-gray-900">{{ format_accounting($data['other_investing']) }}</span>
                    </div>
                 </div>
                 <div class="flex justify-between items-center mt-3 pt-3 ml-4 border-t border-gray-300">
                    <span class="text-base font-bold text-gray-800">Total Kas Bersih dari Investasi</span>
                    <span class="text-lg font-bold text-amber-700">{{ format_accounting($data['net_cash_investing']) }}</span>
                </div>
            </div>

            {{-- Financing --}}
             <div>
                 <h4 class="text-base font-bold text-gray-800 uppercase mb-3 pb-2 border-b-2 border-gray-300">AKTIVITAS PENDANAAN</h4>
                 <div class="space-y-2 ml-4">
                     {{-- Rows --}}
                     <div class="flex justify-between items-center py-2 hover:bg-gray-50 px-2 rounded">
                        <span class="text-sm text-gray-700">Pinjaman</span>
                        <span class="text-sm font-bold text-gray-900">{{ format_accounting($data['loans']) }}</span>
                    </div>
                     <div class="flex justify-between items-center py-2 hover:bg-gray-50 px-2 rounded">
                        <span class="text-sm text-gray-700">Ekuitas/Modal</span>
                        <span class="text-sm font-bold text-gray-900">{{ format_accounting($data['equity']) }}</span>
                    </div>
                 </div>
                 <div class="flex justify-between items-center mt-3 pt-3 ml-4 border-t border-gray-300">
                    <span class="text-base font-bold text-gray-800">Total Kas Bersih dari Pendanaan</span>
                    <span class="text-lg font-bold text-indigo-700">{{ format_accounting($data['net_cash_financing']) }}</span>
                </div>
            </div>
            
            {{-- Summary --}}
            <div class="bg-gray-50 p-4 rounded-lg border-2 border-gray-200 mt-6">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-medium text-gray-600">Saldo Kas Awal</span>
                    <span class="text-base font-bold text-gray-800">{{ format_accounting($data['beginning_balance']) }}</span>
                </div>
                 <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-medium text-gray-600">Kenaikan/Penurunan Bersih</span>
                    <span class="text-base font-bold text-{{ $data['net_increase'] >= 0 ? 'green' : 'red' }}-700">{{ format_accounting($data['net_increase']) }}</span>
                </div>
                <div class="flex justify-between items-center pt-3 border-t-2 border-gray-300">
                    <span class="text-lg font-bold text-gray-900 uppercase">Saldo Kas Akhir</span>
                    <span class="text-xl font-bold text-gray-900">{{ format_accounting($data['ending_balance']) }}</span>
                </div>
            </div>

        </div>
    </div>
    @endif
    



    @php
    function format_accounting($number) {
        if ($number < 0) {
            return '( ' . number_format(abs($number), 0, ',', '.') . ' )';
        }
        return number_format($number, 0, ',', '.');
    }
    @endphp

</div>
