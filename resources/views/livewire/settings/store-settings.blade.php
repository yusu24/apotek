<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-screen-2xl mx-auto">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-slate-900 leading-tight">
                Pengaturan Toko
            </h2>
        </div>
    </x-slot>

    @if($success_message)
        <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">
                        {{ $success_message }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow p-6">
        <form wire:submit="save">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Left Column: Identity -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Identitas Toko -->
                    <div class="bg-white rounded-lg shadow-lg p-6 border border-gray-100">
                        <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Identitas Toko</h3>
                        <div class="space-y-4">
                             <!-- Logo Upload -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Logo Toko</label>
                                <div class="flex items-center gap-4">
                                    @if ($store_logo)
                                        <img src="{{ $store_logo->temporaryUrl() }}" class="h-20 w-20 object-contain rounded border border-gray-200">
                                    @elseif($logo_url)
                                        <img src="{{ $logo_url }}" class="h-20 w-20 object-contain rounded border border-gray-200">
                                    @else
                                        <div class="h-20 w-20 bg-gray-100 rounded border border-gray-200 flex items-center justify-center text-gray-400">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                    @endif
                                    
                                    <div class="flex-1">
                                        <input type="file" wire:model="store_logo" class="block w-full text-sm text-gray-500
                                            file:mr-4 file:py-2 file:px-4
                                            file:rounded-full file:border-0
                                            file:text-sm file:font-semibold
                                            file:bg-blue-50 file:text-blue-700
                                            hover:file:bg-blue-100
                                        "/>
                                        <p class="mt-1 text-xs text-gray-500">PNG, JPG, GIF up to 2MB. Logo akan muncul di struk/invoice.</p>
                                        @error('store_logo') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Store Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Toko *</label>
                                <input type="text" wire:model="store_name" 
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                                @error('store_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Store Address -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap *</label>
                                <textarea wire:model="store_address" rows="3"
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm"></textarea>
                                @error('store_address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Store Phone -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon *</label>
                                    <input type="text" wire:model="store_phone" 
                                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                                    @error('store_phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <!-- Store Email -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                    <input type="email" wire:model="store_email" 
                                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                                    @error('store_email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            
                             <!-- NPWP / Tax ID -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">NPWP / Tax ID</label>
                                <input type="text" wire:model="store_tax_id" 
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm"
                                    placeholder="Opsional">
                            </div>
                        </div>
                    </div>

                    <!-- Informasi Pembayaran & Footer -->
                    <div class="bg-white rounded-lg shadow-lg p-6 border border-gray-100">
                        <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Informasi Pembayaran & Footer Invoice</h3>
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Bank</label>
                                    <input type="text" wire:model="store_bank_name" placeholder="Contoh: BCA"
                                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Rekening</label>
                                    <input type="text" wire:model="store_bank_account" 
                                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Atas Nama</label>
                                    <input type="text" wire:model="store_bank_holder" 
                                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Footer (Struk/Invoice)</label>
                                <textarea wire:model="store_footer_note" rows="2" placeholder="Contoh: Barang yang sudah dibeli tidak dapat dikembalikan."
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm"></textarea>
                                <p class="mt-1 text-xs text-gray-500">Pesan ini akan muncul di bagian bawah struk belanja.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Social Media & Action -->
                <div class="space-y-6">
                     <!-- Social Media -->
                    <div class="bg-white rounded-lg shadow-lg p-6 border border-gray-100">
                        <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Media Sosial</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                                <div class="relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">https://</span>
                                    </div>
                                    <input type="text" wire:model="store_website" class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-16 sm:text-sm border-gray-300 rounded-md" placeholder="www.example.com">
                                </div>
                                @error('store_website') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Facebook</label>
                                <input type="text" wire:model="store_facebook" placeholder="Username / URL"
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Instagram</label>
                                <input type="text" wire:model="store_instagram" placeholder="@username"
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                            </div>
                             <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">TikTok</label>
                                <input type="text" wire:model="store_tiktok" placeholder="@username"
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                            </div>
                        </div>
                    </div>

                    <!-- Save Action -->
                    <div class="bg-white rounded-lg shadow-lg p-6 border border-gray-100 sticky top-6">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-sm text-gray-500">Terakhir update: Sekarang</span>
                        </div>
                        <button type="submit" 
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
