<div class="p-6">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Manajemen Satuan Produk</h1>
            <p class="text-gray-500 text-sm">Atur satuan dasar dan konversi bertingkat untuk setiap produk.</p>
        </div>
    </div>



    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-4 border-b bg-gray-50 flex flex-col md:flex-row gap-4">
            <div class="w-full md:w-64">
                <select wire:model.live="category_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-none">
                 <input type="text" wire:model.live.debounce.300ms="search" 
                    class="w-full md:w-64 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                    placeholder="Cari produk...">
            </div>
        </div>
        <!-- Mobile-friendly table wrapper -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Produk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Kategori</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Satuan Dasar</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Jml Konversi</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($products as $product)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                            <div class="text-xs text-gray-500">{{ $product->barcode }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                            {{ $product->category->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-center whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $product->unit->name ?? 'Belum diset' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center text-sm text-gray-500 whitespace-nowrap">
                            {{ $product->unit_conversions_count ?? 0 }} Level
                        </td>
                        <td class="px-6 py-4 text-right text-sm font-medium whitespace-nowrap">
                            <div class="flex items-center justify-end gap-3">
                                <button wire:click="edit({{ $product->id }})" 
                                    class="text-blue-600 hover:text-blue-900 transition-colors" title="Atur Satuan">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <button wire:click="delete({{ $product->id }})" wire:confirm="Yakin ingin mereset pengaturan satuan ini?" 
                                    class="text-red-600 hover:text-red-900 transition-colors" title="Hapus Pengaturan Satuan">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            Tidak ada data produk ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $products->links() }}
        </div>
    </div>

    <!-- Modal Form -->
    @if($showModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="$set('showModal', false)"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                        Pengaturan Satuan: {{ $editingProduct->name }}
                    </h3>
                    
                    <div class="mt-6 space-y-6">
                        <!-- Base Unit Section -->
                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                            <label class="block text-sm font-medium text-blue-900 mb-1">Satuan Dasar (Terkecil)</label>
                            <p class="text-xs text-blue-700 mb-3">Semua stok akan dihitung dalam satuan ini.</p>
                            <select wire:model="base_unit_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="">Pilih Satuan Dasar</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                @endforeach
                            </select>
                            @error('base_unit_id') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Conversions Section -->
                        <div>
                            <div class="flex justify-between items-center mb-3">
                                <label class="block text-sm font-medium text-gray-700">Satuan Bertingkat (Konversi)</label>
                                <button type="button" wire:click="addConversion" class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-blue-700 bg-blue-100 hover:bg-blue-200">
                                    + Tambah Satuan
                                </button>
                            </div>

                            @if(empty($conversions))
                                <div class="text-center py-6 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300 text-gray-500 text-sm">
                                    Belum ada konversi tambahan.
                                </div>
                            @else
                                <div class="space-y-3">
                                    @foreach($conversions as $index => $conv)
                                    <div class="flex items-center gap-3 bg-gray-50 p-3 rounded-lg border border-gray-200">
                                        <div class="flex-1">
                                            <label class="text-xs text-gray-500 block mb-1">1 Satuan Besar</label>
                                            <select wire:model="conversions.{{ $index }}.from_unit_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                                <option value="">Pilih Satuan</option>
                                                @foreach($units as $unit)
                                                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                        <div class="pt-5 text-gray-400 font-bold">=</div>

                                        <div class="flex-1">
                                            <label class="text-xs text-gray-500 block mb-1">Jumlah</label>
                                            <input type="number" step="any" wire:model="conversions.{{ $index }}.input_factor" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="10">
                                        </div>

                                        <div class="flex-1">
                                            <label class="text-xs text-gray-500 block mb-1">Ke Satuan</label>
                                            <select wire:model="conversions.{{ $index }}.to_unit_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                                <option value="{{ $base_unit_id }}">{{ $units->find($base_unit_id)?->name ?? 'Satuan Dasar' }}</option>
                                                @foreach($conversions as $otherIndex => $otherConv)
                                                    @if($index !== $otherIndex && !empty($otherConv['from_unit_id']))
                                                        <option value="{{ $otherConv['from_unit_id'] }}">{{ $units->find($otherConv['from_unit_id'])?->name }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="pt-5">
                                            <button wire:click="removeConversion({{ $index }})" class="text-red-500 hover:text-red-700">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    </div>
                                    @error("conversions.{$index}.from_unit_id") <span class="text-red-600 text-xs block">{{ $message }}</span> @enderror
                                    @error("conversions.{$index}.conversion_factor") <span class="text-red-600 text-xs block">{{ $message }}</span> @enderror
                                    @endforeach
                                </div>
                                <div class="mt-4 p-3 bg-gray-50 rounded-lg border border-gray-200">
                                    <p class="text-[11px] text-gray-500 leading-relaxed italic">
                                        <span class="font-bold text-blue-600">Tips:</span> Untuk konversi bertingkat, pastikan satuan yang menjadi target sudah didefinisikan terlebih dahulu. 
                                        Contoh: Set <b>1 Box = 12 Strip</b> dulu, baru set <b>1 Karton = 12 Box</b>.
                                    </p>
                                </div>
                                @error('conversions') <span class="text-red-600 text-xs mt-2 block">{{ $message }}</span> @enderror
                            @endif
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                    <button type="button" wire:click="$set('showModal', false)"
                        class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 shadow-md font-bold capitalize transition duration-200 text-sm">
                        Batal
                    </button>
                    <button type="button" wire:click="save"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-md font-bold capitalize transition duration-200 text-sm">
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
