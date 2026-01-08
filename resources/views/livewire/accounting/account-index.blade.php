<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            Daftar Akun (Chart of Accounts)
        </h2>
    </div>

    <!-- Alert Messages -->
    @if (session()->has('message'))
        <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm flex items-center justify-between">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                <span class="font-bold">{{ session('message') }}</span>
            </div>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm flex items-center justify-between">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
                <span class="font-bold">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Financial Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Kas & Bank -->
        <div class="bg-blue-600 rounded-2xl p-6 text-white shadow-lg shadow-blue-200">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-blue-100 font-bold text-xs mb-1 uppercase tracking-wider">Total Kas & Bank</p>
                    <h3 class="text-3xl font-bold text-white">Rp {{ number_format($this->totalCashBank, 0, ',', '.') }}</h3>
                </div>
                <div class="bg-white/20 p-2.5 rounded-xl">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-white/20 flex gap-2">
                <button wire:click="createBankAccount" class="text-xs bg-white text-blue-700 px-4 py-2 rounded-lg font-bold hover:bg-blue-50 transition-colors shadow-sm">
                    + Tambah Akun Bank
                </button>
            </div>
        </div>

        <!-- Piutang Usaha -->
        <div class="bg-orange-500 rounded-2xl p-6 text-white shadow-lg shadow-orange-200">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-white font-bold text-xs mb-1 uppercase tracking-wider opacity-90">Total Piutang Usaha</p>
                    <h3 class="text-3xl font-bold text-white">Rp {{ number_format($this->totalReceivable, 0, ',', '.') }}</h3>
                </div>
                <div class="bg-white/20 p-2.5 rounded-xl">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-white/20">
                <p class="text-xs text-white opacity-90">Estimasi tagihan masuk</p>
            </div>
        </div>

        <!-- Utang Usaha -->
        <div class="bg-red-600 rounded-2xl p-6 text-white shadow-lg shadow-red-200">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-red-100 font-bold text-xs mb-1 uppercase tracking-wider">Total Kewajiban Lancar</p>
                    <h3 class="text-3xl font-bold text-white">Rp {{ number_format($this->totalPayable, 0, ',', '.') }}</h3>
                </div>
                <div class="bg-white/20 p-2.5 rounded-xl">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-white/20">
                <p class="text-xs text-red-100">Kewajiban segera jatuh tempo</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <!-- Filter Actions Bar -->
        <div class="p-4 border-b bg-gray-50 flex flex-col md:flex-row gap-4 items-center justify-between">
            <div class="flex flex-row gap-4 w-full md:w-auto items-center">
                @can('manage accounts')
                <button wire:click="create" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 shadow-sm font-bold flex items-center justify-center gap-2 transition duration-200 text-sm whitespace-nowrap shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    <span class="hidden sm:inline">Tambah Akun</span>
                </button>
                @endcan
                
                <div class="flex-1 md:w-auto md:min-w-[200px]">
                    <select wire:model.live="typeFilter" class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">Semua Tipe</option>
                        <option value="asset">Aset</option>
                        <option value="liability">Kewajiban</option>
                        <option value="equity">Ekuitas</option>
                        <option value="revenue">Pendapatan</option>
                        <option value="expense">Beban</option>
                    </select>
                </div>
            </div>

            <div class="w-full md:flex-1 relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari Kode atau Nama Akun..." 
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm shadow-sm">
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Akun</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Saldo</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($accounts as $account)
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 font-mono">{{ $account->code }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            {{ $account->name }}
                            @if($account->is_system)
                                <span class="ml-2 px-2 py-0.5 inline-flex text-[10px] leading-4 font-bold rounded bg-gray-100 text-gray-600 border border-gray-200">SYSTEM</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                @if($account->type == 'asset') bg-green-100 text-green-800
                                @elseif($account->type == 'liability') bg-red-100 text-red-800
                                @elseif($account->type == 'equity') bg-purple-100 text-purple-800
                                @elseif($account->type == 'revenue') bg-blue-100 text-blue-800
                                @else bg-orange-100 text-orange-800 @endif">
                                {{ ucfirst($account->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 capitalize">{{ str_replace('_', ' ', $account->category) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right font-bold">{{ $account->formatted_balance }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($account->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Aktif</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Non-Aktif</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end items-center gap-3">
                                @can('manage accounts')
                                    <button wire:click="edit({{ $account->id }})" class="text-blue-600 hover:text-blue-900 transition duration-150" title="Edit Akun">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    
                                    @if($account->canDelete())
                                        <button wire:click="delete({{ $account->id }})" wire:confirm="Yakin ingin menghapus akun ini?" class="text-red-600 hover:text-red-900 transition duration-150" title="Hapus Akun">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    @else
                                        <span class="text-gray-300 cursor-not-allowed" title="System Account / Has Transactions">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 00-2 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                        </span>
                                    @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-gray-500 italic">Tidak ada data akun ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="p-4 border-t">
            {{ $accounts->links() }}
        </div>
    </div>


    <!-- Modal Form -->
    @if($showModal)
    <div class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="$set('showModal', false)"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form wire:submit.prevent="store">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">
                                {{ $isEditMode ? 'Edit Akun' : 'Tambah Akun Baru' }}
                            </h3>
                            <button type="button" wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-500">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                        
                        <div class="space-y-4">
                            <!-- Code -->
                            <div>
                                <label for="code" class="block text-sm font-bold text-gray-700">Kode Akun</label>
                                <input type="text" wire:model="code" id="code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="Contoh: 1-1100">
                                @error('code') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-sm font-bold text-gray-700">Nama Akun</label>
                                <input type="text" wire:model="name" id="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="Contoh: Kas Kecil">
                                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Type -->
                            <div>
                                <label for="type" class="block text-sm font-bold text-gray-700">Tipe Akun</label>
                                <select wire:model.live="type" id="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    <option value="asset">Aset (Harta)</option>
                                    <option value="liability">Kewajiban (Utang)</option>
                                    <option value="equity">Ekuitas (Modal)</option>
                                    <option value="revenue">Pendapatan</option>
                                    <option value="expense">Beban (Biaya)</option>
                                </select>
                                @error('type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Category -->
                            <div>
                                <label for="category" class="block text-sm font-bold text-gray-700">Kategori Detail</label>
                                <select wire:model="category" id="category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    @if($type === 'asset')
                                        <option value="cash_bank">Kas & Bank</option>
                                        <option value="current_asset">Aset Lancar Lainnya (Piutang/Stok)</option>
                                        <option value="fixed_asset">Aset Tetap (Peralatan/Gedung)</option>
                                    @elseif($type === 'liability')
                                        <option value="current_liability">Kewajiban Lancar (< 1 Tahun)</option>
                                        <option value="long_term_liability">Kewajiban Jangka Panjang (> 1 Tahun)</option>
                                    @elseif($type === 'equity')
                                        <option value="equity">Ekuitas</option>
                                    @elseif($type === 'revenue')
                                        <option value="operating_revenue">Pendapatan Operasional</option>
                                        <option value="other_revenue">Pendapatan Lain-lain</option>
                                    @elseif($type === 'expense')
                                        <option value="cogs">HPP (Harga Pokok Penjualan)</option>
                                        <option value="operating_expense">Beban Operasional</option>
                                        <option value="other">Beban Lain-lain</option>
                                    @endif
                                </select>
                                @error('category') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Is Active -->
                            <div class="flex items-center pt-2">
                                <input type="checkbox" wire:model="is_active" id="is_active" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded cursor-pointer">
                                <label for="is_active" class="ml-2 block text-sm text-gray-900 cursor-pointer">Status Akun Aktif</label>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-bold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm transition duration-200">
                            Simpan Data
                        </button>
                        <button type="button" wire:click="$set('showModal', false)" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition duration-200">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
