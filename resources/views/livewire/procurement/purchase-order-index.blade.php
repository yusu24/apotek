<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            Pesanan Pembelian (PO)
        </h2>
    </div>

    @if (session()->has('message'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('message') }}</p>
                </div>
            </div>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                   <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow p-6">
        <div class="mb-4 flex flex-row justify-between items-center gap-4">
            <div class="flex flex-col sm:flex-row items-center gap-4 flex-1 sm:flex-none">
                <!-- Status Dropdown -->
                <div class="w-full sm:w-auto">
                    <select wire:model.live="status" class="w-full sm:w-40 border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="">Semua Status</option>
                        <option value="draft">Draf</option>
                        <option value="ordered">Dipesan</option>
                        <option value="partial">Sebagian</option>
                        <option value="received">Diterima</option>
                        <option value="cancelled">Dibatalkan</option>
                    </select>
                </div>

                <!-- Search Box -->
                <div class="relative w-full sm:w-48">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    <input type="text" wire:model.live="search" placeholder="Cari No PO / Supplier..." 
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2"
                        style="padding-left: 2.75rem !important;">
                </div>
            </div>

            <!-- Action Button -->
            <div class="shrink-0">
                <a href="{{ route('procurement.purchase-orders.create') }}" wire:navigate
                    class="bg-blue-600 text-white p-2 sm:px-4 sm:py-2 rounded-lg hover:bg-blue-700 shadow-md font-bold capitalize flex items-center justify-center gap-2 transition duration-200 text-sm whitespace-nowrap" title="Buat Pesanan">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span class="hidden sm:inline">Buat Pesanan</span>
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. PO</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supplier</th>
                        {{-- <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th> --}}
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($orders as $po)
                        <tr>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 border-l-4 border-blue-500 pl-4">{{ $po->po_number }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ \Carbon\Carbon::parse($po->date)->format('d M Y') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $po->supplier->name ?? '-' }}</td>
                            {{-- <td class="px-6 py-4 text-sm text-gray-900 text-right font-bold">Rp {{ number_format($po->total_amount, 0, ',', '.') }}</td> --}}
                            <td class="px-6 py-4 text-sm">
                                <span class="px-2 py-1 text-xs font-medium rounded-full 
                                    {{ $po->status === 'received' ? 'bg-green-100 text-green-800' : 
                                       ($po->status === 'cancelled' ? 'bg-red-100 text-red-800' : 
                                       ($po->status === 'partial' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800')) }}">
                                    {{ [
                                        'draft' => 'Draf',
                                        'ordered' => 'Dipesan',
                                        'partial' => 'Sebagian',
                                        'received' => 'Diterima',
                                        'cancelled' => 'Dibatalkan'
                                    ][$po->status] ?? ucfirst($po->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $po->user->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ $po->status === 'draft' ? route('procurement.purchase-orders.edit', $po->id) : route('procurement.purchase-orders.view', $po->id) }}" wire:navigate 
                                        class="text-blue-600 hover:text-blue-900 transition-colors" 
                                        title="{{ $po->status === 'draft' ? 'Edit' : 'Lihat Detail' }}">
                                        @if($po->status === 'draft')
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        @else
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        @endif
                                    </a>
                                    <a href="{{ route('procurement.purchase-orders.print', $po->id) }}" target="_blank"
                                        class="text-gray-600 hover:text-gray-900 transition-colors" title="Cetak PO">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                    </a>
                                    @if($po->goodsReceipts->count() == 0)
                                        <button wire:click="delete({{ $po->id }})" wire:confirm="Hapus PO ini?"
                                            class="text-red-600 hover:text-red-900 transition-colors" title="Hapus">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">Belum ada pesanan pembelian.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    </div>
    </div>
</div>
