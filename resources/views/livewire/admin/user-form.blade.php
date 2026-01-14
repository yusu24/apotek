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
                <!-- Row 1: Name & Email -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Nama Lengkap *</label>
                        <input type="text" wire:model="name" 
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                        @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Email *</label>
                        <input type="email" wire:model="email" 
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                        @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Row 2: Role Selection -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Role / Jabatan *</label>
                    <select wire:model.live="role_name" 
                        class="w-full md:w-1/2 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="">Pilih Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                        @endforeach
                    </select>
                    @error('role_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Hak Akses Section -->
                <div class="p-6 bg-gray-50 rounded-xl border border-gray-200">
                    <div class="flex items-center justify-between mb-8">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-blue-600 rounded-lg text-white">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 00-2 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800 uppercase tracking-wider text-sm">Hak Akses Menu (Custom)</h3>
                                <p class="text-sm text-gray-500">Berikan izin akses khusus ke menu tertentu untuk user ini.</p>
                            </div>
                        </div>
                        @if($role_name === 'super-admin')
                            <div class="px-4 py-1 bg-amber-100 border border-amber-200 text-amber-700 rounded-full text-xs font-bold flex items-center gap-2">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path></svg>
                                AKSES PENUH (TERKUNCI)
                            </div>
                        @endif
                    </div>
                    
                    <fieldset class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12" {{ $role_name === 'super-admin' ? 'disabled' : '' }}>
                        <!-- Dashboard -->
                        <div class="space-y-4">
                            <h4 class="font-bold text-gray-700 text-xs uppercase tracking-widest border-l-4 border-blue-500 pl-3 flex items-center gap-2">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                                Dashboard
                            </h4>
                            <div class="space-y-4">
                                <label class="flex items-center group cursor-pointer {{ $role_name === 'super-admin' ? 'opacity-50' : '' }}">
                                    <input type="checkbox" wire:model="menu_permissions.view dashboard" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 transition cursor-pointer" {{ $role_name === 'super-admin' ? 'checked' : '' }}>
                                    <span class="ml-3 text-sm text-gray-700 group-hover:text-blue-600 transition font-medium">Halaman Dashboard</span>
                                </label>
                                <p class="text-sm text-gray-400 italic font-medium leading-tight">* Panduan Aplikasi tersedia untuk semua user.</p>
                            </div>
                        </div>

                        <!-- Pengaturan Produk -->
                        <div class="space-y-4">
                            <h4 class="font-bold text-gray-700 text-xs uppercase tracking-widest border-l-4 border-green-500 pl-3 flex items-center gap-2">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1 3 3 3h10c2 0 3-1 3-3V7c0-2-1-3-3-3H7c-2 0-3 1-3 3zM9 12h6M9 16h6M9 8h6"></path></svg>
                                Produk
                            </h4>
                            <div class="space-y-4">
                                @php
                                    $productItems = [
                                        'view products' => 'Obat / Produk',
                                        'manage categories' => 'Kategori Produk',
                                        'manage product units' => 'Satuan Produk',
                                        'manage suppliers' => 'Daftar Supplier',
                                        'import_master_data' => 'Import Master Data (Excel)'
                                    ];
                                @endphp
                                @foreach($productItems as $perm => $label)
                                <label class="flex items-center group cursor-pointer {{ $role_name === 'super-admin' ? 'opacity-50' : '' }}">
                                    <input type="checkbox" wire:model="menu_permissions.{{ $perm }}" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 transition cursor-pointer" {{ $role_name === 'super-admin' ? 'checked' : '' }}>
                                    <span class="ml-3 text-sm text-gray-700 group-hover:text-blue-600 transition font-medium">{{ $label }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Stok & Pengadaan -->
                        <div class="space-y-4">
                            <h4 class="font-bold text-gray-700 text-xs uppercase tracking-widest border-l-4 border-orange-500 pl-3 flex items-center gap-2">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                Stok & Pengadaan
                            </h4>
                            <div class="space-y-4">
                                @php
                                        'view stock' => 'Stok & Opname',
                                        'adjust stock' => 'Penyesuaian Stok',
                                        'view stock movements' => 'Riwayat Mutasi Stok',
                                        'view purchase orders' => 'Pesanan (PO)',
                                        'view goods receipts' => 'Penerimaan Pesanan'
                                    ];
                                @endphp
                                @foreach($stockItems as $perm => $label)
                                <label class="flex items-center group cursor-pointer {{ $role_name === 'super-admin' ? 'opacity-50' : '' }}">
                                    <input type="checkbox" wire:model="menu_permissions.{{ $perm }}" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 transition cursor-pointer" {{ $role_name === 'super-admin' ? 'checked' : '' }}>
                                    <span class="ml-3 text-sm text-gray-700 group-hover:text-blue-600 transition font-medium">{{ $label }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Kasir -->
                        <div class="space-y-4">
                            <h4 class="font-bold text-gray-700 text-xs uppercase tracking-widest border-l-4 border-purple-500 pl-3 flex items-center gap-2">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                                Penjualan
                            </h4>
                            <div class="space-y-4">
                                <label class="flex items-center group cursor-pointer {{ $role_name === 'super-admin' ? 'opacity-50' : '' }}">
                                    <input type="checkbox" wire:model="menu_permissions.access pos" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 transition cursor-pointer" {{ $role_name === 'super-admin' ? 'checked' : '' }}>
                                    <span class="ml-3 text-sm text-gray-700 group-hover:text-blue-600 transition font-medium">Halaman Kasir (POS)</span>
                                </label>
                                <label class="flex items-center group cursor-pointer {{ $role_name === 'super-admin' ? 'opacity-50' : '' }}">
                                    <input type="checkbox" wire:model="menu_permissions.view sales reports" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 transition cursor-pointer" {{ $role_name === 'super-admin' ? 'checked' : '' }}>
                                    <span class="ml-3 text-sm text-gray-700 group-hover:text-blue-600 transition font-medium">Laporan Penjualan</span>
                                </label>
                            </div>
                        </div>

                        <!-- Keuangan & Administrasi -->
                        <div class="space-y-4">
                            <h4 class="font-bold text-gray-700 text-xs uppercase tracking-widest border-l-4 border-red-500 pl-3 flex items-center gap-2">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                Keuangan & Akuntansi
                            </h4>
                            <div class="space-y-4">
                                @php
                                        'view profit loss' => 'Laporan Laba Rugi',
                                        'view income statement' => 'Laporan Arus Kas',
                                        'view balance sheet' => 'Laporan Neraca',
                                        'view trial balance' => 'Neraca Saldo',
                                        'view ppn report' => 'Laporan PPN',
                                        'view product margin report' => 'Laporan Margin Produk',
                                        'view ap aging report' => 'Laporan Umur Hutang',
                                        'manage opening balances' => 'Neraca Awal (Saldo Awal)',
                                        'view general ledger' => 'Buku Besar'
                                    ];
                                @endphp
                                @foreach($financeItems as $perm => $label)
                                <label class="flex items-center group cursor-pointer {{ $role_name === 'super-admin' ? 'opacity-50' : '' }}">
                                    <input type="checkbox" wire:model="menu_permissions.{{ $perm }}" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 transition cursor-pointer" {{ $role_name === 'super-admin' ? 'checked' : '' }}>
                                    <span class="ml-3 text-sm text-gray-700 group-hover:text-blue-600 transition font-medium">{{ $label }}</span>
                                </label>
                                @endforeach

                                <!-- Journal Section -->
                                <div class="space-y-4">
                                    <label class="flex items-center group cursor-pointer {{ $role_name === 'super-admin' ? 'opacity-50' : '' }}">
                                        <input type="checkbox" wire:model="menu_permissions.view journals" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 transition cursor-pointer" {{ $role_name === 'super-admin' ? 'checked' : '' }}>
                                        <span class="ml-3 text-sm text-gray-700 group-hover:text-blue-600 transition font-medium">Jurnal Umum</span>
                                    </label>
                                    <div class="ml-7">
                                        <label class="flex items-center group cursor-pointer {{ $role_name === 'super-admin' ? 'opacity-50' : '' }}">
                                            <input type="checkbox" wire:model="menu_permissions.create journal" class="w-4 h-4 text-blue-500 rounded border-gray-300" {{ $role_name === 'super-admin' ? 'checked' : '' }}>
                                            <span class="ml-3 text-sm text-gray-600 group-hover:text-blue-500 transition font-medium">Input Jurnal</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- COA Section -->
                                <div class="space-y-4">
                                    <label class="flex items-center group cursor-pointer {{ $role_name === 'super-admin' ? 'opacity-50' : '' }}">
                                        <input type="checkbox" wire:model="menu_permissions.view accounts" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 transition cursor-pointer" {{ $role_name === 'super-admin' ? 'checked' : '' }}>
                                        <span class="ml-3 text-sm text-gray-700 group-hover:text-blue-600 transition font-medium">Daftar Akun (COA)</span>
                                    </label>
                                    <div class="ml-7">
                                        <label class="flex items-center group cursor-pointer {{ $role_name === 'super-admin' ? 'opacity-50' : '' }}">
                                            <input type="checkbox" wire:model="menu_permissions.manage accounts" class="w-4 h-4 text-blue-500 rounded border-gray-300" {{ $role_name === 'super-admin' ? 'checked' : '' }}>
                                            <span class="ml-3 text-sm text-gray-600 group-hover:text-blue-500 transition font-medium">Kelola Akun</span>
                                        </label>
                                    </div>
                                </div>

                                <label class="flex items-center group cursor-pointer {{ $role_name === 'super-admin' ? 'opacity-50' : '' }}">
                                    <input type="checkbox" wire:model="menu_permissions.view expenses" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 transition cursor-pointer" {{ $role_name === 'super-admin' ? 'checked' : '' }}>
                                    <span class="ml-3 text-sm text-gray-700 group-hover:text-blue-600 transition font-medium">Pengeluaran / Biaya</span>
                                </label>
                                <label class="flex items-center group cursor-pointer {{ $role_name === 'super-admin' ? 'opacity-50' : '' }}">
                                    <input type="checkbox" wire:model="menu_permissions.manage expense categories" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 transition cursor-pointer" {{ $role_name === 'super-admin' ? 'checked' : '' }}>
                                    <span class="ml-3 text-sm text-gray-700 group-hover:text-blue-600 transition font-medium">Kategori Pengeluaran</span>
                                </label>
                            </div>
                        </div>

                        <!-- Retur Barang -->
                        <div class="space-y-4">
                            <h4 class="font-bold text-gray-700 text-xs uppercase tracking-widest border-l-4 border-yellow-500 pl-3 flex items-center gap-2">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v14a2 2 0 002 2h12a2 2 0 002-2z"></path></svg>
                                Retur Barang
                            </h4>
                            <div class="space-y-4">
                                <label class="flex items-center group cursor-pointer {{ $role_name === 'super-admin' ? 'opacity-50' : '' }}">
                                    <input type="checkbox" wire:model="menu_permissions.manage sales returns" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 transition cursor-pointer" {{ $role_name === 'super-admin' ? 'checked' : '' }}>
                                    <span class="ml-3 text-sm text-gray-700 group-hover:text-blue-600 transition font-medium">Retur Penjualan (Customer)</span>
                                </label>
                                <label class="flex items-center group cursor-pointer {{ $role_name === 'super-admin' ? 'opacity-50' : '' }}">
                                    <input type="checkbox" wire:model="menu_permissions.manage purchase returns" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 transition cursor-pointer" {{ $role_name === 'super-admin' ? 'checked' : '' }}>
                                    <span class="ml-3 text-sm text-gray-700 group-hover:text-blue-600 transition font-medium">Retur Pembelian (Supplier)</span>
                                </label>
                            </div>
                        </div>

                        <!-- Pengaturan -->
                        <div class="space-y-4">
                            <h4 class="font-bold text-gray-700 text-xs uppercase tracking-widest border-l-4 border-gray-500 pl-3 flex items-center gap-2">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                Pengaturan Sistem
                            </h4>
                            <div class="space-y-4">
                                @php
                                    $settingItems = [
                                        'manage settings' => 'Identitas Toko',
                                        'manage pos settings' => 'Konfigurasi Kasir',
                                        'manage users' => 'Kelola User',
                                        'view activity logs' => 'Log Aktivitas'
                                    ];
                                @endphp
                                @foreach($settingItems as $perm => $label)
                                <label class="flex items-center group cursor-pointer {{ $role_name === 'super-admin' ? 'opacity-50' : '' }}">
                                    <input type="checkbox" wire:model="menu_permissions.{{ $perm }}" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 transition cursor-pointer" {{ $role_name === 'super-admin' ? 'checked' : '' }}>
                                    <span class="ml-3 text-sm text-gray-700 group-hover:text-blue-600 transition font-medium">{{ $label }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </fieldset>
                </div>

                <!-- Password -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Password {{ $user_id ? '(Kosongkan jika tidak ingin mengubah)' : '*' }}
                        </label>
                        <input type="password" wire:model="password" 
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                        @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Konfirmasi Password</label>
                        <input type="password" wire:model="password_confirmation" 
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                    </div>
                </div>
            </div>

            <div class="mt-10 flex justify-end gap-3 border-t pt-6">
                <a href="{{ route('admin.users.index') }}" wire:navigate
                    class="px-5 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-bold transition text-sm">
                    Batal
                </a>
                <button type="submit" 
                    class="px-6 py-2.5 bg-gray-800 text-white rounded-lg hover:bg-gray-700 font-bold transition shadow-md flex items-center gap-2 text-sm uppercase tracking-wide">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Simpan User
                </button>
            </div>
        </form>
    </div>
</div>
