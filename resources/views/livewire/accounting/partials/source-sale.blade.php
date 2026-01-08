{{-- Sale Transaction Detail --}}
<div class="space-y-4">
    {{-- Sale Info --}}
    <div class="grid grid-cols-2 gap-4 pb-4 border-b">
        <div>
            <label class="text-xs font-bold text-gray-500 uppercase">No. Invoice</label>
            <p class="text-sm font-bold text-gray-900">{{ $sale->invoice_number }}</p>
        </div>
        <div>
            <label class="text-xs font-bold text-gray-500 uppercase">Tanggal</label>
            <p class="text-sm text-gray-900">{{ $sale->created_at->format('d/m/Y H:i') }}</p>
        </div>
        <div>
            <label class="text-xs font-bold text-gray-500 uppercase">Metode Pembayaran</label>
            <p class="text-sm text-gray-900">{{ ucfirst($sale->payment_method) }}</p>
        </div>
        <div>
            <label class="text-xs font-bold text-gray-500 uppercase">Total</label>
            <p class="text-lg font-bold text-green-600">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Items Sold --}}
    <div>
        <h4 class="font-bold text-gray-900 mb-3">Item Penjualan</h4>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700">Produk</th>
                        <th class="px-3 py-2 text-right text-xs font-bold text-gray-700">Qty</th>
                        <th class="px-3 py-2 text-left text-xs font-bold text-gray-700">Satuan</th>
                        <th class="px-3 py-2 text-right text-xs font-bold text-gray-700">Harga</th>
                        <th class="px-3 py-2 text-right text-xs font-bold text-gray-700">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($sale->saleItems as $item)
                    <tr>
                        <td class="px-3 py-2 text-gray-900">{{ $item->product->name }}</td>
                        <td class="px-3 py-2 text-right text-gray-900">{{ $item->quantity }}</td>
                        <td class="px-3 py-2 text-gray-700">{{ $item->unit->name }}</td>
                        <td class="px-3 py-2 text-right text-gray-900">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        <td class="px-3 py-2 text-right font-semibold text-gray-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 font-bold">
                    <tr>
                        <td colspan="4" class="px-3 py-2 text-right">Total:</td>
                        <td class="px-3 py-2 text-right text-green-600">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Payment Details --}}
    @if($sale->payment_amount)
    <div class="grid grid-cols-3 gap-4 p-4 bg-blue-50 rounded-lg">
        <div>
            <label class="text-xs font-bold text-gray-600">Jumlah Dibayar</label>
            <p class="text-sm font-bold text-gray-900">Rp {{ number_format($sale->payment_amount, 0, ',', '.') }}</p>
        </div>
        <div>
            <label class="text-xs font-bold text-gray-600">Diskon</label>
            <p class="text-sm font-bold text-gray-900">Rp {{ number_format($sale->discount, 0, ',', '.') }}</p>
        </div>
        <div>
            <label class="text-xs font-bold text-gray-600">Kembalian</label>
            <p class="text-sm font-bold text-green-600">Rp {{ number_format($sale->change_amount, 0, ',', '.') }}</p>
        </div>
    </div>
    @endif
</div>
