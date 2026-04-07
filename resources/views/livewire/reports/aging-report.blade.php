<div class="p-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Laporan Umur Hutang & Piutang</h2>
            <p class="text-sm text-gray-500 mt-1">Analisis keterlambatan pembayaran hutang supplier dan piutang pelanggan • Terupdate: {{ now()->format('H:i:s') }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-2 sm:gap-3 w-full md:w-auto">
            @can('export aging report')
            <a href="{{ route('excel.aging-report', ['showPaid' => $showPaid]) }}" 
               target="_blank"
               class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 shadow-md font-bold text-sm flex items-center justify-center gap-2 transition duration-200"
               title="Export Excel AP and AR">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span class="hidden sm:inline">Export Excel</span>
            </a>
            @endcan
            <a href="{{ route('pdf.aging-report', ['type' => $type, 'showPaid' => $showPaid]) }}" target="_blank" class="px-4 py-2 bg-blue-900 text-white rounded-lg hover:bg-blue-800 shadow-md font-bold text-sm flex items-center justify-center gap-2 transition duration-200" title="Export PDF">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                <span class="hidden sm:inline">Export PDF</span>
            </a>
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

    <div class="flex flex-row flex-nowrap gap-2 mb-8 py-2 overflow-x-auto custom-scrollbar">
        @foreach($colorSequence as $key => $config)
            @php
                $isActive = ($activeTab === $key);
                $ringClass = $isActive ? "ring-2 ring-white ring-offset-2 scale-[1.03] z-10" : "";
            @endphp
            <div class="flex-1 min-w-[120px] rounded-xl shadow-xl p-3 cursor-pointer transition-all duration-300 hover:-translate-y-1 {{ $ringClass }}" 
                style="background-color: {{ $config['hex'] }};"
                wire:click="setActiveTab('{{ $key }}')">
                <h3 class="text-[12px] font-black text-white uppercase tracking-wider text-center opacity-95">{{ $config['label'] }}</h3>
                <p class="text-[12px] font-black text-white truncate text-center leading-tight mt-1">Rp{{ number_format($summary[$key === 'all' ? 'total' : $key] ?? 0, 0, ',', '.') }}</p>
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

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 text-gray-800 font-bold uppercase text-xs">
                    <tr>
                        <th class="px-6 py-4 text-left">
                            {{ $type === 'ap' ? 'Supplier' : 'Customer' }}
                        </th>
                        <th class="px-6 py-4 text-left">
                            {{ $type === 'ap' ? 'No. Surat Jalan' : 'No. Invoice' }}
                        </th>
                        <th class="px-6 py-4 text-left">Tanggal</th>
                        <th class="px-6 py-4 text-left">Jatuh Tempo</th>
                        <th class="px-6 py-4 text-center">Umur (Hari)</th>
                        @if($type === 'ar')
                             <th class="px-6 py-4 text-center">Status Jatuh Tempo</th>
                        @endif
                        <th class="px-6 py-4 text-right">Total Tagihan</th>
                        <th class="px-6 py-4 text-right">Sisa {{ $type === 'ap' ? 'Hutang' : 'Piutang' }}</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @php
                        $displayData = [];
                        if ($activeTab === 'all') {
                            $displayData = array_merge(
                                $reportData['45+'] ?? [], 
                                $reportData['31-45'] ?? [], 
                                $reportData['16-30'] ?? [], 
                                $reportData['8-15'] ?? [],
                                $reportData['0-7'] ?? []
                            );
                        } else {
                            $displayData = $reportData[$activeTab] ?? [];
                        }
                    @endphp

                    @forelse($displayData as $item)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                            {{ $type === 'ap' ? ($item['supplier'] ?? '-') : ($item['customer'] ?? '-') }}
                            @if($type === 'ar' && isset($item['customer_phone']))
                                <span class="block text-xs text-gray-500">{{ $item['customer_phone'] }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item['invoice_number'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($item['date'])->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $item['due_date'] && $item['due_date'] !== '-' ? \Carbon\Carbon::parse($item['due_date'])->format('d/m/Y') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-black rounded-full 
                                {{ $item['age'] > 90 ? 'bg-red-100 text-red-800' : 
                                   ($item['age'] > 60 ? 'bg-yellow-100 text-yellow-800' : 
                                   ($item['age'] > 30 ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800')) }}">
                                {{ $item['age'] }} Hari
                            </span>
                        </td>
                        @if($type === 'ar')
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                            @if($item['status'] === 'paid' || $item['outstanding'] <= 0)
                                <span class="px-2 inline-flex text-xs leading-5 font-bold rounded-full bg-green-100 text-green-800">
                                    LUNAS
                                </span>
                            @elseif(isset($item['days_remaining']))
                                @if($item['days_remaining'] < 0)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Telat {{ abs($item['days_remaining']) }} Hari
                                    </span>
                                @elseif($item['days_remaining'] == 0)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                                        Jatuh Tempo Hari Ini
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        {{ $item['days_remaining'] }} Hari Lagi
                                    </span>
                                @endif
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        @endif
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500">Rp {{ number_format($item['total_amount'], 0, ',', '.') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-gray-900">
                            @if($item['outstanding'] <= 0)
                                <span class="text-green-600 font-bold">LUNAS</span>
                            @else
                                Rp {{ number_format($item['outstanding'], 0, ',', '.') }}
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                            @if($item['outstanding'] > 0)
                                @php
                                    $entityName = ($type === 'ap') ? ($item['supplier'] ?? 'Supplier') : ($item['customer'] ?? 'Customer');
                                @endphp
                                <button wire:click="openPaymentModal({{ $item['id'] }}, {{ $item['outstanding'] }}, '{{ addslashes($entityName) }}')"
                                    class="inline-flex items-center px-4 py-1.5 border border-transparent text-xs font-bold rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150 shadow-sm">
                                    Bayar
                                </button>
                            @else
                                <span class="text-xs text-green-600 font-bold flex items-center justify-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Terbayar
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ $type === 'ar' ? 8 : 7 }}" class="px-6 py-10 text-center text-gray-500 italic">
                            Tidak ada data {{ $type === 'ap' ? 'hutang' : 'piutang' }} pada kategori ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
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
                                    <span class="text-xs font-medium text-gray-500">Rp</span>
                                    <span class="text-xl font-bold text-gray-900">{{ number_format($maxPaymentAmount, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Amount Field -->
                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-2">Jumlah Bayar</label>
                            <div class="relative" x-data="money($wire.entangle('paymentAmount'))">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <span class="text-sm font-medium text-gray-400">Rp</span>
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
                            class="px-6 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl font-normal hover:bg-gray-50 transition-all shadow-sm text-sm">
                            Batal
                        </button>
                        <button type="submit" 
                            class="px-6 py-2.5 bg-green-600 text-white rounded-xl font-normal hover:bg-green-700 transition-all shadow-sm text-sm">
                            Simpan Pembayaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
