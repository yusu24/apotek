<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-screen-2xl mx-auto">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-slate-900 leading-tight">
                Riwayat Penerimaan Barang
            </h2>
        </div>
    </x-slot>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="mb-4 flex gap-4">
            <a href="{{ route('procurement.goods-receipts.create') }}" wire:navigate
                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 shadow-sm font-bold flex items-center gap-2 transition duration-200 text-sm whitespace-nowrap">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Penerimaan
            </a>
            <input type="text" wire:model.live="search" placeholder="Cari No Surat Jalan / Supplier..." 
                class="w-full md:w-1/3 border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>

        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Terima</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Surat Jalan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier (PO)</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Item</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diterima Oleh</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($receipts as $gr)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ \Carbon\Carbon::parse($gr->received_date)->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-sm font-bold text-gray-900">{{ $gr->delivery_note_number }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $gr->purchaseOrder->supplier->name ?? 'Direct Receipt' }}
                            @if($gr->purchaseOrder)
                                <br><span class="text-xs text-gray-400">Ref: {{ $gr->purchaseOrder->po_number }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $gr->items->count() }} Jenis Produk</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $gr->user->name ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">Belum ada data penerimaan barang.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-4">
            {{ $receipts->links() }}
        </div>
    </div>
</div>
