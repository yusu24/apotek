<div class="p-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Master Satuan</h2>
        <p class="text-sm text-gray-500 mt-1">Kelola daftar satuan produk (contoh: Pcs, Strip, Box, Karton)</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-4 border-b border-gray-100 bg-gray-50/50 flex flex-col md:flex-row gap-4 items-center">
            <x-button wire:click="openModal" variant="primary" class="gap-2 whitespace-nowrap">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Tambah Unit
            </x-button>
            <div class="relative max-w-sm w-full">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="text" wire:model.live="search" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Cari nama satuan...">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-widest">No</th>
                        <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-widest">Nama Satuan</th>
                        <th class="px-6 py-3 text-center text-[10px] font-bold text-gray-500 uppercase tracking-widest">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($units as $index => $unit)
                    <tr class="hover:bg-gray-50/80 transition-colors group">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ ($units->currentpage()-1) * $units->perpage() + $index + 1 }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-bold text-gray-900">{{ $unit->name }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button wire:click="edit({{ $unit->id }})" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-all" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <button wire:click="delete({{ $unit->id }})" wire:confirm="Yakin ingin menghapus satuan ini?" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Hapus">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center text-gray-300 mb-3 border border-gray-100">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path></svg>
                                </div>
                                <div class="text-sm font-bold text-gray-900">Belum ada data satuan</div>
                                <div class="text-xs text-gray-400 mt-1">Silakan tambah satuan baru untuk digunakan pada produk</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($units->hasPages())
        <div class="p-4 border-t border-gray-100 bg-gray-50/30">
            {{ $units->links() }}
        </div>
        @endif
    </div>

    <!-- Modal -->
    @if($showModal)
    <div class="fixed inset-0 z-[100] overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true" wire:click="closeModal">
                <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form wire:submit.prevent="save">
                    <div class="bg-white px-6 pt-6 pb-4">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-gray-900">
                                {{ $editMode ? 'Edit Satuan' : 'Tambah Satuan Baru' }}
                            </h3>
                            <button type="button" wire:click="closeModal" class="text-gray-400 hover:text-gray-500 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Nama Satuan <span class="text-red-500">*</span></label>
                                <input type="text" wire:model="name" class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="Contoh: Strip, Box, Karton, Tablet">
                                @error('name') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3">
                        <x-button type="submit" variant="primary" class="px-6 py-2.5 shadow-md active:scale-95 text-sm">
                            {{ $editMode ? 'Update Satuan' : 'Simpan Satuan' }}
                        </x-button>
                        <x-button type="button" variant="secondary" wire:click="closeModal" class="px-6 py-2.5 text-sm">
                            Batal
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
