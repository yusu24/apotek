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
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" {{ $role_name === 'super-admin' ? 'disabled' : '' }}>
                        @foreach($this->permissionStructure as $groupName => $group)
                        <div class="bg-white border rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                            <!-- Group Header with Select All -->
                            <div class="bg-{{ $group['color'] }}-50 px-4 py-3 border-b border-{{ $group['color'] }}-100 flex justify-between items-center">
                                <div class="flex items-center gap-2">
                                    <div class="p-1.5 bg-{{ $group['color'] }}-100 text-{{ $group['color'] }}-600 rounded">
                                        {{-- We use a generic icon here as passing icons dynamically in blade component can be tricky if not using x-dynamic-component --}}
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                    </div>
                                    <h4 class="font-bold text-gray-800 text-sm uppercase tracking-wide">{{ $groupName }}</h4>
                                </div>
                                @if($role_name !== 'super-admin')
                                <button type="button" wire:click="toggleGroup('{{ $groupName }}')" class="text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline">
                                    Pilih Semua
                                </button>
                                @endif
                            </div>

                            <!-- Permission Items -->
                            <div class="p-4 space-y-3">
                                @foreach($group['items'] as $perm => $data)
                                <label class="flex items-start group cursor-pointer {{ $role_name === 'super-admin' ? 'opacity-50' : '' }}">
                                    <div class="flex items-center h-5">
                                        <input type="checkbox" wire:model="menu_permissions.{{ $perm }}" class="w-4 h-4 text-{{ $group['color'] }}-600 rounded border-gray-300 focus:ring-{{ $group['color'] }}-500 transition cursor-pointer" {{ $role_name === 'super-admin' ? 'checked disabled' : '' }}>
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <div class="flex items-center gap-2">
                                            <span class="text-gray-700 group-hover:text-{{ $group['color'] }}-600 transition font-medium">{{ $data['label'] }}</span>
                                            @if(($data['type'] ?? 'view') === 'action')
                                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-orange-100 text-orange-700 border border-orange-200">AKSI</span>
                                            @endif
                                        </div>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
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
