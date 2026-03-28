<div class="p-6">
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('admin.roles.index') }}" wire:navigate class="p-2 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors shadow-sm">
            <svg class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">{{ $role_id ? 'Edit Jabatan' : 'Tambah Jabatan' }}</h2>
            <p class="text-sm text-gray-500">Atur nama jabatan dan hak akses standar yang dimiliki.</p>
        </div>
    </div>

    <form wire:submit="save" class="space-y-8">
        <!-- Role Name Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6">
                <div class="max-w-md">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Nama Jabatan <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="name" class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm" placeholder="Contoh: Apoteker, Admin Gudang, dll">
                    @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Permissions Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                <h3 class="font-bold text-gray-800 uppercase tracking-wide text-sm">Pengaturan Hak Akses (Standard)</h3>
                <p class="text-xs text-gray-500 mt-1 italic">Izin yang dipilih di sini akan otomatis diterapkan ke user dengan jabatan ini.</p>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($this->permissionStructure as $group => $data)
                    <div class="border border-gray-100 rounded-xl overflow-hidden flex flex-col h-full bg-gray-50/30">
                        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between bg-white">
                            <h4 class="text-xs font-bold text-{{ $data['color'] }}-700 flex items-center gap-2 uppercase tracking-wider">
                                {{ $group }}
                            </h4>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" wire:click="toggleGroup('{{ $group }}')" class="sr-only peer" 
                                    @php
                                        $items = array_keys($data['items']);
                                        $allChecked = true;
                                        foreach ($items as $p) { if (empty($menu_permissions[$p])) { $allChecked = false; break; } }
                                    @endphp
                                    {{ $allChecked ? 'checked' : '' }}>
                                <div class="w-7 h-4 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-3 after:w-3 after:transition-all peer-checked:bg-{{ $data['color'] }}-500"></div>
                            </label>
                        </div>
                        <div class="p-3 space-y-2 flex-1">
                            @foreach ($data['items'] as $perm => $idat)
                                <label class="flex items-start gap-2 p-2 rounded-lg hover:bg-white transition-colors cursor-pointer group">
                                    <input type="checkbox" wire:model="menu_permissions.{{ $perm }}" class="rounded border-gray-300 text-{{ $data['color'] }}-600 focus:ring-{{ $data['color'] }}-500 mt-0.5">
                                    <div>
                                        <div class="text-xs font-medium text-gray-800 group-hover:text-{{ $data['color'] }}-700 transition-colors">{{ $idat['label'] }}</div>
                                        <div class="text-[9px] text-gray-400 font-mono">{{ $perm }}</div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 sticky bottom-6 z-10">
            <a href="{{ route('admin.roles.index') }}" wire:navigate class="px-6 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors font-bold shadow-sm">
                Batal
            </a>
            <button type="submit" class="px-8 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors font-bold shadow-lg flex items-center gap-2">
                <svg wire:loading.remove wire:target="save" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                </svg>
                <svg wire:loading wire:target="save" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <span>Simpan Perubahan</span>
            </button>
        </div>
    </form>
</div>
