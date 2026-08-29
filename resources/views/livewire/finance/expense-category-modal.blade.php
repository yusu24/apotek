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

                    <div class="space-y-5">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">Tipe Kategori <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="flex items-center justify-center gap-2 border-2 rounded-xl py-2.5 px-3 cursor-pointer text-sm transition-all {{ $type === 'expense' ? 'border-red-500 bg-red-50 text-red-700 font-bold shadow-sm' : 'border-gray-200 text-gray-500 hover:bg-gray-50' }}">
                                    <input type="radio" wire:model.live="type" value="expense" class="hidden">
                                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
                                    <span>Pengeluaran</span>
                                </label>
                                <label class="flex items-center justify-center gap-2 border-2 rounded-xl py-2.5 px-3 cursor-pointer text-sm transition-all {{ $type === 'income' ? 'border-green-500 bg-green-50 text-green-700 font-bold shadow-sm' : 'border-gray-200 text-gray-500 hover:bg-gray-50' }}">
                                    <input type="radio" wire:model.live="type" value="income" class="hidden">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                                    <span>Pemasukan</span>
                                </label>
                            </div>
                            @error('type') <span class="text-xs text-rose-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">Nama Kategori <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="name" placeholder="{{ $type === 'income' ? 'Contoh: Bunga Bank, Penjualan Kardus/Limbah, Pendapatan Sewa...' : 'Contoh: Operasional, Gaji, Listrik, ATK...' }}"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition duration-150 text-sm">
                            @error('name') <span class="text-xs text-rose-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">Deskripsi (Opsional)</label>
                            <textarea wire:model="description" rows="3" placeholder="Keterangan singkat kategori ini..."
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition duration-150 text-sm"></textarea>
                            @error('description') <span class="text-xs text-rose-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="px-8 py-6 bg-gray-50 flex justify-end gap-3 rounded-b-2xl border-t border-gray-100">
                    <button type="button" wire:click="closeModal" class="btn btn-secondary">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        {{ $isEditMode ? 'Simpan Perubahan' : 'Tambah Kategori' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
