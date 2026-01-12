<div class="p-8 max-w-[210mm] mx-auto bg-white print-container">
    <script>
        window.onload = function() {
            setTimeout(window.print, 1000);
        }
    </script>
    
    <style>
        @media print {
            @page {
                margin: 0;
                size: auto;
            }
            body { 
                background: white; 
                margin: 0;
                padding: 0;
            }
            .print-container {
                padding: 1.5cm;
            }
            .no-print { display: none; }
        }
    </style>

    <!-- Dynamic Settings -->
    @php
        $store_name = \App\Models\Setting::get('store_name', config('app.name', 'Apotek'));
        $store_address = \App\Models\Setting::get('store_address', 'Alamat Toko Belum Diatur');
        $store_phone = \App\Models\Setting::get('store_phone', '-');
    @endphp

    <!-- Header -->
    <div class="flex justify-between items-start border-b border-gray-800 pb-6 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">RETUR PEMBELIAN</h1>
            <p class="text-gray-600 font-bold text-lg">{{ $return->return_no }}</p>
        </div>
        <div class="text-right">
            <h2 class="text-xl font-bold text-gray-800">{{ $store_name }}</h2>
            <p class="text-gray-600 text-sm max-w-xs ml-auto">
                {{ $store_address }}<br>
                Telp: {{ $store_phone }}
            </p>
        </div>
    </div>

    <!-- Info Sections -->
    <div class="flex justify-between mb-8 gap-8">
        <div class="w-1/2">
            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Kepada Supplier:</h3>
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <p class="font-bold text-gray-900 text-lg">{{ optional($return->supplier)->name ?? '-' }}</p>
                <p class="text-gray-600">{{ $return->supplier->address ?? '-' }}</p>
                <p class="text-gray-600">Tel: {{ $return->supplier->phone ?? '-' }}</p>
            </div>
        </div>
        <div class="w-1/2 text-right">
            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Detail Retur:</h3>
            <div class="space-y-1">
                <div class="flex justify-end gap-4">
                    <span class="text-gray-600">Tanggal:</span>
                    <span class="font-bold text-gray-900">{{ $return->created_at->format('d/m/Y') }}</span>
                </div>
                <div class="flex justify-end gap-4">
                    <span class="text-gray-600">Dibuat Oleh:</span>
                    <span class="font-bold text-gray-900">{{ optional($return->user)->name ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <table class="w-full mb-8">
        <thead>
            <tr class="bg-gray-800 text-white text-sm uppercase">
                <th class="py-3 px-4 text-left rounded-l-lg">Produk</th>
                <th class="py-3 px-4 text-center">Batch</th>
                <th class="py-3 px-4 text-center">Qty</th>
                <th class="py-3 px-4 text-right">Harga Beli</th>
                <th class="py-3 px-4 text-right rounded-r-lg">Subtotal</th>
            </tr>
        </thead>
        <tbody class="text-gray-700">
            @foreach($return->items as $item)
            <tr class="border-b border-gray-100 items-center">
                <td class="py-3 px-4">
                    <div class="font-bold text-gray-900">{{ optional($item->product)->name ?? 'Produk Dihapus' }}</div>
                </td>
                <td class="py-3 px-4 text-center text-gray-600">{{ $item->batch->batch_no ?? '-' }}</td>
                <td class="py-3 px-4 text-center font-bold">{{ $item->quantity }}</td>
                <td class="py-3 px-4 text-right">Rp {{ number_format($item->cost_price, 0, ',', '.') }}</td>
                <td class="py-3 px-4 text-right font-bold text-gray-900">Rp {{ number_format($item->quantity * $item->cost_price, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot class="text-gray-900">
            <tr>
                <td colspan="4" class="py-4 px-4 text-right font-bold uppercase tracking-wide">Total Amount</td>
                <td class="py-4 px-4 text-right text-xl font-bold bg-gray-50 rounded-lg">Rp {{ number_format($return->total_amount, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- Footer -->
    <div class="border-t-2 border-dashed border-gray-200 pt-8 mt-12">
        <div class="flex justify-between gap-8 text-center">
            <div class="w-1/3">
                <p class="text-sm text-gray-500 mb-8">Dibuat Oleh,</p>
                <p class="font-bold text-gray-900 underline">{{ optional($return->user)->name ?? '-' }}</p>
                <p class="text-xs text-gray-500 mt-1">Staff Gudang</p>
            </div>
            <div class="w-1/3">
                <p class="text-sm text-gray-500 mb-8">Disetujui Oleh,</p>
                <div class="border-b border-gray-400 w-32 mx-auto my-6"></div>
                <p class="text-xs text-gray-500 mt-1">Kepala Apotek</p>
            </div>
            <div class="w-1/3">
                <p class="text-sm text-gray-500 mb-8">Diterima Supplier,</p>
                <div class="border-b border-gray-400 w-32 mx-auto my-6"></div>
                <p class="text-xs text-gray-500 mt-1">Tanda Tangan & STEMPEL</p>
            </div>
        </div>
        
        <div class="mt-8 text-sm text-gray-500">
            <p class="font-bold mb-1">Catatan:</p>
            <p class="italic">{{ $return->notes ?? '-' }}</p>
        </div>
    </div>
    
    <!-- Action Buttons (No Print) -->
    <div class="no-print fixed bottom-6 right-6 flex gap-3">
        <button onclick="window.print()" class="btn btn-lg btn-primary">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Cetak Retur
        </button>
        <a href="{{ route('inventory.returns.purchase') }}" class="bg-gray-800 text-white px-6 py-3 rounded-full shadow-lg hover:bg-gray-900 font-bold flex items-center gap-2 transform hover:scale-105 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>
    </div>
</div>
