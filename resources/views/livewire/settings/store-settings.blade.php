<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            Pengaturan Toko
        </h2>
    </div>

    @if($success_message)
        <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm flex items-center justify-between">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span class="font-bold">{{ $success_message }}</span>
            </div>
        </div>
    @endif

    <form wire:submit="save">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Identitas Toko -->
            <div class="space-y-6">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-bold text-gray-800 mb-6 border-b pb-2">Identitas Toko</h3>
                    
                    <!-- Logo Upload Grid -->
                    <div class="grid grid-cols-3 gap-4 mb-8">
                        <!-- Logo Struk/Invoice -->
                        <div class="space-y-3" wire:key="logo-store">
                            <label class="block text-[10px] font-bold text-gray-700 uppercase tracking-tight">Logo Struk/Invoice</label>
                            <div class="group relative h-32">
                                <label class="border-2 border-dashed border-gray-200 rounded-xl p-4 h-full flex flex-col items-center justify-center bg-gray-50/50 transition-all hover:bg-gray-50 hover:border-blue-300 cursor-pointer overflow-hidden z-0">
                                    <input type="file" wire:model="store_logo" class="hidden">
                                    @if($store_logo)
                                        <img src="{{ $store_logo->temporaryUrl() }}" class="max-w-full max-h-full object-contain">
                                    @elseif($logo_url)
                                        <img src="{{ $logo_url }}" class="max-w-full max-h-full object-contain">
                                    @else
                                        <div class="flex flex-col items-center text-gray-400">
                                            <svg class="w-8 h-8 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            <span class="text-[10px] font-medium">Default Logo</span>
                                        </div>
                                    @endif
                                </label>
                                
                                @if($logo_url || $store_logo)
                                    <button type="button" 
                                            wire:click.prevent.stop="deleteLogo('store')" 
                                            class="absolute top-2 right-2 p-1.5 bg-white text-red-500 rounded-lg shadow-lg hover:bg-red-50 transition-all z-50 border border-gray-100" 
                                            title="Hapus Logo">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                @endif
                            </div>
                            <div class="flex justify-center">
                                <span class="text-[10px] text-gray-500 truncate max-w-full px-1">{{ $store_logo ? $store_logo->getClientOriginalName() : 'Choose File' }}</span>
                            </div>
                        </div>

                        <!-- Logo Halaman Login -->
                        <div class="space-y-3" wire:key="logo-login">
                            <label class="block text-[10px] font-bold text-gray-700 uppercase tracking-tight">Logo Halaman Login</label>
                            <div class="group relative h-32">
                                <label class="border-2 border-dashed border-gray-200 rounded-xl p-4 h-full flex flex-col items-center justify-center bg-gray-50/50 transition-all hover:bg-gray-50 hover:border-blue-300 cursor-pointer overflow-hidden z-0">
                                    <input type="file" wire:model="login_logo" class="hidden">
                                    @if($login_logo)
                                        <img src="{{ $login_logo->temporaryUrl() }}" class="max-w-full max-h-full object-contain">
                                    @elseif($login_logo_url)
                                        <img src="{{ $login_logo_url }}" class="max-w-full max-h-full object-contain">
                                    @else
                                        <div class="flex flex-col items-center text-gray-400">
                                            <svg class="w-8 h-8 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            <span class="text-[10px] font-medium">Default Logo</span>
                                        </div>
                                    @endif
                                </label>

                                @if($login_logo_url || $login_logo)
                                    <button type="button" 
                                            wire:click.prevent.stop="deleteLogo('login')" 
                                            class="absolute top-2 right-2 p-1.5 bg-white text-red-500 rounded-lg shadow-lg hover:bg-red-50 transition-all z-50 border border-gray-100" 
                                            title="Hapus Logo">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                @endif
                            </div>
                            <div class="flex justify-center">
                                <span class="text-[10px] text-gray-500 truncate max-w-full px-1">{{ $login_logo ? $login_logo->getClientOriginalName() : 'Choose File' }}</span>
                            </div>
                        </div>

                        <!-- Logo Sidebar -->
                        <div class="space-y-3" wire:key="logo-sidebar">
                            <label class="block text-[10px] font-bold text-gray-700 uppercase tracking-tight">Logo Sidebar</label>
                            <div class="group relative h-32">
                                <label class="border-2 border-dashed border-gray-200 rounded-xl p-4 h-full flex flex-col items-center justify-center bg-gray-50/50 transition-all hover:bg-gray-50 hover:border-blue-300 cursor-pointer overflow-hidden z-0">
                                    <input type="file" wire:model="sidebar_logo" class="hidden">
                                    @if($sidebar_logo)
                                        <img src="{{ $sidebar_logo->temporaryUrl() }}" class="max-w-full max-h-full object-contain">
                                    @elseif($sidebar_logo_url)
                                        <img src="{{ $sidebar_logo_url }}" class="max-w-full max-h-full object-contain">
                                    @else
                                        <div class="flex flex-col items-center text-gray-400">
                                            <svg class="w-8 h-8 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            <span class="text-[10px] font-medium">Default Logo</span>
                                        </div>
                                    @endif
                                </label>

                                @if($sidebar_logo_url || $sidebar_logo)
                                    <button type="button" 
                                            wire:click.prevent.stop="deleteLogo('sidebar')" 
                                            class="absolute top-2 right-2 p-1.5 bg-white text-red-500 rounded-lg shadow-lg hover:bg-red-50 transition-all z-50 border border-gray-100" 
                                            title="Hapus Logo">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                @endif
                            </div>
                            <div class="flex justify-center">
                                <span class="text-[10px] text-gray-500 truncate max-w-full px-1">{{ $sidebar_logo ? $sidebar_logo->getClientOriginalName() : 'Choose File' }}</span>
                            </div>
                        </div>
                    </div>
                    <p class="text-[10px] text-gray-400 mb-8 italic">Format: PNG, JPG, GIF max 2MB. Logo struk akan muncul di hasil cetak. Klik tombol sampah di pojok logo untuk menghapus.</p>

                    <div class="space-y-6">
                        <!-- Store Name -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Nama Toko *</label>
                            <input type="text" wire:model="store_name" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            @error('store_name') <span class="text-red-500 text-sm italic">{{ $message }}</span> @enderror
                        </div>

                        <!-- Store Address -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Alamat Lengkap *</label>
                            <textarea wire:model="store_address" rows="3" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                            @error('store_address') <span class="text-red-500 text-sm italic">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Store Phone -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Nomor Telepon *</label>
                                <input type="text" wire:model="store_phone" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                @error('store_phone') <span class="text-red-500 text-sm italic">{{ $message }}</span> @enderror
                            </div>

                            <!-- Store Email -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Email</label>
                                <input type="email" wire:model="store_email" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                @error('store_email') <span class="text-red-500 text-sm italic">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        
                        <!-- NPWP / Tax ID -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">NPWP / Tax ID</label>
                            <input type="text" wire:model="store_tax_id" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Opsional">
                        </div>
                    </div>
                </div>

                <!-- Informasi Pembayaran & Footer -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="mb-4 border-b pb-2">
                        <h3 class="text-lg font-bold text-gray-800">Informasi Pembayaran & Footer Invoice</h3>
                    </div>
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Nama Bank</label>
                                <input type="text" wire:model="store_bank_name" placeholder="Contoh: BCA" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">No. Rekening</label>
                                <input type="text" wire:model="store_bank_account" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Atas Nama</label>
                                <input type="text" wire:model="store_bank_holder" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Catatan Footer (Struk/Invoice)</label>
                            <textarea wire:model="store_footer_note" rows="2" placeholder="Contoh: Barang yang sudah dibeli tidak dapat dikembalikan." class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                            <p class="mt-1.5 text-xs text-gray-500">Pesan ini akan muncul di bagian bawah struk belanja.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Social Media & Action -->
            <div class="space-y-8">
                    <!-- Social Media -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="mb-4 border-b pb-2">
                        <h3 class="text-lg font-bold text-gray-800">Media Sosial</h3>
                    </div>
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Website</label>
                            <div class="relative rounded-lg shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">https://</span>
                                </div>
                                <input type="text" wire:model="store_website" class="block w-full pl-16 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="www.example.com">
                            </div>
                            @error('store_website') <span class="text-red-500 text-sm italic">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Facebook</label>
                            <input type="text" wire:model="store_facebook" placeholder="Username / URL" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Instagram</label>
                            <input type="text" wire:model="store_instagram" placeholder="@username" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                            <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">TikTok</label>
                            <input type="text" wire:model="store_tiktok" placeholder="@username" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                    </div>
                </div>

                <!-- Save Action -->
                <div class="bg-white p-6 rounded-lg shadow-md sticky top-6">
                    <div class="flex items-center justify-between mb-6">
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Status: Aktif</span>
                        <div class="h-2 w-2 rounded-full bg-green-500 animate-pulse"></div>
                    </div>
                    <button type="submit" class="w-full px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-xl font-bold transition duration-200 flex items-center justify-center gap-2">
                         <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Simpan Perubahan
                    </button>
                    <p class="mt-4 text-center text-xs text-gray-400">
                        Perubahan akan langsung diterapkan ke seluruh sistem.
                    </p>
                </div>
            </div>
        </div>
    </form>
</div>
