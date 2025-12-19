<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-screen-2xl mx-auto">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-slate-900 leading-tight">
                {{ $user_id ? 'Edit User' : 'Tambah User Baru' }}
            </h2>
            <a href="{{ route('admin.users.index') }}" wire:navigate class="text-gray-600 hover:text-gray-900 font-bold flex items-center gap-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali
            </a>
        </div>
    </x-slot>

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
                    <h3 class="font-medium text-gray-800 mb-2">Hak Akses Menu (Custom)</h3>
                    <p class="text-xs text-gray-500 mb-4">
                        Centang menu yang ingin diberikan akses khusus kepada user ini, terlepas dari rolenya.
                    </p>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="menu_permissions.view dashboard" class="w-4 h-4 text-blue-600 rounded border-gray-300">
                            <span class="ml-2 text-sm text-gray-700">Dashboard</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="menu_permissions.view products" class="w-4 h-4 text-blue-600 rounded border-gray-300">
                            <span class="ml-2 text-sm text-gray-700">Obat (Data Obat)</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="menu_permissions.view stock" class="w-4 h-4 text-blue-600 rounded border-gray-300">
                            <span class="ml-2 text-sm text-gray-700">Stok (Inventory)</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="menu_permissions.access pos" class="w-4 h-4 text-blue-600 rounded border-gray-300">
                            <span class="ml-2 text-sm text-gray-700">Kasir (POS)</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="menu_permissions.view reports" class="w-4 h-4 text-blue-600 rounded border-gray-300">
                            <span class="ml-2 text-sm text-gray-700">Laporan</span>
                        </label>
                         <label class="flex items-center">
                            <input type="checkbox" wire:model="menu_permissions.view finance" class="w-4 h-4 text-blue-600 rounded border-gray-300">
                            <span class="ml-2 text-sm text-gray-700">Finance (Laba Rugi & Pengeluaran)</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="menu_permissions.manage settings" class="w-4 h-4 text-blue-600 rounded border-gray-300">
                            <span class="ml-2 text-sm text-gray-700">Pengaturan Toko</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="menu_permissions.manage users" class="w-4 h-4 text-blue-600 rounded border-gray-300">
                            <span class="ml-2 text-sm text-gray-700">Kelola User</span>
                        </label>
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
