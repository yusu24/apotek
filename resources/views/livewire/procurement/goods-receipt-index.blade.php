<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            Penerimaan Pesanan
        </h2>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="mb-4 flex gap-4">
            <a href="{{ route('procurement.goods-receipts.create') }}" wire:navigate
                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 shadow-sm font-bold flex items-center gap-2 transition duration-200 text-sm whitespace-nowrap">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span class="hidden sm:inline">Penerimaan</span>
            </a>
            <input type="text" wire:model.live="search" placeholder="Cari No Surat Jalan / Supplier..." 
                class="w-64 border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Terima</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Surat Jalan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier (PO)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Bayar</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($receipts as $gr)
                        <tr wire:key="receipt-{{ $gr->id }}">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ \Carbon\Carbon::parse($gr->received_date)->format('d M Y') }}</td>
                            <td class="px-6 py-4 text-sm font-bold text-gray-900">{{ $gr->delivery_note_number }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $gr->purchaseOrder->supplier->name ?? 'Direct Receipt' }}
                                @if($gr->purchaseOrder)
                                    <br><span class="text-xs text-gray-400">Ref: {{ $gr->purchaseOrder->po_number }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $gr->payment_status_color }}-100 text-{{ $gr->payment_status_color }}-800">
                                    {{ $gr->payment_status_label }}
                                </span>
                                @if($gr->payment_status !== 'paid' && $gr->due_date)
                                    <br><span class="text-[10px] text-red-500 font-bold">Jatuh Tempo: {{ \Carbon\Carbon::parse($gr->due_date)->format('d/m/y') }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-right font-bold text-gray-900">
                                Rp {{ number_format($gr->total_amount, 0, ',', '.') }}
                                @if($gr->payment_status !== 'paid')
                                    <br><span class="text-[10px] text-orange-600 font-bold">Sisa: Rp {{ number_format($gr->total_amount - $gr->paid_amount, 0, ',', '.') }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-right whitespace-nowrap">
                                @if($gr->payment_status !== 'paid')
                                    <button wire:click="openPaymentModal({{ $gr->id }})" 
                                        class="text-green-600 hover:text-green-900 transition-colors duration-200 p-1 rounded-full hover:bg-green-50" title="Bayar">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    </button>
                                @endif
                                <button wire:click="showDetail({{ $gr->id }})" 
                                    class="text-blue-600 hover:text-blue-900 transition-colors duration-200 p-1 rounded-full hover:bg-blue-50" title="Lihat Detail">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </button>
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
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-xl font-bold text-gray-900" id="modal-title">
                        Detail Penerimaan Pesanan
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
                                <span class="text-[10px] font-medium text-blue-400 uppercase tracking-[0.2em] block mb-1">No. Surat Jalan</span>
                                <span class="text-lg font-medium text-gray-900">{{ $selectedReceipt->delivery_note_number }}</span>
                                @if($selectedReceipt->purchaseOrder)
                                    <span class="text-xs text-blue-500 font-bold mt-1 flex items-center gap-1">
                                        PO: {{ $selectedReceipt->purchaseOrder->po_number }}
                                    </span>
                                @endif
                            </div>
                            <div>
                                <span class="text-[10px] font-medium text-gray-400 uppercase tracking-[0.2em] block mb-1">Tanggal Terima</span>
                                <span class="font-medium text-gray-700">{{ \Carbon\Carbon::parse($selectedReceipt->received_date)->format('d F Y') }}</span>
                            </div>
                        </div>
                        <div class="space-y-4 text-right">
                            <div>
                                <span class="text-[10px] font-medium text-gray-400 uppercase tracking-[0.2em] block mb-1">Status Pembayaran</span>
                                <span class="inline-flex px-3 py-1 rounded-full text-xs font-medium bg-{{ $selectedReceipt->payment_status_color }}-100 text-{{ $selectedReceipt->payment_status_color }}-800 uppercase tracking-widest">
                                    {{ $selectedReceipt->payment_status_label }}
                                </span>
                            </div>
                            <div>
                                <span class="text-[10px] font-medium text-gray-400 uppercase tracking-[0.2em] block mb-1">Total Pembelian</span>
                                <span class="text-xl font-bold text-blue-600">Rp {{ number_format($selectedReceipt->total_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Items Table Container -->
                    <div class="mb-4 flex items-center gap-2">
                        <div class="h-4 w-1 bg-blue-600 rounded-full"></div>
                        <h4 class="text-sm font-bold text-gray-900 uppercase tracking-widest">Daftar Barang yang Diterima</h4>
                    </div>

                    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead>
                                <tr class="bg-gray-50/50">
                                    <th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Produk</th>
                                    <th class="px-6 py-4 text-center text-[10px] font-bold text-gray-400 uppercase tracking-widest">Qty</th>
                                    <th class="px-6 py-4 text-center text-[10px] font-bold text-gray-400 uppercase tracking-widest">Satuan</th>
                                    <th class="px-6 py-4 text-right text-[10px] font-bold text-gray-400 uppercase tracking-widest">Batch No</th>
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
                            class="px-6 py-2.5 bg-blue-600 text-white rounded-xl font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 text-xs uppercase tracking-widest transition-all shadow-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            Export PDF
                        </a>
                        <button type="button" wire:click="closeDetailModal" 
                            class="px-6 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl font-medium hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 text-xs uppercase tracking-widest transition-all shadow-sm">
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
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-xl font-bold text-gray-900" id="payment-modal-title">
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
                            <span class="text-[10px] font-medium text-blue-400 uppercase tracking-[0.2em] block mb-2">Sisa Hutang</span>
                            <div class="flex items-baseline gap-2">
                                <span class="text-sm font-medium text-gray-500">Rp</span>
                                <span class="text-3xl font-bold text-gray-900">{{ number_format($remaining_debt, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <!-- Amount Field -->
                        <div>
                            <label class="block text-[10px] font-medium text-gray-400 uppercase tracking-[0.2em] mb-2">Jumlah Bayar</label>
                            <div class="relative" x-data="money($wire.entangle('payment_amount'))">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <span class="text-sm font-medium text-gray-400">Rp</span>
                                </div>
                                <input type="text" x-bind="input"
                                    class="w-full pl-12 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-medium text-gray-900 text-lg transition-all placeholder:text-gray-300"
                                    placeholder="0">
                            </div>
                            @error('payment_amount') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <!-- Date Field -->
                            <div>
                                <label class="block text-[10px] font-medium text-gray-400 uppercase tracking-[0.2em] mb-2">Tanggal</label>
                                <input type="date" wire:model="payment_date" 
                                    class="w-full px-3 py-2 border-gray-200 rounded-xl focus:ring-blue-500 focus:border-blue-500 font-bold text-gray-900">
                                @error('payment_date') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <!-- Method Field -->
                            <div>
                                <label class="block text-[10px] font-medium text-gray-400 uppercase tracking-[0.2em] mb-2">Metode</label>
                                <select wire:model="payment_method" 
                                    class="w-full px-3 py-2 border-gray-200 rounded-xl focus:ring-blue-500 focus:border-blue-500 font-bold text-gray-900">
                                    <option value="cash">CASH</option>
                                    <option value="transfer">TRANSFER</option>
                                </select>
                                @error('payment_method') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Notes Field -->
                        <div>
                            <label class="block text-[10px] font-medium text-gray-400 uppercase tracking-[0.2em] mb-2">Catatan (Opsional)</label>
                            <textarea wire:model="payment_notes" rows="2" 
                                class="w-full px-3 py-2 border-gray-200 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-sm"
                                placeholder="Misal: Pelunasan tahap 1..."></textarea>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-end gap-3">
                        <button type="button" wire:click="closePaymentModal" 
                            class="px-6 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl font-medium hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 text-xs uppercase tracking-widest transition-all shadow-sm">
                            Batal
                        </button>
                        <button type="submit" 
                            class="px-6 py-2.5 bg-blue-600 text-white rounded-xl font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 text-xs uppercase tracking-widest transition-all shadow-sm">
                            Simpan Pembayaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>

