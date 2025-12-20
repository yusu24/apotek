<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-screen-2xl mx-auto">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-semibold text-slate-900 leading-tight">
                    Riwayat Stok: {{ $product->name }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Kategori: {{ $product->category->name ?? '-' }} | 
                    Total Stok: <span class="font-bold">{{ $product->batches()->sum('stock_current') }} {{ $product->unit->name ?? 'pcs' }}</span>
                </p>
            </div>
            <a href="{{ route('inventory.index') }}" wire:navigate class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 font-semibold">
                ← Kembali
            </a>
        </div>
    </x-slot>

    <!-- Active Batches Section -->
    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
        <div class="p-6 border-b bg-blue-50">
            <h3 class="text-lg font-bold text-gray-900">Daftar Batch Aktif</h3>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Batch No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Exp. Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stok Saat Ini</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($productBatches as $batch)
                <tr>
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $batch->batch_no }}</td>
                    <td class="px-6 py-4 text-sm {{ $batch->expired_date < now() ? 'text-red-600 font-bold' : 'text-gray-500' }}">
                        {{ $batch->expired_date ? \Carbon\Carbon::parse($batch->expired_date)->format('d M Y') : '-' }}
                    </td>
                    <td class="px-6 py-4 text-sm font-bold text-gray-900">{{ $batch->stock_current }}</td>
                    <td class="px-6 py-4">
                        @if($batch->expired_date < now())
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Expired</span>
                        @elseif($batch->expired_date < now()->addMonths(3))
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Near Exp</span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Good</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">Tidak ada batch aktif.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Transaction History Section -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6 border-b bg-gray-50">
            <h3 class="text-lg font-bold text-gray-900">Riwayat Transaksi (100 Terakhir)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Batch</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Referensi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Keterangan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($stockMovements as $movement)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                            {{ $movement->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($movement->type == 'in')
                                <span class="px-2 py-1 rounded text-xs font-semibold bg-green-100 text-green-800">Masuk</span>
                            @elseif($movement->type == 'out')
                                <span class="px-2 py-1 rounded text-xs font-semibold bg-red-100 text-red-800">Keluar</span>
                            @elseif($movement->type == 'sale')
                                <span class="px-2 py-1 rounded text-xs font-semibold bg-blue-100 text-blue-800">Penjualan</span>
                            @elseif($movement->type == 'adjustment')
                                <span class="px-2 py-1 rounded text-xs font-semibold bg-purple-100 text-purple-800">Opname</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $movement->batch->batch_no ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm font-bold whitespace-nowrap {{ $movement->quantity > 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $movement->quantity > 0 ? '+' : '' }}{{ $movement->quantity }}
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $movement->doc_ref ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $movement->description }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $movement->user->name ?? 'System' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">Belum ada riwayat transaksi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Stock Movement Summary -->
        <div class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
            <h4 class="font-bold text-gray-700 mb-2">Ringkasan Pergerakan Stok</h4>
            <div class="grid grid-cols-3 gap-4">
                @php
                    $totalIn = $stockMovements->where('quantity', '>', 0)->sum('quantity');
                    $totalOut = abs($stockMovements->where('quantity', '<', 0)->sum('quantity'));
                    $netChange = $totalIn - $totalOut;
                @endphp
                <div class="text-center">
                    <p class="text-xs text-gray-500">Total Masuk</p>
                    <p class="text-lg font-bold text-green-600">+{{ number_format($totalIn, 0, ',', '.') }}</p>
                </div>
                <div class="text-center">
                    <p class="text-xs text-gray-500">Total Keluar</p>
                    <p class="text-lg font-bold text-red-600">-{{ number_format($totalOut, 0, ',', '.') }}</p>
                </div>
                <div class="text-center">
                    <p class="text-xs text-gray-500">Sisa (Net)</p>
                    <p class="text-lg font-bold {{ $netChange >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $netChange >= 0 ? '+' : '' }}{{ number_format($netChange, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
