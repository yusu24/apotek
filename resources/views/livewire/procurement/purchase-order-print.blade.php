<div class="p-8 max-w-[210mm] mx-auto bg-white">
    <script>
        window.onload = function() {
            setTimeout(window.print, 1000);
        }
    </script>
    
    <style>
        @media print {
            body { 
                background: white; 
                margin: 0;
                padding: 0;
            }
            .no-print { display: none; }
        }
    </style>

    <!-- Header -->
    <div class="flex justify-between items-start border-b border-gray-800 pb-6 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">PURCHASE ORDER</h1>
            <p class="text-gray-600 font-bold text-lg">{{ $po->po_number }}</p>
        </div>
        <div class="text-right">
            <h2 class="text-xl font-bold text-gray-800">{{ config('app.name', 'Apotek') }}</h2>
            <p class="text-gray-600 text-sm max-w-xs ml-auto">
                Jl. Raya Apotek No. 123<br>
                Kota Sehat, Indonesia<br>
                Telp: (021) 1234-5678
            </p>
        </div>
    </div>

    <!-- Info Sections -->
    <div class="flex justify-between mb-8 gap-8">
        <div class="w-1/2">
            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Kepada Supplier:</h3>
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <p class="font-bold text-gray-900 text-lg">{{ $po->supplier->name }}</p>
                <p class="text-gray-600">{{ $po->supplier->address ?? '-' }}</p>
                <p class="text-gray-600">Tel: {{ $po->supplier->phone ?? '-' }}</p>
            </div>
        </div>
        <div class="w-1/2 text-right">
            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Detail Pesanan:</h3>
            <div class="space-y-1">
                <div class="flex justify-end gap-4">
                    <span class="text-gray-600">Tanggal:</span>
                    <span class="font-bold text-gray-900">{{ \Carbon\Carbon::parse($po->order_date)->format('d/m/Y') }}</span>
                </div>
                <div class="flex justify-end gap-4">
                    <span class="text-gray-600">Status:</span>
                    <span class="font-bold uppercase {{ $po->status == 'completed' ? 'text-green-600' : 'text-amber-600' }}">{{ $po->status }}</span>
                </div>
                <div class="flex justify-end gap-4">
                    <span class="text-gray-600">Dibuat Oleh:</span>
                    <span class="font-bold text-gray-900">{{ $po->user->name ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <table class="w-full mb-8">
        <thead>
            <tr class="bg-gray-800 text-white text-sm uppercase">
                <th class="py-3 px-4 text-left rounded-l-lg">Produk</th>
                <th class="py-3 px-4 text-right">Harga Satuan</th>
                <th class="py-3 px-4 text-center">Qty</th>
                <th class="py-3 px-4 text-right rounded-r-lg">Total</th>
            </tr>
        </thead>
        <tbody class="text-gray-700">
            @foreach($po->items as $item)
            <tr class="border-b border-gray-100 items-center">
                <td class="py-3 px-4">
                    <div class="font-bold text-gray-900">{{ $item->product->name }}</div>
                    <div class="text-xs text-gray-500">Unit: {{ $item->product->unit->name ?? 'pcs' }}</div>
                </td>
                <td class="py-3 px-4 text-right">Rp {{ number_format($item->buy_price, 0, ',', '.') }}</td>
                <td class="py-3 px-4 text-center font-bold">{{ $item->quantity }}</td>
                <td class="py-3 px-4 text-right font-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot class="text-gray-900">
            <tr>
                <td colspan="3" class="py-4 px-4 text-right font-bold uppercase tracking-wide">Total Akhir</td>
                <td class="py-4 px-4 text-right text-xl font-bold bg-gray-50 rounded-lg">Rp {{ number_format($po->total_amount, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- Footer -->
    <div class="border-t-2 border-dashed border-gray-200 pt-8 mt-12 flex justify-between items-end">
        <div class="text-sm text-gray-500 max-w-md">
            <p class="font-bold mb-1">Catatan:</p>
            <p class="italic">{{ $po->notes ?? 'Tidak ada catatan tambahan.' }}</p>
        </div>
        <div class="flex gap-12 text-center">
            <div>
                <p class="text-sm text-gray-500 mb-16">Diterima Oleh,</p>
                <div class="h-px bg-gray-400 w-32 mx-auto"></div>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-16">Disetujui Oleh,</p>
                <div class="h-px bg-gray-400 w-32 mx-auto"></div>
            </div>
        </div>
    </div>

    <!-- Action Buttons (No Print) -->
    <div class="no-print fixed bottom-6 right-6 flex gap-3">
        <button onclick="window.print()" class="bg-blue-600 text-white px-6 py-3 rounded-full shadow-lg hover:bg-blue-700 font-bold flex items-center gap-2 transform hover:scale-105 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Print Order
        </button>
        <button onclick="history.back()" class="bg-gray-800 text-white px-6 py-3 rounded-full shadow-lg hover:bg-gray-900 font-bold flex items-center gap-2 transform hover:scale-105 transition-all">
            Kembali
        </button>
    </div>
</div>
