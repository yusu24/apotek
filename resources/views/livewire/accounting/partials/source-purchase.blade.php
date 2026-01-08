{{-- Purchase / Goods Receipt Detail --}}
<div class="space-y-4">
    {{-- Receipt Info --}}
    <div class="grid grid-cols-2 gap-4 pb-4 border-b">
        <div>
            <label class="text-xs font-bold text-gray-500 uppercase">No. Surat Jalan</label>
            <p class="text-sm font-bold text-gray-900">{{ $receipt->delivery_note_number }}</p>
        </div>
        <div>
            <label class="text-xs font-bold text-gray-500 uppercase">Tanggal Terima</label>
            <p class="text-sm text-gray-900">{{ $receipt->received_date }}</p>
        </div>
        <div>
            <label class="text-xs font-bold text-gray-500 uppercase">Supplier</label>
            <p class="text-sm font-semibold text-gray-900">{{ $receipt->purchaseOrder->supplier->name ?? 'N/A' }}</p>
        </div>
        <div>
            <label class="text-xs font-bold text-gray-500 uppercase">Total Amount</label>
            <p class="text-lg font-bold text-blue-600">Rp {{ number_format($receipt->total_amount, 0, ',', '.') }}</p>
        </div>
        @if($receipt->purchaseOrder)
        <div>
            <label class="text-xs font-bold text-gray-500 uppercase">No. PO</label>
            <p class="text-sm text-blue-600 font-semibold">{{ $receipt->purchaseOrder->po_number }}</p>
        </div>
        @endif
        <div>
            <label class="text-xs font-bold text-gray-500 uppercase">Status Pembayaran</label>
            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-bold
                {{ $receipt->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                {{ $receipt->payment_status_label }}
            </span>
        </div>
    </div>

    {{-- Items Received --}}
    <div>
        <h4 class="font-bold text-gray-900 mb-3">Item yang Diterima</h4>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700">Produk</th>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700">Batch No</th>
                        <th class="px-3 py-2 text-right text-xs font-bold text-gray-700">Qty</th>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700">Satuan</th>
                        <th class="px-3 py-2 text-right text-xs font-bold text-gray-700">Harga Beli</th>
                        <th class="px-3 py-2 text-right text-xs font-bold text-gray-700">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($receipt->items as $item)
                    <tr>
                        <td class="px-3 py-2 text-gray-900">{{ $item->product->name }}</td>
                        <td class="px-3 py-2 text-gray-700 font-mono text-xs">{{ $item->batch_no }}</td>
                        <td class="px-3 py-2 text-right text-gray-900">{{ $item->quantity }}</td>
                        <td class="px-3 py-2 text-gray-700">{{ $item->unit->name }}</td>
                        <td class="px-3 py-2 text-right text-gray-900">Rp {{ number_format($item->buy_price, 0, ',', '.') }}</td>
                        <td class="px-3 py-2 text-right font-semibold text-gray-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 font-bold">
                    <tr>
                        <td colspan="5" class="px-3 py-2 text-right">Total:</td>
                        <td class="px-3 py-2 text-right text-blue-600">Rp {{ number_format($receipt->total_amount, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Payment Info --}}
    @if($receipt->payment_status !== 'paid')
    <div class="p-4 bg-yellow-50 rounded-lg border border-yellow-200">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            <div>
                <p class="text-sm font-bold text-yellow-800">Belum Lunas</p>
                @if($receipt->due_date)
                <p class="text-xs text-yellow-700">Jatuh tempo: {{ \Carbon\Carbon::parse($receipt->due_date)->format('d/m/Y') }}</p>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- Notes --}}
    @if($receipt->notes)
    <div class="p-4 bg-gray-50 rounded-lg">
        <label class="text-xs font-bold text-gray-600 uppercase">Catatan</label>
        <p class="text-sm text-gray-700 mt-1">{{ $receipt->notes }}</p>
    </div>
    @endif
</div>
