<div class="p-4 max-w-screen-2xl mx-auto">
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-slate-900 leading-tight">
            Konversi Satuan
        </h2>
    </x-slot>

    @if (session()->has('message'))
        <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4">
            <p class="text-sm text-green-700">{{ session('message') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h3 class="text-lg font-bold text-gray-800">Daftar Konversi Satuan</h3>
                <p class="text-sm text-gray-600 mt-1">Kelola konversi antar satuan untuk setiap produk (contoh: 1 Box = 10 Strip)</p>
            </div>
            <button wire:click="openModal" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium flex items-center gap-2">
                <span>+</span> <span class="hidden md:inline">Tambah Konversi</span>
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dari Satuan</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">→</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ke Satuan</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Faktor</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($conversions as $conversion)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $conversion->product->name }}</div>
                                <div class="text-xs text-gray-500">{{ $conversion->product->barcode ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $conversion->fromUnit->name }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <svg class="w-5 h-5 text-gray-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                </svg>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ $conversion->toUnit->name }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="text-sm font-bold text-gray-900">{{ number_format($conversion->conversion_factor, 2) }}</div>
                                <div class="text-xs text-gray-500">1 {{ $conversion->fromUnit->name }} = {{ $conversion->conversion_factor }} {{ $conversion->toUnit->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex flex-col sm:flex-row items-center justify-center gap-2">
                                    <button wire:click="edit({{ $conversion->id }})" 
                                        class="text-blue-600 hover:text-blue-900" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button wire:click="delete({{ $conversion->id }})" wire:confirm="Yakin ingin menghapus konversi ini?" 
                                        class="text-red-600 hover:text-red-900" title="Hapus">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                                <p class="text-sm">Belum ada konversi satuan. Klik "Tambah Konversi" untuk memulai.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data x-cloak>
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" wire:click="closeModal"></div>
            
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative bg-white rounded-lg shadow-2xl w-full max-w-md">
                    <div class="bg-blue-900 text-white px-6 py-4 rounded-t-lg flex items-center justify-between">
                        <h3 class="text-lg font-bold">{{ $editMode ? 'Edit' : 'Tambah' }} Konversi Satuan</h3>
                        <button wire:click="closeModal" class="hover:bg-blue-800 rounded p-1">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form wire:submit="save" class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Produk <span class="text-red-500">*</span>
                            </label>
                            <select wire:model="product_id" class="w-full rounded-lg border-gray-300">
                                <option value="">-- Pilih Produk --</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                            @error('product_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Dari Satuan <span class="text-red-500">*</span>
                            </label>
                            <select wire:model="from_unit_id" class="w-full rounded-lg border-gray-300">
                                <option value="">-- Pilih Satuan Asal --</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                @endforeach
                            </select>
                            @error('from_unit_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Ke Satuan <span class="text-red-500">*</span>
                            </label>
                            <select wire:model="to_unit_id" class="w-full rounded-lg border-gray-300">
                                <option value="">-- Pilih Satuan Tujuan --</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                @endforeach
                            </select>
                            @error('to_unit_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Faktor Konversi <span class="text-red-500">*</span>
                            </label>
                            <input type="number" wire:model="conversion_factor" step="0.01" min="0" class="w-full rounded-lg border-gray-300" placeholder="Contoh: 10">
                            <p class="text-xs text-gray-500 mt-1">Contoh: 1 Box = 10 Strip, maka faktor = 10</p>
                            @error('conversion_factor') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex justify-end gap-3 pt-4">
                            <button type="button" wire:click="closeModal" class="px-4 py-2 border rounded-lg hover:bg-gray-100">
                                Batal
                            </button>
                            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                {{ $editMode ? 'Update' : 'Simpan' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
