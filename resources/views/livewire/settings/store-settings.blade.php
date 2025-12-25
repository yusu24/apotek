<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            Pengaturan Toko
        </h2>
    </div>

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
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Logo Struk/Invoice</label>
                                    <div class="space-y-3">
                                        <div class="h-24 w-full bg-gray-50 rounded-lg border-2 border-dashed border-gray-200 flex items-center justify-center overflow-hidden relative group">
                                            @if ($store_logo)
                                                <img src="{{ $store_logo->temporaryUrl() }}" class="max-h-full max-w-full object-contain p-2">
                                            @elseif($logo_url)
                                                <img src="{{ $logo_url }}" class="max-h-full max-w-full object-contain p-2">
                                            @else
                                                <div class="text-gray-400 text-xs flex flex-col items-center gap-1">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                    <span>Default Logo</span>
                                                </div>
                                            @endif

                                            @if($logo_url || $store_logo)
                                            <button type="button" wire:click="deleteLogo('store')" wire:confirm="Hapus logo ini?" 
                                                class="absolute top-2 right-2 bg-red-600 text-white p-1.5 rounded-lg shadow-lg hover:bg-red-700 transition-colors z-10"
                                                title="Hapus Logo">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                            @endif
                                        </div>
                                        
                                        <input type="file" wire:model="store_logo" class="block w-full text-xs text-gray-500
                                            file:mr-2 file:py-1.5 file:px-3
                                            file:rounded-lg file:border-0
                                            file:text-xs file:font-semibold
                                            file:bg-blue-50 file:text-blue-700
                                            hover:file:bg-blue-100
                                        "/>
                                        @error('store_logo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Logo Halaman Login</label>
                                    <div class="space-y-3">
                                        <div class="h-24 w-full bg-gray-50 rounded-lg border-2 border-dashed border-gray-200 flex items-center justify-center overflow-hidden relative group">
                                            @if ($login_logo)
                                                <img src="{{ $login_logo->temporaryUrl() }}" class="max-h-full max-w-full object-contain p-2">
                                            @elseif($login_logo_url)
                                                <img src="{{ $login_logo_url }}" class="max-h-full max-w-full object-contain p-2">
                                            @else
                                                <div class="text-gray-400 text-xs flex flex-col items-center gap-1">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                    <span>Default Logo</span>
                                                </div>
                                            @endif

                                            @if($login_logo_url || $login_logo)
                                            <button type="button" wire:click="deleteLogo('login')" wire:confirm="Hapus logo login?" 
                                                class="absolute top-2 right-2 bg-red-600 text-white p-1.5 rounded-lg shadow-lg hover:bg-red-700 transition-colors z-10"
                                                title="Hapus Logo">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                            @endif
                                        </div>
                                        
                                        <input type="file" wire:model="login_logo" class="block w-full text-xs text-gray-500
                                            file:mr-2 file:py-1.5 file:px-3
                                            file:rounded-lg file:border-0
                                            file:text-xs file:font-semibold
                                            file:bg-blue-50 file:text-blue-700
                                            hover:file:bg-blue-100
                                        "/>
                                        @error('login_logo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Logo Sidebar</label>
                                    <div class="space-y-3">
                                        <div class="h-24 w-full bg-gray-50 rounded-lg border-2 border-dashed border-gray-200 flex items-center justify-center overflow-hidden relative group">
                                            @if ($sidebar_logo)
                                                <img src="{{ $sidebar_logo->temporaryUrl() }}" class="max-h-full max-w-full object-contain p-2">
                                            @elseif($sidebar_logo_url)
                                                <img src="{{ $sidebar_logo_url }}" class="max-h-full max-w-full object-contain p-2">
                                            @else
                                                <div class="text-gray-400 text-xs flex flex-col items-center gap-1">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                    <span>Default Logo</span>
                                                </div>
                                            @endif

                                            @if($sidebar_logo_url || $sidebar_logo)
                                            <button type="button" wire:click="deleteLogo('sidebar')" wire:confirm="Hapus logo sidebar?" 
                                                class="absolute top-2 right-2 bg-red-600 text-white p-1.5 rounded-lg shadow-lg hover:bg-red-700 transition-colors z-10"
                                                title="Hapus Logo">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                            @endif
                                        </div>
                                        
                                        <input type="file" wire:model="sidebar_logo" class="block w-full text-xs text-gray-500
                                            file:mr-2 file:py-1.5 file:px-3
                                            file:rounded-lg file:border-0
                                            file:text-xs file:font-semibold
                                            file:bg-blue-50 file:text-blue-700
                                            hover:file:bg-blue-100
                                        "/>
                                        @error('sidebar_logo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                            <p class="mt-2 text-xs text-gray-500 italic">Format: PNG, JPG, GIF max 2MB. Logo struk akan muncul di hasil cetak. Klik tombol sampah di pojok logo untuk menghapus.</p>

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
