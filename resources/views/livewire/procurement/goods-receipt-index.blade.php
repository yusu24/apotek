<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            Penerimaan Pesanan
        </h2>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="mb-4 flex flex-col sm:flex-row gap-4">
            <a href="{{ route('procurement.goods-receipts.create') }}" wire:navigate
                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 shadow-sm font-medium flex items-center justify-center gap-2 transition duration-200 text-sm whitespace-nowrap shrink-0 w-full sm:w-auto">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span class="inline">Buat Penerimaan</span>
            </a>
            
            <div class="flex-1 flex gap-4">
                <input type="text" wire:model.live="search" placeholder="Cari No Surat Jalan / Supplier..." 
                    class="w-64 border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                    <tr>
                        <th class="px-6 py-4 text-left border-b border-gray-100">No. Surat Jalan</th>
                        <th class="px-6 py-4 text-left border-b border-gray-100">Tanggal</th>
                        <th class="px-6 py-4 text-left border-b border-gray-100">Supplier (PO)</th>
                        <th class="px-6 py-4 text-left border-b border-gray-100">Status Bayar</th>
                        <th class="px-6 py-4 text-right border-b border-gray-100">Total</th>
                        <th class="px-6 py-4 text-left border-b border-gray-100">User</th>
                        <th class="px-6 py-4 text-right border-b border-gray-100">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($receipts as $gr)
                        <tr wire:key="receipt-{{ $gr->id }}" class="hover:bg-blue-50/50 transition-all duration-200">
                            <td class="px-6 py-4 text-sm font-bold text-gray-900 border-l-4 border-blue-500">{{ $gr->delivery_note_number }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ \Carbon\Carbon::parse($gr->received_date)->format('d M Y') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <div class="font-medium text-gray-900">{{ $gr->purchaseOrder->supplier->name ?? 'Direct Receipt' }}</div>
                                @if($gr->purchaseOrder)
                                    <div class="text-[10px] text-gray-400 mt-1 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                        {{ $gr->purchaseOrder->po_number }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex flex-col gap-1">
                                    <span class="inline-flex items-center w-fit px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-{{ $gr->payment_status_color }}-100 text-{{ $gr->payment_status_color }}-800">
                                        {{ $gr->payment_status_label }}
                                    </span>
                                    @if($gr->payment_status !== 'paid' && $gr->due_date)
                                        <span class="text-[10px] text-red-500 font-bold flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            {{ \Carbon\Carbon::parse($gr->due_date)->format('d/m/y') }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 text-right text-normal text-gray-900">
                                <div>Rp {{ number_format($gr->total_amount, 0, ',', '.') }}</div>
                                @if($gr->payment_status !== 'paid')
                                    <div class="text-[10px] text-orange-600 font-bold mt-1">Sisa: Rp {{ number_format($gr->total_amount - $gr->paid_amount, 0, ',', '.') }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $gr->user->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm whitespace-nowrap">
                                <div class="flex items-center justify-end gap-3">
                                    @if($gr->payment_status !== 'paid')
                                        <button wire:click="openPaymentModal({{ $gr->id }})" 
                                            class="text-green-600 hover:text-green-900 transition-colors duration-200" title="Bayar">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                        </button>
                                    @endif
                                    
                                    @can('edit goods receipts')
                                        <a href="{{ route('procurement.goods-receipts.edit', $gr->id) }}" wire:navigate
                                            class="text-orange-500 hover:text-orange-700 transition-colors duration-200" title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>
                                    @endcan
                                    
                                    <button wire:click="showDetail({{ $gr->id }})" 
                                        class="text-blue-600 hover:text-blue-900 transition-colors duration-200" title="Lihat Detail">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </button>

                                    <a href="{{ route('pdf.goods-receipt', $gr->id) }}" target="_blank"
                                        class="text-gray-600 hover:text-gray-900 transition-colors duration-200" title="Cetak Surat Jalan">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">Belum ada data penerimaan barang.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $receipts->links() }}
        </div>
    </div>

    <!-- Detail Modal -->
    @if($showDetailModal && $selectedReceipt)
    <div wire:key="detail-modal-{{ $selectedId }}" class="fixed inset-0 z-[60] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true" wire:click="closeDetailModal"></div>

            <div class="relative inline-block align-middle bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-2xl sm:w-full border border-gray-100 animate-fade-in-up">
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg leading-6 font-normal text-gray-900" id="modal-title">
                        Detail Penerimaan Barang
                    </h3>
                    <button wire:click="closeDetailModal" class="text-gray-400 hover:text-gray-600 transition-colors p-1 rounded-lg hover:bg-gray-200/50">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-6">
                    <!-- Summary Info Card -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 bg-blue-50/30 p-6 rounded-2xl border border-blue-100/50">
                        <div class="space-y-4">
                            <div>
                                <span class="text-xs font-normal text-blue-500 block mb-1">No. Surat Jalan</span>
                                <span class="text-lg font-bold text-gray-900">{{ $selectedReceipt->delivery_note_number }}</span>
                                @if($selectedReceipt->purchaseOrder)
                                    <span class="text-xs text-blue-500 font-bold mt-1 flex items-center gap-1">
                                        PO: {{ $selectedReceipt->purchaseOrder->po_number }}
                                    </span>
                                @endif
                            </div>
                            <div>
                                <span class="text-xs font-normal text-gray-400 block mb-1">Tanggal Terima</span>
                                <span class="text-sm font-normal text-gray-700">{{ \Carbon\Carbon::parse($selectedReceipt->received_date)->format('d F Y') }}</span>
                            </div>
                        </div>
                        <div class="space-y-4 text-right">
                            <div>
                                <p class="text-[10px] font-normal text-blue-400 uppercase tracking-wider mb-1">Status Pembayaran</p>
                                <span class="inline-flex px-3 py-1 rounded-full text-[10px] font-bold bg-{{ $selectedReceipt->payment_status_color }}-100 text-{{ $selectedReceipt->payment_status_color }}-800 uppercase tracking-wider">
                                    {{ $selectedReceipt->payment_status_label }}
                                </span>
                            </div>
                            <div>
                                <span class="text-xs font-normal text-gray-400 block mb-1">Total Pembelian</span>
                                <span class="text-xl font-extrabold text-blue-600">Rp {{ number_format($selectedReceipt->total_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Items Table Container -->
                    <div class="mb-4 flex items-center gap-2">
                        <div class="h-4 w-1 bg-blue-600 rounded-full"></div>
                        <h4 class="text-sm font-normal text-gray-900">Daftar Barang yang Diterima</h4>
                    </div>

                    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead>
                                <tr class="bg-gray-50/50">
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase">Produk</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-400 uppercase">Qty</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-400 uppercase">Satuan</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-400 uppercase">Batch No</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($selectedReceipt->items as $item)
                                    <tr class="hover:bg-blue-50/20 transition-colors group">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm font-medium text-gray-900 group-hover:text-blue-600 transition-colors">{{ $item->product->name }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span class="text-sm font-medium text-gray-900">{{ number_format($item->qty_received, 0, ',', '.') }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span class="inline-flex px-2.5 py-1 rounded-lg text-[10px] font-medium bg-gray-100 text-gray-500 uppercase tracking-tighter">{{ $item->unit->name ?? '-' }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <span class="font-mono text-xs text-gray-400 bg-gray-50 px-2 py-1 rounded border border-gray-100">{{ $item->batch_no ?? '-' }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-between items-center">
                    <div class="flex-1">
                        @if($selectedReceipt->notes)
                            <div class="text-xs text-gray-400 italic flex items-start gap-2 max-w-md">
                                <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path></svg>
                                <p>Catatan: {{ $selectedReceipt->notes }}</p>
                            </div>
                        @endif
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('pdf.goods-receipt', $selectedReceipt->id) }}" target="_blank"
                            class="px-6 py-2.5 bg-blue-600 text-white rounded-xl font-normal hover:bg-blue-700 transition-all shadow-sm flex items-center gap-2 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            Export PDF
                        </a>
                        <button type="button" wire:click="closeDetailModal" 
                            class="px-6 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl font-normal hover:bg-gray-50 transition-all shadow-sm text-sm">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Payment Modal -->
    @if($showPaymentModal)
    <div wire:key="payment-modal-{{ $selectedId }}" class="fixed inset-0 z-[60] overflow-y-auto" aria-labelledby="payment-modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true" wire:click="closePaymentModal"></div>

            <div class="relative inline-block align-middle bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-md sm:w-full border border-gray-100 animate-fade-in-up">
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900" id="payment-modal-title">
                        Catat Pembayaran Hutang
                    </h3>
                    <button wire:click="closePaymentModal" class="text-gray-400 hover:text-gray-600 transition-colors p-1 rounded-lg hover:bg-gray-200/50">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form wire:submit="savePayment">
                    <div class="p-6 space-y-4">
                        <!-- Debt Summary Card -->
                        <div class="bg-blue-50/30 p-6 rounded-2xl border border-blue-100/50">
                            <span class="text-xs font-medium text-blue-500 block mb-2">Sisa Hutang</span>
                            <div class="flex items-baseline gap-2">
                                <span class="text-sm font-medium text-gray-500">Rp</span>
                                <span class="text-3xl font-bold text-gray-900">{{ number_format($remaining_debt, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <!-- Amount Field -->
                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-2">Jumlah Bayar</label>
                            <div class="relative" x-data="money($wire.entangle('payment_amount'))">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <span class="text-sm font-medium text-gray-400">Rp</span>
                                </div>
                                <input type="text" x-bind="input"
                                    class="w-full pl-12 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-medium text-gray-900 text-lg transition-all placeholder:text-gray-300"
                                    placeholder="0">
                            </div>
                            @error('payment_amount') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <!-- Date Field -->
                            <div>
                                <label class="block text-xs font-medium text-gray-400 mb-2">Tanggal</label>
                                <input type="date" wire:model="payment_date" 
                                    class="w-full px-3 py-2 border-gray-200 rounded-xl focus:ring-blue-500 focus:border-blue-500 font-bold text-gray-900 text-sm">
                                @error('payment_date') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <!-- Method Field -->
                            <div>
                                <label class="block text-xs font-medium text-gray-400 mb-2">Metode</label>
                                <select wire:model="payment_method" 
                                    class="w-full px-3 py-2 border-gray-200 rounded-xl focus:ring-blue-500 focus:border-blue-500 font-bold text-gray-900 text-sm">
                                    <option value="cash">CASH</option>
                                    <option value="transfer">TRANSFER</option>
                                </select>
                                @error('payment_method') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Notes Field -->
                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-2">Catatan (Opsional)</label>
                            <textarea wire:model="payment_notes" rows="2" 
                                class="w-full px-3 py-2 border-gray-200 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-sm"
                                placeholder="Misal: Pelunasan tahap 1..."></textarea>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-end gap-3">
                        <button type="button" wire:click="closePaymentModal" 
                            class="px-6 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl font-normal hover:bg-gray-50 transition-all shadow-sm text-sm">
                            Batal
                        </button>
                        <button type="submit" 
                            class="px-6 py-2.5 bg-blue-600 text-white rounded-xl font-normal hover:bg-blue-700 transition-all shadow-sm text-sm">
                            Simpan Pembayaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>

