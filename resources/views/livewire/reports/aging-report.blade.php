<div class="p-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <div class="flex items-center gap-3">
                <h2 class="text-xl md:text-2xl font-bold text-gray-800">Laporan Umur Hutang &amp; Piutang</h2>
                <a href="{{ route('guide.formulas', ['tab' => 'aging']) }}" wire:navigate class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200 transition-colors shadow-2xs" title="Pelajari rumus perhitungan umur & sisa hutang/piutang">
                    <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    <span>Rumus Perhitungan</span>
                </a>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-2 sm:gap-3 w-full md:w-auto">
            @can('export aging report')
            <div class="relative btn-export-dropdown" x-data="{ open: false }" @click.outside="open = false">
                <button @click="open = !open" class="btn btn-export-excel" title="Export">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span class="hidden sm:inline ml-1">Export</span>
                    <svg class="w-3 h-3 ml-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div class="dropdown-menu" x-show="open" x-cloak style="display:none">
                    <a href="{{ route('excel.aging-report', ['showPaid' => $showPaid]) }}" target="_blank" @click="open = false" class="dropdown-item">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20" class="text-green-600">
                            <path d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"/>
                        </svg>
                        Excel (.xlsx)
                    </a>
                    <a href="{{ route('pdf.aging-report', ['type' => $type, 'showPaid' => $showPaid]) }}" target="_blank" @click="open = false" class="dropdown-item">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20" class="text-red-600">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                        </svg>
                        PDF (.pdf)
                    </a>
                </div>
            </div>
            @else
            <a href="{{ route('pdf.aging-report', ['type' => $type, 'showPaid' => $showPaid]) }}" target="_blank" class="btn btn-export-pdf" title="Export PDF">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                <span class="hidden sm:inline">Export PDF</span>
            </a>
            @endcan
        </div>
    </div>


    {{-- Main Type Tabs --}}
    <div class="mb-6 border-b border-gray-200">
        <nav class="-mb-px flex space-x-8">
            <button wire:click="setType('ap')" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ $type === 'ap' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Hutang Supplier (AP)
            </button>
            <button wire:click="setType('ar')" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ $type === 'ar' ? 'border-amber-500 text-amber-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Piutang Customer (AR)
            </button>
        </nav>
    </div>

    @if($reportData)
    @php
        $summary = $reportData['summary'] ?? [];
        // Accurate color hex sequence from image: Purple, Light Green, Dark, Blue, Green, Brown
        $colorSequence = [
            'all'   => ['hex' => '#bf50bc', 'label' => 'Semua'], 
            '0-7'   => ['hex' => '#d97706', 'label' => '0-7'],   
            '8-15'  => ['hex' => '#534a78', 'label' => '8-15'],  
            '16-30' => ['hex' => '#3182f7', 'label' => '16-30'], 
            '31-45' => ['hex' => '#2da01d', 'label' => '31-45'], 
            '45+'   => ['hex' => '#9f6941', 'label' => '45+'],   
        ];
    @endphp

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
        @foreach($colorSequence as $key => $config)
            @php
                $isActive = ($activeTab === $key);
            @endphp
            <div class="bg-white dark:bg-gray-800 rounded-2xl border p-4 cursor-pointer transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md flex flex-col justify-between {{ $isActive ? 'shadow-md border-gray-400 dark:border-gray-500 ring-2 ring-blue-500/20' : 'shadow-sm border-slate-100 dark:border-gray-700' }}"
                wire:click="setActiveTab('{{ $key }}')">
                <div>
                    <div class="flex items-center justify-between gap-2 mb-2">
                        <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider truncate">{{ $config['label'] }}{{ $key !== 'all' ? ' Hari' : '' }}</span>
                        <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background-color: {{ $config['hex'] }};"></span>
                    </div>
                    <p class="text-base lg:text-lg font-bold text-gray-900 dark:text-white tracking-tight truncate" title="Rp {{ number_format($summary[$key === 'all' ? 'total' : $key] ?? 0, 0, ',', '.') }}">
                        Rp {{ number_format($summary[$key === 'all' ? 'total' : $key] ?? 0, 0, ',', '.') }}
                    </p>
                </div>
                <div class="w-full h-1 bg-gray-100 dark:bg-gray-700 rounded-full mt-3 overflow-hidden">
                    <div class="h-full rounded-full" style="width: 100%; background-color: {{ $config['hex'] }};"></div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Transaction List Card --}}
    <div class="bg-white rounded-lg overflow-hidden border border-gray-100">
        <div class="flex justify-between items-center px-6 py-3 bg-gray-50 border-b border-gray-200">
            <h3 class="text-sm font-bold text-gray-700 uppercase tracking-tight">
                Detail Transaksi: {{ $activeTab === 'all' ? 'Semua Periode' : $activeTab . ' Hari' }}
            </h3>
             <div class="flex items-center">
                <input type="checkbox" wire:model.live="showPaid" id="showPaid" class="rounded border-gray-300 text-green-600 shadow-sm focus:ring-green-500">
                <label for="showPaid" class="ml-2 text-xs text-gray-500 cursor-pointer">Tampilkan Data Lunas</label>
            </div>
        </div>

        <!-- Filters Bar -->
        <div class="p-4 border-b bg-gray-50 flex flex-row items-center gap-4">

            <!-- Search Input -->
            <div class="flex-1 max-w-xs">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Cari</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    <input wire:model.live.debounce.300ms="search" 
                        type="text" placeholder="Cari {{ $type === 'ap' ? 'Supplier / No. Surat Jalan' : 'Customer / No. Invoice' }}..." 
                        class="w-full pl-10 pr-4 rounded-lg border-gray-300 text-sm py-2 font-medium focus:ring-2 focus:ring-blue-500 transition-all bg-white shadow-sm">
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-blue-950 text-white font-semibold uppercase text-xs tracking-wider border-b border-blue-900">
                    <tr>
                        <th class="px-4 py-3 text-left">
                            {{ $type === 'ap' ? 'Supplier' : 'Customer' }}
                        </th>
                        <th class="px-4 py-3 text-left">
                            {{ $type === 'ap' ? 'No. Surat Jalan' : 'No. Invoice' }}
                        </th>
                        <th class="px-4 py-3 text-left">Tanggal</th>
                        <th class="px-4 py-3 text-left">Jatuh Tempo</th>
                        <th class="px-4 py-3 text-center">Umur (Hari)</th>
                        @if($type === 'ar')
                             <th class="px-4 py-3 text-center">Status Jatuh Tempo</th>
                        @endif
                        <th class="px-4 py-3 text-right">Total Tagihan</th>
                        <th class="px-4 py-3 text-right">Sisa {{ $type === 'ap' ? 'Hutang' : 'Piutang' }}</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($paginatedItems as $item)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                            {{ $type === 'ap' ? ($item['supplier'] ?? '-') : ($item['customer'] ?? '-') }}
                            @if($type === 'ar' && isset($item['customer_phone']))
                                <span class="text-xs text-gray-500 font-normal ml-1">({{ $item['customer_phone'] }})</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-mono">
                            <button type="button" 
                                    wire:click="openDetailModal({{ $item['id'] }})"
                                    class="inline-flex items-center gap-1.5 text-blue-600 hover:text-blue-800 font-medium hover:underline text-left cursor-pointer" 
                                    title="Klik untuk melihat detail surat jalan / transaksi">
                                <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span>{{ $item['invoice_number'] }}</span>
                            </button>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($item['date'])->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                            {{ $item['due_date'] && $item['due_date'] !== '-' ? \Carbon\Carbon::parse($item['due_date'])->format('d/m/Y') : '-' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-center">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 rounded-full 
                                {{ $item['age'] > 90 ? 'bg-red-100 text-red-800' : 
                                   ($item['age'] > 60 ? 'bg-yellow-100 text-yellow-800' : 
                                   ($item['age'] > 30 ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800')) }}">
                                {{ $item['age'] }} Hari
                            </span>
                        </td>
                        @if($type === 'ar')
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-center">
                            @if($item['status'] === 'paid' || $item['outstanding'] <= 0)
                                <span class="px-2 inline-flex text-xs leading-5 rounded-full bg-green-100 text-green-800">
                                    LUNAS
                                </span>
                            @elseif(isset($item['days_remaining']))
                                @if($item['days_remaining'] < 0)
                                    <span class="px-2 inline-flex text-xs leading-5 rounded-full bg-red-100 text-red-800">
                                        Telat {{ abs($item['days_remaining']) }} Hari
                                    </span>
                                @elseif($item['days_remaining'] == 0)
                                    <span class="px-2 inline-flex text-xs leading-5 rounded-full bg-orange-100 text-orange-800">
                                        Jatuh Tempo Hari Ini
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 rounded-full bg-green-100 text-green-800">
                                        {{ $item['days_remaining'] }} Hari Lagi
                                    </span>
                                @endif
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        @endif
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-500 font-mono">Rp {{ number_format($item['total_amount'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-900 font-mono">
                            @if($item['outstanding'] <= 0)
                                <span class="text-green-600 font-medium">LUNAS</span>
                            @else
                                Rp {{ number_format($item['outstanding'], 0, ',', '.') }}
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <!-- Tombol Lihat Detail Transaksi / Surat Jalan -->
                                <button type="button"
                                        wire:click="openDetailModal({{ $item['id'] }})"
                                        class="p-1.5 text-blue-600 hover:text-blue-800 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition-colors border border-blue-200 dark:border-blue-800 shadow-2xs cursor-pointer"
                                        title="Lihat Detail Transaksi / Surat Jalan">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>

                                @if($item['outstanding'] > 0)
                                    @php
                                        $entityName = ($type === 'ap') ? ($item['supplier'] ?? 'Supplier') : ($item['customer'] ?? 'Customer');
                                    @endphp
                                    <button wire:click="openPaymentModal({{ $item['id'] }}, {{ $item['outstanding'] }}, '{{ addslashes($entityName) }}')"
                                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150 shadow-sm cursor-pointer">
                                        Bayar
                                    </button>
                                @else
                                    <span class="text-xs text-green-600 inline-flex items-center">
                                        <svg class="w-4 h-4 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        Lunas
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                        <x-empty-table colspan="{{ $type === 'ar' ? 9 : 8 }}" message="Tidak ada data {{ $type === 'ap' ? 'hutang' : 'piutang' }} pada kategori ini." />
                    @endforelse
                </tbody>
            </table>
        </div>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            @include('components.custom-pagination', ['items' => $paginatedItems])
        </div>
    </div>
    @endif

    {{-- Detail Modal (Surat Jalan & Transaksi) --}}
    @if($showDetailModal)
    <div wire:key="detail-modal-{{ $selectedDetailId }}" class="fixed inset-0 z-[60] overflow-y-auto" aria-labelledby="detail-modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true" wire:click="closeDetailModal"></div>

            <div class="relative inline-block align-middle bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-3xl sm:w-full border border-gray-100 animate-fade-in-up">
                
                @if($type === 'ap' && $detailReceipt)
                    <!-- AP (Hutang Supplier / Surat Jalan Penerimaan Barang) -->
                    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-blue-50/40">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-blue-100 rounded-xl text-blue-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900" id="detail-modal-title">
                                    Detail Surat Jalan Penerimaan Barang
                                </h3>
                                <p class="text-xs text-gray-500 font-mono">No. Surat Jalan: {{ $detailReceipt->delivery_note_number }}</p>
                            </div>
                        </div>
                        <button type="button" wire:click="closeDetailModal" class="text-gray-400 hover:text-gray-600 transition-colors p-1.5 rounded-lg hover:bg-gray-200/50 cursor-pointer">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div class="p-6 space-y-6 max-h-[75vh] overflow-y-auto">
                        <!-- Summary Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-gray-50/80 p-4 rounded-xl border border-gray-100">
                            <div>
                                <span class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider block mb-1">Supplier</span>
                                <p class="text-sm font-bold text-gray-900">{{ $detailReceipt->purchaseOrder?->supplier?->name ?? 'Supplier Tidak Diketahui' }}</p>
                                @if($detailReceipt->purchaseOrder)
                                    <span class="text-xs text-blue-600 font-medium mt-0.5 inline-flex items-center gap-1">
                                        PO: {{ $detailReceipt->purchaseOrder->po_number }}
                                    </span>
                                @endif
                            </div>
                            <div>
                                <span class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider block mb-1">Tanggal Transaksi</span>
                                <p class="text-xs text-gray-700">Diterima: <span class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($detailReceipt->received_date)->format('d M Y') }}</span></p>
                                <p class="text-xs text-gray-700 mt-0.5">Jatuh Tempo: 
                                    <span class="font-medium {{ $detailReceipt->due_date && \Carbon\Carbon::parse($detailReceipt->due_date)->isPast() && ($detailReceipt->total_amount - $detailReceipt->paid_amount > 0) ? 'text-red-600 font-bold' : 'text-gray-900' }}">
                                        {{ $detailReceipt->due_date ? \Carbon\Carbon::parse($detailReceipt->due_date)->format('d M Y') : '-' }}
                                    </span>
                                </p>
                            </div>
                            <div class="text-left md:text-right">
                                <span class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider block mb-1">Status & Hutang</span>
                                <div class="flex flex-col md:items-end gap-1">
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-{{ $detailReceipt->payment_status_color }}-100 text-{{ $detailReceipt->payment_status_color }}-800 uppercase tracking-wider">
                                        {{ $detailReceipt->payment_status_label }}
                                    </span>
                                    <p class="text-xs text-gray-500">Total: Rp {{ number_format($detailReceipt->total_amount, 0, ',', '.') }}</p>
                                    <p class="text-sm font-extrabold text-red-600">Sisa: Rp {{ number_format(max(0, $detailReceipt->total_amount - $detailReceipt->paid_amount), 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Daftar Item Surat Jalan -->
                        <div>
                            <div class="mb-2.5 flex items-center justify-between">
                                <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider flex items-center gap-2">
                                    <span class="w-1.5 h-3.5 bg-blue-600 rounded-full"></span>
                                    Rincian Barang yang Diterima
                                </h4>
                                <span class="text-xs text-gray-400 font-normal">{{ $detailReceipt->items->count() }} macam item</span>
                            </div>

                            <div class="border border-gray-200 rounded-xl overflow-hidden shadow-2xs">
                                <table class="min-w-full divide-y divide-gray-200 text-xs">
                                    <thead class="bg-gray-50 text-gray-600 font-semibold uppercase">
                                        <tr>
                                            <th class="px-3.5 py-2.5 text-left">No</th>
                                            <th class="px-3.5 py-2.5 text-left">Nama Produk</th>
                                            <th class="px-3.5 py-2.5 text-center">Qty</th>
                                            <th class="px-3.5 py-2.5 text-center">Satuan</th>
                                            <th class="px-3.5 py-2.5 text-center">Batch No</th>
                                            <th class="px-3.5 py-2.5 text-center">Kadaluarsa</th>
                                            <th class="px-3.5 py-2.5 text-right">Harga Beli</th>
                                            <th class="px-3.5 py-2.5 text-right">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 bg-white">
                                        @forelse($detailReceipt->items as $idx => $item)
                                            <tr class="hover:bg-blue-50/20 transition-colors">
                                                <td class="px-3.5 py-2.5 text-gray-400 text-center">{{ $idx + 1 }}</td>
                                                <td class="px-3.5 py-2.5 font-medium text-gray-900">{{ $item->product?->name ?? 'Produk Dihapus' }}</td>
                                                <td class="px-3.5 py-2.5 text-center font-bold text-gray-800">{{ number_format($item->qty_received, 0, ',', '.') }}</td>
                                                <td class="px-3.5 py-2.5 text-center">
                                                    <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-600 text-[10px] font-semibold uppercase">
                                                        {{ $item->unit?->name ?? ($item->product?->unit?->name ?? '-') }}
                                                    </span>
                                                </td>
                                                <td class="px-3.5 py-2.5 text-center font-mono text-gray-500">{{ $item->batch_no ?? '-' }}</td>
                                                <td class="px-3.5 py-2.5 text-center text-gray-600">
                                                    {{ $item->expired_date ? \Carbon\Carbon::parse($item->expired_date)->format('d/m/Y') : '-' }}
                                                </td>
                                                <td class="px-3.5 py-2.5 text-right font-mono text-gray-600">
                                                    Rp {{ number_format($item->buy_price ?? 0, 0, ',', '.') }}
                                                </td>
                                                <td class="px-3.5 py-2.5 text-right font-mono font-semibold text-gray-900">
                                                    Rp {{ number_format(($item->qty_received ?? 0) * ($item->buy_price ?? 0), 0, ',', '.') }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="px-4 py-4 text-center text-gray-400">Tidak ada rincian item barang.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Riwayat Pembayaran (jika sudah pernah dicicil) -->
                        @if($detailReceipt->payments && $detailReceipt->payments->count() > 0)
                        <div>
                            <div class="mb-2 flex items-center justify-between">
                                <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider flex items-center gap-2">
                                    <span class="w-1.5 h-3.5 bg-green-600 rounded-full"></span>
                                    Riwayat Pembayaran Hutang
                                </h4>
                            </div>
                            <div class="border border-gray-200 rounded-xl overflow-hidden shadow-2xs">
                                <table class="min-w-full divide-y divide-gray-200 text-xs">
                                    <thead class="bg-gray-50 text-gray-600 font-semibold uppercase">
                                        <tr>
                                            <th class="px-3.5 py-2 text-left">Tanggal</th>
                                            <th class="px-3.5 py-2 text-right">Jumlah Dibayar</th>
                                            <th class="px-3.5 py-2 text-left">Dicatat Oleh</th>
                                            <th class="px-3.5 py-2 text-left">Catatan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 bg-white">
                                        @foreach($detailReceipt->payments as $pay)
                                            <tr>
                                                <td class="px-3.5 py-2 text-gray-700 font-medium">{{ \Carbon\Carbon::parse($pay->payment_date ?? $pay->created_at)->format('d/m/Y') }}</td>
                                                <td class="px-3.5 py-2 text-right font-mono font-bold text-green-600">Rp {{ number_format($pay->amount, 0, ',', '.') }}</td>
                                                <td class="px-3.5 py-2 text-gray-600">{{ $pay->user?->name ?? 'Kasir / Staff' }}</td>
                                                <td class="px-3.5 py-2 text-gray-500 italic">{{ $pay->notes ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Modal Footer -->
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-3">
                        <div class="text-xs text-gray-400 italic">
                            @if($detailReceipt->notes)
                                <span>Catatan: {{ $detailReceipt->notes }}</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                            <a href="{{ route('pdf.goods-receipt', $detailReceipt->id) }}" target="_blank"
                               class="btn btn-export-pdf inline-flex items-center gap-1.5" title="Export PDF Surat Jalan">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                </svg>
                                <span>Lihat Surat Jalan (PDF)</span>
                            </a>

                            @if(($detailReceipt->total_amount - $detailReceipt->paid_amount) > 0)
                                <button type="button" wire:click="payFromDetail" class="btn btn-success inline-flex items-center gap-1 cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    <span>Bayar Hutang</span>
                                </button>
                            @endif

                            <button type="button" wire:click="closeDetailModal" class="btn btn-secondary cursor-pointer">
                                Tutup
                            </button>
                        </div>
                    </div>

                @elseif($type === 'ar' && $detailReceivable)
                    <!-- AR (Piutang Customer / Penjualan) -->
                    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-amber-50/40">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-amber-100 rounded-xl text-amber-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900" id="detail-modal-title">
                                    Detail Piutang Penjualan
                                </h3>
                                <p class="text-xs text-gray-500 font-mono">No. Invoice: {{ $detailReceivable->sale?->invoice_number ?? $detailReceivable->invoice_number }}</p>
                            </div>
                        </div>
                        <button type="button" wire:click="closeDetailModal" class="text-gray-400 hover:text-gray-600 transition-colors p-1.5 rounded-lg hover:bg-gray-200/50 cursor-pointer">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div class="p-6 space-y-6 max-h-[75vh] overflow-y-auto">
                        <!-- Summary Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-gray-50/80 p-4 rounded-xl border border-gray-100">
                            <div>
                                <span class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider block mb-1">Customer / Pasien</span>
                                <p class="text-sm font-bold text-gray-900">{{ $detailReceivable->customer?->name ?? 'Pelanggan Umum' }}</p>
                                @if($detailReceivable->customer?->phone)
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $detailReceivable->customer->phone }}</p>
                                @endif
                            </div>
                            <div>
                                <span class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider block mb-1">Tanggal Transaksi</span>
                                <p class="text-xs text-gray-700">Tanggal: <span class="font-medium text-gray-900">{{ $detailReceivable->created_at->format('d M Y H:i') }}</span></p>
                                <p class="text-xs text-gray-700 mt-0.5">Jatuh Tempo: 
                                    <span class="font-medium {{ $detailReceivable->due_date && \Carbon\Carbon::parse($detailReceivable->due_date)->isPast() && ($detailReceivable->remaining_balance > 0) ? 'text-red-600 font-bold' : 'text-gray-900' }}">
                                        {{ $detailReceivable->due_date ? \Carbon\Carbon::parse($detailReceivable->due_date)->format('d M Y') : '-' }}
                                    </span>
                                </p>
                            </div>
                            <div class="text-left md:text-right">
                                <span class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider block mb-1">Total & Sisa Piutang</span>
                                <p class="text-xs text-gray-500">Total: Rp {{ number_format($detailReceivable->total_amount, 0, ',', '.') }}</p>
                                <p class="text-sm font-extrabold text-amber-600">Sisa: Rp {{ number_format($detailReceivable->remaining_balance, 0, ',', '.') }}</p>
                            </div>
                        </div>

                        <!-- Daftar Item Penjualan -->
                        @if($detailReceivable->sale && $detailReceivable->sale->items && $detailReceivable->sale->items->count() > 0)
                        <div>
                            <div class="mb-2.5 flex items-center justify-between">
                                <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider flex items-center gap-2">
                                    <span class="w-1.5 h-3.5 bg-amber-500 rounded-full"></span>
                                    Rincian Produk yang Dibeli
                                </h4>
                            </div>
                            <div class="border border-gray-200 rounded-xl overflow-hidden shadow-2xs">
                                <table class="min-w-full divide-y divide-gray-200 text-xs">
                                    <thead class="bg-gray-50 text-gray-600 font-semibold uppercase">
                                        <tr>
                                            <th class="px-3.5 py-2.5 text-left">No</th>
                                            <th class="px-3.5 py-2.5 text-left">Nama Produk</th>
                                            <th class="px-3.5 py-2.5 text-center">Qty</th>
                                            <th class="px-3.5 py-2.5 text-center">Satuan</th>
                                            <th class="px-3.5 py-2.5 text-right">Harga Jual</th>
                                            <th class="px-3.5 py-2.5 text-right">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 bg-white">
                                        @foreach($detailReceivable->sale->items as $idx => $sItem)
                                            <tr class="hover:bg-amber-50/20 transition-colors">
                                                <td class="px-3.5 py-2.5 text-gray-400 text-center">{{ $idx + 1 }}</td>
                                                <td class="px-3.5 py-2.5 font-medium text-gray-900">{{ $sItem->product?->name ?? 'Produk' }}</td>
                                                <td class="px-3.5 py-2.5 text-center font-bold text-gray-800">{{ number_format($sItem->quantity, 0, ',', '.') }}</td>
                                                <td class="px-3.5 py-2.5 text-center text-gray-600 uppercase">{{ $sItem->unit?->name ?? '-' }}</td>
                                                <td class="px-3.5 py-2.5 text-right font-mono text-gray-600">Rp {{ number_format($sItem->sell_price, 0, ',', '.') }}</td>
                                                <td class="px-3.5 py-2.5 text-right font-mono font-semibold text-gray-900">Rp {{ number_format($sItem->subtotal, 0, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif

                        <!-- Riwayat Pembayaran Piutang -->
                        @if($detailReceivable->payments && $detailReceivable->payments->count() > 0)
                        <div>
                            <div class="mb-2 flex items-center justify-between">
                                <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider flex items-center gap-2">
                                    <span class="w-1.5 h-3.5 bg-green-600 rounded-full"></span>
                                    Riwayat Pembayaran Piutang
                                </h4>
                            </div>
                            <div class="border border-gray-200 rounded-xl overflow-hidden shadow-2xs">
                                <table class="min-w-full divide-y divide-gray-200 text-xs">
                                    <thead class="bg-gray-50 text-gray-600 font-semibold uppercase">
                                        <tr>
                                            <th class="px-3.5 py-2 text-left">Tanggal</th>
                                            <th class="px-3.5 py-2 text-right">Jumlah Dibayar</th>
                                            <th class="px-3.5 py-2 text-left">Metode / Akun</th>
                                            <th class="px-3.5 py-2 text-left">Catatan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 bg-white">
                                        @foreach($detailReceivable->payments as $rPay)
                                            <tr>
                                                <td class="px-3.5 py-2 text-gray-700 font-medium">{{ $rPay->created_at->format('d/m/Y H:i') }}</td>
                                                <td class="px-3.5 py-2 text-right font-mono font-bold text-green-600">Rp {{ number_format($rPay->amount, 0, ',', '.') }}</td>
                                                <td class="px-3.5 py-2 text-gray-600">{{ $rPay->account?->name ?? ucfirst($rPay->payment_method ?? 'Kas') }}</td>
                                                <td class="px-3.5 py-2 text-gray-500 italic">{{ $rPay->notes ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Modal Footer -->
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex flex-col sm:flex-row justify-end items-center gap-3">
                        @if($detailReceivable->sale_id)
                            <a href="{{ route('pos.receipt', $detailReceivable->sale_id) }}" target="_blank"
                               class="btn btn-secondary inline-flex items-center gap-1.5" title="Lihat Struk Penjualan">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                </svg>
                                <span>Lihat Struk</span>
                            </a>
                        @endif

                        @if($detailReceivable->remaining_balance > 0)
                            <button type="button" wire:click="payFromDetail" class="btn btn-success inline-flex items-center gap-1 cursor-pointer">
                                <span>Bayar Piutang</span>
                            </button>
                        @endif

                        <button type="button" wire:click="closeDetailModal" class="btn btn-secondary cursor-pointer">
                            Tutup
                        </button>
                    </div>
                @endif

            </div>
        </div>
    </div>
    @endif

    {{-- Payment Modal --}}
    @if($showPaymentModal)
    <div wire:key="payment-modal-{{ $selectedItemId }}" class="fixed inset-0 z-[60] overflow-y-auto" aria-labelledby="payment-modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true" wire:click="closePaymentModal"></div>

            <div class="relative inline-block align-middle bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-md sm:w-full border border-gray-100 animate-fade-in-up">
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900" id="payment-modal-title">
                        Pelunasan {{ $type === 'ap' ? 'Hutang' : 'Piutang' }}
                    </h3>
                    <button wire:click="closePaymentModal" class="text-gray-400 hover:text-gray-600 transition-colors p-1 rounded-lg hover:bg-gray-200/50">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form wire:submit.prevent="paySettlement">
                    <div class="p-6 space-y-4">
                        <!-- Summary Info -->
                        <div class="bg-blue-50/30 p-4 rounded-xl border border-blue-100/50 space-y-3">
                            <div>
                                <span class="text-xs font-medium text-gray-500 block uppercase tracking-wide">{{ $type === 'ap' ? 'Supplier' : 'Customer' }}</span>
                                <span class="text-sm font-bold text-gray-900 block">{{ $selectedEntityName }}</span>
                            </div>
                            <div>
                                <span class="text-xs font-medium text-blue-500 block uppercase tracking-wide">Sisa {{ $type === 'ap' ? 'Hutang' : 'Piutang' }}</span>
                                <div class="flex items-baseline gap-2">
                                    <span class="text-xs font-medium text-gray-500">Rp.</span>
                                    <span class="text-xl font-bold text-gray-900">{{ number_format($maxPaymentAmount, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Amount Field -->
                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-2">Jumlah Bayar</label>
                            <div class="relative" x-data="money($wire.entangle('paymentAmount'))">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <span class="text-sm font-medium text-gray-400">Rp.</span>
                                </div>
                                <input type="text" x-bind="input"
                                    class="w-full pl-12 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-medium text-gray-900 text-lg transition-all placeholder:text-gray-300"
                                    placeholder="0">
                            </div>
                            @error('paymentAmount') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Date Field -->
                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-2">Tanggal</label>
                            <x-date-picker wire:model="paymentDate" 
                                class="w-full px-3 py-2 border-gray-200 rounded-xl focus:ring-blue-500 focus:border-blue-500 font-medium text-gray-900 text-sm"></x-date-picker>
                            @error('paymentDate') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Method & Bank Selection -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-400 mb-2">Metode Pembayaran</label>
                                <select wire:model.live="paymentMethod" 
                                    class="w-full px-3 py-2 border-gray-200 rounded-xl focus:ring-blue-500 focus:border-blue-500 font-bold text-gray-900 text-sm">
                                    <option value="cash">Tunai (Kas)</option>
                                    <option value="transfer">Transfer Bank</option>
                                </select>
                            </div>

                            @if($paymentMethod === 'transfer')
                            <div class="animate-fade-in-up">
                                <label class="block text-xs font-medium text-gray-400 mb-2">Pilih Bank</label>
                                <select wire:model="bankAccountId" 
                                    class="w-full px-3 py-2 border-gray-200 rounded-xl focus:ring-blue-500 focus:border-blue-500 font-medium text-gray-900 text-sm">
                                    <option value="">-- Pilih Akun Bank --</option>
                                    @foreach($accounts as $acc)
                                        <option value="{{ $acc->id }}">{{ $acc->name }} ({{ $acc->code }})</option>
                                    @endforeach
                                </select>
                                @error('bankAccountId') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            @endif
                        </div>

                        <!-- Notes Field -->
                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-2">Catatan (Opsional)</label>
                            <textarea wire:model="paymentNotes" rows="2" 
                                class="w-full px-3 py-2 border-gray-200 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-sm"
                                placeholder="Catatan pelunasan..."></textarea>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-end gap-3">
                        <button type="button" wire:click="closePaymentModal"
                            class="btn btn-secondary">
                            Batal
                        </button>
                        <button type="submit"
                            class="btn btn-success">
                            Simpan Pembayaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
