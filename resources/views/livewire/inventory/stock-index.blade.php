<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-screen-2xl mx-auto">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-slate-900 leading-tight">
                Stok & Inventori (Batch)
            </h2>
        </div>
    </x-slot>

    <!-- Low Stock Alert -->
    @if($low_stock_products->count() > 0)
        <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-400 p-4 shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        Perhatian: Ada <span class="font-bold">{{ $low_stock_products->count() }}</span> produk dengan stok menipis (di bawah minimum).
                    </p>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6 border-b flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <input type="text" wire:model.live="search" placeholder="Cari Batch / Produk..." class="w-full md:w-1/3 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            
            <div class="flex gap-2">
                <a href="{{ route('inventory.index', ['filter_status' => 'all']) }}" wire:navigate 
                    class="px-4 py-2 text-sm font-bold rounded-lg transition duration-200 {{ $filter_status == 'all' ? 'bg-gray-800 text-white shadow-md' : 'bg-white border border-gray-300 text-gray-600 hover:bg-gray-50' }}">
                    Semua
                </a>
                <a href="{{ route('inventory.index', ['filter_status' => 'expired']) }}" wire:navigate 
                    class="px-4 py-2 text-sm font-bold rounded-lg transition duration-200 {{ $filter_status == 'expired' ? 'bg-red-600 text-white shadow-md' : 'bg-white border border-gray-300 text-red-600 hover:bg-red-50' }}">
                    Kadaluarsa
                </a>
            </div>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Batch No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Exp. Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stok Saat Ini (Masuk)</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    @can('adjust stock')
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    @endcan
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($batches as $batch)
                    <tr class="{{ $batch->expired_date < now() ? 'bg-red-50' : '' }}">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $batch->batch_no }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $batch->product->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $batch->expired_date ? \Carbon\Carbon::parse($batch->expired_date)->format('d M Y') : '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 font-bold">
                            {{ $batch->stock_current }} / {{ $batch->unit->name ?? 'pcs' }} 
                            <span class="text-xs font-normal text-gray-400">({{ $batch->stock_in }})</span>
                        </td>
                        <td class="px-6 py-4">
                            @if($batch->stock_current <= 0)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Habis</span>
                            @elseif($batch->expired_date < now())
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Expired</span>
                            @elseif($batch->expired_date < now()->addMonths(3))
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Near Exp</span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Good</span>
                            @endif
                        </td>
                        @can('adjust stock')
                        <td class="px-6 py-4">
                            <a href="{{ route('inventory.adjust', $batch->id) }}" wire:navigate
                                class="text-blue-600 hover:text-blue-900 font-semibold">
                                Sesuaikan
                            </a>
                        </td>
                        @endcan
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">Data stok kosong.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-6">
            {{ $batches->links() }}
        </div>
    </div>
</div>
