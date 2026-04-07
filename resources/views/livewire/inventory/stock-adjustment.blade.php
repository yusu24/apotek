<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            Penyesuaian Stok
        </h2>
    </div>

    <!-- Compact Form Card -->
    <div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden">
        <!-- Header inside box -->
        <div class="px-5 py-3 border-b border-gray-50 bg-gray-50/50 flex justify-between items-center">
            <h2 class="text-sm font-bold text-gray-700 truncate mr-4">{{ $product_name }}</h2>
            <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-[10px] font-black shrink-0">
                Stok: {{ $current_stock }}
            </span>
        </div>

        <form wire:submit="save" class="p-5 space-y-4">
            <!-- Type Toggle -->
            <div class="flex items-center justify-between gap-4">
                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Tipe:</label>
                <div class="flex bg-gray-50 p-1 rounded-lg border border-gray-100">
                    <button type="button" 
                            wire:click="$set('adjustment_type', 'add')"
                            class="px-3 py-1 rounded text-[10px] font-bold transition-all {{ $adjustment_type === 'add' ? 'bg-white shadow text-green-600' : 'text-gray-400' }}">
                        Tambah
                    </button>
                    <button type="button" 
                            wire:click="$set('adjustment_type', 'subtract')"
                            class="px-3 py-1 rounded text-[10px] font-bold transition-all {{ $adjustment_type === 'subtract' ? 'bg-white shadow text-red-600' : 'text-gray-400' }}">
                        Kurangi
                    </button>
                </div>
            </div>

            <!-- Amount Input -->
            <div>
                <div class="flex justify-between mb-1">
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Jumlah Unit:</label>
                    @if($quantity)
                    <span class="text-[9px] font-bold text-gray-400 uppercase">
                        Hasil &rarr; <span class="{{ $adjustment_type === 'add' ? 'text-green-500' : 'text-red-500' }}">{{ $adjustment_type === 'add' ? ($current_stock + $quantity) : ($current_stock - $quantity) }}</span>
                    </span>
                    @endif
                </div>
                <input type="number" wire:model.live="quantity" min="1" placeholder="0"
                    class="w-full bg-gray-50 border-gray-100 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-xl font-black py-2 px-3 transition-all">
                @error('quantity') <span class="text-red-500 text-[9px] font-bold uppercase mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Reason -->
            <div>
                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-1">Alasan:</label>
                <textarea wire:model="description" rows="2" 
                    placeholder="Contoh: Koreksi stok"
                    class="w-full bg-gray-50 border-gray-100 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-xs p-2 resize-none transition-all"></textarea>
                @error('description') <span class="text-red-500 text-[9px] font-bold uppercase mt-1 block">{{ $message }}</span> @enderror
            </div>

            @error('save') 
                <div class="bg-red-50 text-red-600 p-2 rounded text-[10px] font-medium border border-red-100 italic">
                    {{ $message }}
                </div>
            @enderror

            <!-- Compact Actions -->
            <div class="flex justify-end items-center gap-3 pt-3 border-t border-gray-100">
                <a href="{{ route('inventory.index') }}" wire:navigate class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-white shadow-md font-bold capitalize flex items-center justify-center gap-2 transition duration-200 text-sm w-fit shrink-0">
                    Batal
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-md font-bold capitalize flex items-center justify-center gap-2 transition duration-200 text-sm w-fit shrink-0">
                    Simpan
                </button>
            </div>
        </form>
    </div>

    <!-- FIFO Note -->
    <p class="mt-3 text-center text-gray-400 text-[9px] font-medium uppercase tracking-tighter italic">
        * Penyesuaian menggunakan logika FIFO
    </p>
</div>
