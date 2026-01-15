<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">
                Riwayat Stok: {{ $product->name }}
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                Kategori: {{ $product->category->name ?? '-' }} | 
                Total Stok: <span class="font-bold">{{ $product->batches()->sum('stock_current') }} {{ $product->unit->name ?? 'pcs' }}</span>
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('pdf.stock-history', [
                'productId' => $productId,
                'type' => $filterType,
                'start_date' => $startDate,
                'end_date' => $endDate
            ]) }}" target="_blank"
                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 shadow-md font-bold text-sm flex items-center justify-center gap-2 transition duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span>Cetak PDF</span>
            </a>
        </div>
    </div>

    <!-- Active Batches Section -->
    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
        <div class="p-6 border-b bg-blue-50">
            <h3 class="text-lg font-bold text-gray-900">Daftar Batch Aktif</h3>
        </div>
        <div class="overflow-x-auto">
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
    </div>

    <!-- Transaction History Section -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6 border-b bg-gray-50">
            <h3 class="text-lg font-bold text-gray-900">Riwayat Transaksi (100 Terakhir)</h3>
        </div>
        
        <!-- Filter Section -->
        <div class="p-6 bg-gray-50/50 border-b">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                <!-- Search -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Pencarian</label>
                    <input type="text" 
                        wire:model.live.debounce.300ms="searchTerm" 
                        placeholder="Cari batch, referensi..."
                        class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Type Filter -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Tipe Transaksi</label>
                    <select wire:model.live="filterType" 
                        class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="all">Semua Tipe</option>
                        <option value="in">Masuk</option>
                        <option value="out">Keluar</option>
                        <option value="sale">Penjualan</option>
                        <option value="adjustment">Opname</option>
                    </select>
                </div>

                <!-- Start Date -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                    <input type="date" 
                        wire:model.live="startDate" 
                        class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- End Date -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal Akhir</label>
                    <input type="date" 
                        wire:model.live="endDate" 
                        class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Reset Button -->
                <div>
                    <button wire:click="resetFilters" 
                        class="w-full px-4 py-2 text-sm bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-medium transition-colors">
                        Reset Filter
                    </button>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Batch</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Masuk</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Keluar</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Saldo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Referensi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Keterangan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($this->stockMovements as $movement)
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
                        
                        <!-- Masuk -->
                        <td class="px-6 py-4 text-sm font-bold text-center text-green-600">
                            @if($movement->quantity > 0)
                                +{{ number_format($movement->quantity, 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </td>

                        <!-- Keluar -->
                        <td class="px-6 py-4 text-sm font-bold text-center text-red-600">
                            @if($movement->quantity < 0)
                                {{ number_format(abs($movement->quantity), 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </td>

                        <!-- Saldo (Stock After) -->
                        <td class="px-6 py-4 text-sm font-bold text-center text-blue-600">
                            {{ number_format($movement->stock_after ?? $movement->running_balance, 0, ',', '.') }}
                        </td>

                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $movement->doc_ref ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $movement->description }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $movement->user->name ?? 'System' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-4 text-center text-gray-500">Belum ada riwayat transaksi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
