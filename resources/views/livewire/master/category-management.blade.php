<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            Manajemen Kategori Produk
        </h2>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="mb-4 flex flex-row gap-4">
            <button wire:click="openModal"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 shadow-sm font-bold capitalize flex items-center justify-center gap-2 transition duration-200 text-sm whitespace-nowrap w-fit shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span class="hidden sm:inline">Tambah Kategori</span>
            </button>
            <input type="text" wire:model.live="search" placeholder="Cari kategori..." 
                class="w-64 border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($categories as $category)
                        <tr wire:key="cat-{{ $category->id }}">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $category->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $category->slug }}</td>
                            <td class="px-6 py-4 text-right text-sm font-medium">
                                <div class="flex flex-col sm:flex-row items-end sm:items-center justify-end gap-2">
                                    <button wire:click="edit({{ $category->id }})" 
                                        class="text-blue-600 hover:text-blue-900 transition-colors" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <button wire:click="delete({{ $category->id }})" wire:confirm="Yakin ingin menghapus kategori ini?" 
                                        class="text-red-600 hover:text-red-900 transition-colors" title="Hapus">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-center text-gray-500 font-italic">Belum ada data kategori.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $categories->links() }}
        </div>
    </div>

    <!-- Category Modal -->
    @if($showModal)
    <div class="fixed inset-0 z-[60] overflow-y-auto">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" wire:click="closeModal"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden animate-fade-in-up border border-gray-100">
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h3 class="text-xl font-bold text-gray-900">
                        {{ $editMode ? 'Edit Kategori' : 'Tambah Kategori Baru' }}
                    </h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors p-1 rounded-lg hover:bg-gray-200/50">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form wire:submit.prevent="save">
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-[10px] font-medium text-gray-400 uppercase tracking-[0.2em] mb-2">Nama Kategori</label>
                            <input type="text" wire:model="name" autofocus
                                class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-all py-3 font-medium text-gray-700 placeholder:font-normal"
                                placeholder="Contoh: Analgesik">
                            @error('name') <span class="text-red-500 text-xs font-bold mt-2 block flex items-center gap-1">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                {{ $message }}
                            </span> @enderror
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="p-6 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                        <button type="button" wire:click="closeModal"
                            class="px-5 py-2.5 text-gray-500 hover:text-gray-900 font-bold transition-all text-sm capitalize rounded-xl">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-6 py-2.5 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 shadow-lg shadow-blue-200 transition-all text-sm capitalize flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            {{ $editMode ? 'Update' : 'Simpan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
