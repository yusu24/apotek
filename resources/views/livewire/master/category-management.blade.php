<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            Manajemen Kategori Produk
        </h2>
    </div>

    <div class="bg-white rounded-lg shadow border overflow-hidden">
        <div class="p-4 border-b bg-gray-50 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex flex-col md:flex-row gap-4 w-full md:w-auto flex-1 md:items-center">
                <div class="flex items-center gap-2 text-sm text-gray-600 shrink-0">
                    <span class="hidden sm:inline">Tampilkan</span>
                    <select wire:model.live="perPage" class="border-gray-300 rounded-lg py-1.5 pl-3 pr-8 focus:ring-blue-500 focus:border-blue-500 text-sm shadow-sm transition-all bg-white">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>

                <div class="relative w-full md:w-64">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    <input type="text" wire:model.live="search" placeholder="Cari kategori..." 
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:border-blue-500 text-sm bg-white">
                </div>
            </div>

            <div class="flex gap-2 w-full md:w-auto justify-end shrink-0">
                <button x-data @click="$dispatch('open-import-modal')" class="btn btn-import" title="Import Excel">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                    <span class="hidden sm:inline text-sm">Import</span>
                </button>

                <button wire:click="openModal" class="btn btn-primary" title="Tambah Kategori">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span class="hidden sm:inline text-sm">Tambah</span>
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 text-gray-600 font-normal uppercase text-xs">
                    <tr>
                        <th class="px-6 py-4 text-left">No</th>
                        <th class="px-6 py-4 text-left">Nama Kategori</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($categories as $index => $category)
                    <tr class="hover:bg-gray-50/80 transition-colors group">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ ($categories->currentpage()-1) * $categories->perpage() + $index + 1 }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-bold text-gray-900">{{ $category->name }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button wire:click="edit({{ $category->id }})" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-all" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <button wire:click="delete({{ $category->id }})" wire:confirm="Yakin ingin menghapus kategori ini?" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Hapus">
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
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                                </div>
                                <div class="text-sm font-bold text-gray-900">Belum ada data kategori</div>
                                <div class="text-xs text-gray-400 mt-1">Silakan tambah kategori baru untuk produk Anda</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($categories->hasPages())
        <div class="p-4 border-t border-gray-100 bg-gray-50/30">
            @include('components.custom-pagination', ['items' => $categories])
        </div>
        @endif
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
                            class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 shadow-md font-bold capitalize transition duration-200 text-sm">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-md font-bold capitalize transition duration-200 text-sm">
                            {{ $editMode ? 'Update' : 'Simpan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Import Modal -->
    <div x-data="{ openImport: false }" @open-import-modal.window="openImport = true" class="relative z-[100]" aria-labelledby="modal-title" role="dialog" aria-modal="true" x-cloak>
        <div x-show="openImport" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="openImport = false"></div>

        <div x-show="openImport" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex items-end justify-center min-h-full p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                    <form action="{{ route('import.categories') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                    <h3 class="text-base font-semibold leading-6 text-gray-900" id="modal-title">Import Kategori Produk</h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500 mb-4">
                                            Unduh template Excel, isi data kategori, lalu upload kembali.
                                        </p>
                                        
                                        <div class="mb-4">
                                            <a href="{{ route('import.download-category-template') }}" class="text-blue-600 hover:text-blue-800 text-sm font-bold flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                                Download Template Excel
                                            </a>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Upload File Excel</label>
                                            <input type="file" name="file" accept=".xlsx, .xls" required class="mt-1 block w-full text-sm text-gray-500
                                                file:mr-4 file:py-2 file:px-4
                                                file:rounded-full file:border-0
                                                file:text-sm file:font-semibold
                                                file:bg-green-50 file:text-green-700
                                                hover:file:bg-green-100
                                            "/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            <button type="submit" class="inline-flex w-full justify-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 sm:ml-3 sm:w-auto">Import Sekarang</button>
                            <button type="button" @click="openImport = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
