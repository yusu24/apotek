<div class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
            <form wire:submit.prevent="save">
                <div class="px-8 pt-8 pb-6 bg-white">
                    <div class="mb-6 flex justify-between items-center">
                        <h3 class="text-xl font-bold text-gray-800 uppercase tracking-tight">
                            {{ $isEditMode ? 'Edit Kategori' : 'Tambah Kategori' }}
                        </h3>
                        <button type="button" wire:click="closeModal" class="text-gray-400 hover:text-gray-600 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1.5">Nama Kategori</label>
                            <input type="text" wire:model="name" placeholder="Contoh: Operasional, Gaji, Listrik..."
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-red-500 focus:border-red-500 transition duration-150">
                            @error('name') <span class="text-xs text-rose-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1.5">Deskripsi (Opsional)</label>
                            <textarea wire:model="description" rows="3" placeholder="Keterangan singkat kategori ini..."
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-red-500 focus:border-red-500 transition duration-150"></textarea>
                            @error('description') <span class="text-xs text-rose-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="px-8 py-6 bg-gray-50 flex justify-end gap-3 rounded-b-2xl border-t border-gray-100">
                    <button type="button" wire:click="closeModal" class="btn btn-secondary">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-lg btn-danger">
                        {{ $isEditMode ? 'Simpan Perubahan' : 'Tambah Kategori' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
