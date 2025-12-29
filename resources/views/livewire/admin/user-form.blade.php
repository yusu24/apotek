<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            {{ $user_id ? 'Edit User' : 'Tambah User Baru' }}
        </h2>
        <a href="{{ route('admin.users.index') }}" wire:navigate class="text-gray-600 hover:text-gray-900 font-bold flex items-center gap-1">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-6">
        <form wire:submit="save">
            <div class="space-y-6">
                <!-- Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap *</label>
                    <input type="text" wire:model="name" 
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                    <input type="email" wire:model="email" 
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Role -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Role / Jabatan *</label>
                    <select wire:model="role_name" 
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Pilih Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                        @endforeach
                    </select>
                    @error('role_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Special Access -->
                <!-- Special Access -->
                <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <h3 class="font-bold text-gray-800 mb-2 uppercase tracking-wider text-xs flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 00-2 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        Hak Akses Menu (Custom)
                    </h3>
                    <p class="text-xs text-gray-500 mb-6">
                        Berikan izin akses khusus ke menu tertentu untuk user ini.
                    </p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <!-- Dashboard & Panduan -->
                        <div class="space-y-4">
                            <h4 class="font-bold text-gray-700 text-[10px] uppercase tracking-widest border-l-2 border-blue-500 pl-2">Utama</h4>
                            <div class="space-y-2">
                                <label class="flex items-center group cursor-pointer">
                                    <input type="checkbox" wire:model="menu_permissions.view dashboard" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 transition cursor-pointer">
                                    <span class="ml-3 text-sm text-gray-700 group-hover:text-blue-600 transition">Dashboard</span>
                                </label>
                                <label class="flex items-center group cursor-pointer">
                                    <input type="checkbox" wire:model="menu_permissions.view guide" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 transition cursor-pointer">
                                    <span class="ml-3 text-sm text-gray-700 group-hover:text-blue-600 transition">Panduan Aplikasi</span>
                                </label>
                            </div>
                        </div>

                        <!-- Produk -->
                        <div class="space-y-4">
                            <h4 class="font-bold text-gray-700 text-[10px] uppercase tracking-widest border-l-2 border-green-500 pl-2">Pengaturan Produk</h4>
                            <div class="space-y-2">
                                <label class="flex items-center group cursor-pointer">
                                    <input type="checkbox" wire:model="menu_permissions.view products" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 transition cursor-pointer">
                                    <span class="ml-3 text-sm text-gray-700 group-hover:text-blue-600 transition">Obat / Produk</span>
                                </label>
                                <label class="flex items-center group cursor-pointer">
                                    <input type="checkbox" wire:model="menu_permissions.manage categories" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 transition cursor-pointer">
                                    <span class="ml-3 text-sm text-gray-700 group-hover:text-blue-600 transition">Kategori Produk</span>
                                </label>
                                <label class="flex items-center group cursor-pointer">
                                    <input type="checkbox" wire:model="menu_permissions.manage product units" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 transition cursor-pointer">
                                    <span class="ml-3 text-sm text-gray-700 group-hover:text-blue-600 transition">Satuan Produk</span>
                                </label>
                                <label class="flex items-center group cursor-pointer">
                                    <input type="checkbox" wire:model="menu_permissions.manage suppliers" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 transition cursor-pointer">
                                    <span class="ml-3 text-sm text-gray-700 group-hover:text-blue-600 transition">Pemasok</span>
                                </label>
                            </div>
                        </div>

                        <!-- Inventory & Pengadaan -->
                        <div class="space-y-4">
                            <h4 class="font-bold text-gray-700 text-[10px] uppercase tracking-widest border-l-2 border-orange-500 pl-2">Stok & Pengadaan</h4>
                            <div class="space-y-2">
                                <label class="flex items-center group cursor-pointer">
                                    <input type="checkbox" wire:model="menu_permissions.view stock" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 transition cursor-pointer">
                                    <span class="ml-3 text-sm text-gray-700 group-hover:text-blue-600 transition">Stok & Opname</span>
                                </label>
                                <label class="flex items-center group cursor-pointer">
                                    <input type="checkbox" wire:model="menu_permissions.adjust stock" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 transition cursor-pointer">
                                    <span class="ml-3 text-sm text-gray-700 group-hover:text-blue-600 transition">Penyesuaian Stok</span>
                                </label>
                                <label class="flex items-center group cursor-pointer">
                                    <input type="checkbox" wire:model="menu_permissions.view purchase orders" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 transition cursor-pointer">
                                    <span class="ml-3 text-sm text-gray-700 group-hover:text-blue-600 transition">Pesanan Pembelian (PO)</span>
                                </label>
                                <label class="flex items-center group cursor-pointer">
                                    <input type="checkbox" wire:model="menu_permissions.view goods receipts" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 transition cursor-pointer">
                                    <span class="ml-3 text-sm text-gray-700 group-hover:text-blue-600 transition">Penerimaan Pesanan</span>
                                </label>
                            </div>
                        </div>

                        <!-- POS & Sales -->
                        <div class="space-y-4">
                            <h4 class="font-bold text-gray-700 text-[10px] uppercase tracking-widest border-l-2 border-purple-500 pl-2">Transaksi</h4>
                            <div class="space-y-2">
                                <label class="flex items-center group cursor-pointer">
                                    <input type="checkbox" wire:model="menu_permissions.access pos" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 transition cursor-pointer">
                                    <span class="ml-3 text-sm text-gray-700 group-hover:text-blue-600 transition">Kasir (POS)</span>
                                </label>
                                <label class="flex items-center group cursor-pointer">
                                    <input type="checkbox" wire:model="menu_permissions.view sales reports" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 transition cursor-pointer">
                                    <span class="ml-3 text-sm text-gray-700 group-hover:text-blue-600 transition">Laporan Penjualan</span>
                                </label>
                            </div>
                        </div>

                        <!-- Keuangan -->
                        <div class="space-y-4">
                            <h4 class="font-bold text-gray-700 text-[10px] uppercase tracking-widest border-l-2 border-red-500 pl-2">Keuangan</h4>
                            <div class="space-y-2">
                                <label class="flex items-center group cursor-pointer">
                                    <input type="checkbox" wire:model="menu_permissions.view expenses" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 transition cursor-pointer">
                                    <span class="ml-3 text-sm text-gray-700 group-hover:text-blue-600 transition">Pengeluaran</span>
                                </label>
                                <label class="flex items-center group cursor-pointer">
                                    <input type="checkbox" wire:model="menu_permissions.view profit loss" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 transition cursor-pointer">
                                    <span class="ml-3 text-sm text-gray-700 group-hover:text-blue-600 transition">Laba Rugi</span>
                                </label>
                                <label class="flex items-center group cursor-pointer">
                                    <input type="checkbox" wire:model="menu_permissions.view balance sheet" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 transition cursor-pointer">
                                    <span class="ml-3 text-sm text-gray-700 group-hover:text-blue-600 transition">Neraca</span>
                                </label>
                                <label class="flex items-center group cursor-pointer">
                                    <input type="checkbox" wire:model="menu_permissions.view journals" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 transition cursor-pointer">
                                    <span class="ml-3 text-sm text-gray-700 group-hover:text-blue-600 transition">Jurnal Umum</span>
                                </label>
                                <label class="flex items-center group cursor-pointer">
                                    <input type="checkbox" wire:model="menu_permissions.manage expense categories" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 transition cursor-pointer">
                                    <span class="ml-3 text-sm text-gray-700 group-hover:text-blue-600 transition">Kategori Pengeluaran</span>
                                </label>
                            </div>
                        </div>

                        <!-- Sistem -->
                        <div class="space-y-4">
                            <h4 class="font-bold text-gray-700 text-[10px] uppercase tracking-widest border-l-2 border-gray-500 pl-2">Sistem</h4>
                            <div class="space-y-2">
                                <label class="flex items-center group cursor-pointer">
                                    <input type="checkbox" wire:model="menu_permissions.manage settings" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 transition cursor-pointer">
                                    <span class="ml-3 text-sm text-gray-700 group-hover:text-blue-600 transition">Pengaturan Toko</span>
                                </label>
                                <label class="flex items-center group cursor-pointer">
                                    <input type="checkbox" wire:model="menu_permissions.manage users" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 transition cursor-pointer">
                                    <span class="ml-3 text-sm text-gray-700 group-hover:text-blue-600 transition">Kelola User</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Password {{ $user_id ? '(Kosongkan jika tidak ingin mengubah)' : '*' }}
                    </label>
                    <input type="password" wire:model="password" 
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Password Confirmation -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password</label>
                    <input type="password" wire:model="password_confirmation" 
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3 border-t pt-6">
                <a href="{{ route('admin.users.index') }}" wire:navigate
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-bold transition text-sm">
                    Batal
                </a>
                <button type="submit" 
                    class="px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700 font-bold transition shadow-md flex items-center gap-2 text-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Simpan User
                </button>
            </div>
        </form>
    </div>
</div>
