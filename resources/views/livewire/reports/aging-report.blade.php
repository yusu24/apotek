<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Laporan Umur Hutang & Piutang</h2>
            <p class="text-sm text-gray-500 mt-1">AP & AR Aging Report</p>
        </div>
        <a href="{{ route('pdf.aging-report', ['type' => $type, 'showPaid' => $showPaid]) }}" target="_blank" class="px-4 py-2 bg-blue-900 text-white rounded-lg hover:bg-blue-800 shadow-md font-bold text-sm flex items-center justify-center gap-2 transition duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span>Export PDF</span>
        </a>
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
    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        @foreach(['0-7' => 'green', '8-15' => 'blue', '16-30' => 'yellow', '31-45' => 'orange', '45+' => 'red'] as $key => $color)
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-{{ $color }}-500 cursor-pointer hover:bg-gray-50 transition" wire:click="setActiveTab('{{ $key }}')">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-xs font-bold text-gray-500 uppercase">{{ $key }} Hari</h3>
                    <div class="p-1.5 bg-{{ $color }}-100 rounded-lg">
                        <svg class="w-4 h-4 text-{{ $color }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <p class="text-xl font-bold text-gray-800">Rp {{ number_format($reportData['summary'][$key] ?? 0, 0, ',', '.') }}</p>
                <p class="text-xs mt-1 text-gray-500">Total Outstanding</p>
            </div>
        @endforeach
    </div>

    {{-- Tabs --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="border-b border-gray-200">
            <div class="px-4 py-3">
                <label for="aging_period" class="block text-sm font-medium text-gray-700 mb-1">Pilih Jangka Waktu</label>
                <select wire:model.live="activeTab" id="aging_period" class="block w-64 pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                    <option value="all">Semua (Rp {{ number_format($reportData['summary']['total'], 0, ',', '.') }})</option>
                    @foreach(['0-7', '8-15', '16-30', '31-45', '45+'] as $key)
                        <option value="{{ $key }}">
                            {{ $key === '45+' ? '> 45 Hari' : $key . ' Hari' }} 
                            (Rp {{ number_format($reportData['summary'][$key] ?? 0, 0, ',', '.') }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="flex justify-end px-4 py-2 bg-gray-50 border-t border-gray-200">
             <div class="flex items-center">
                <input type="checkbox" wire:model.live="showPaid" id="showPaid" class="rounded border-gray-300 text-green-600 shadow-sm focus:ring-green-500">
                <label for="showPaid" class="ml-2 text-sm text-gray-600 font-bold cursor-pointer">Tampilkan Data Lunas</label>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ $type === 'ap' ? 'Supplier' : 'Customer' }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ $type === 'ap' ? 'No. Surat Jalan' : 'No. Invoice' }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jatuh Tempo</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Umur (Hari)</th>
                        @if($type === 'ar')
                             <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status Jatuh Tempo</th>
                        @endif
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Tagihan</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Sisa {{ $type === 'ap' ? 'Hutang' : 'Piutang' }}</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
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
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
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
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
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
                                <button wire:click="openPaymentModal({{ $item['id'] }}, {{ $item['outstanding'] }}, '{{ $type === 'ap' ? ($item['supplier'] ?? 'Supplier') : ($item['customer'] ?? 'Customer') }}')" 
                                    class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150">
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
                            <input type="date" wire:model="paymentDate" 
                                class="w-full px-3 py-2 border-gray-200 rounded-xl focus:ring-blue-500 focus:border-blue-500 font-medium text-gray-900 text-sm">
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
