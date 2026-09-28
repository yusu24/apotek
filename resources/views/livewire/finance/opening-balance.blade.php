<div class="p-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <h2 class="text-xl md:text-2xl font-bold text-gray-800">
                Neraca Saldo Awal
            </h2>
        </div>
        
        <div class="flex items-center gap-3">
            @if($is_locked)
                <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold flex items-center gap-1 border border-red-200">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path></svg>
                    TERKUNCI
                </span>
                @can('unlock opening balances')
                    <button type="button" wire:click="unlock" class="text-xs text-blue-600 font-bold hover:underline">Buka Kunci</button>
                @endcan
            @else
                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold flex items-center gap-1 border border-green-200">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"></path></svg>
                    DRAFT
                </span>
                @if($openingBalanceId)
                    @can('lock opening balances')
                        <button type="button" wire:click="lock" class="text-xs text-red-600 font-bold hover:underline" wire:confirm="Yakin ingin mengunci neraca saldo awal? Data tidak akan bisa diubah setelah dikunci.">Kunci Sekarang</button>
                    @endcan
                @endif
            @endif
        </div>
    </div>

    @if (session('success'))
        <div class="mb-6 p-3 bg-green-100 border-l-4 border-green-500 text-green-700 rounded shadow-sm flex items-center gap-3 text-xs font-medium">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 p-3 bg-red-100 border-l-4 border-red-500 text-red-700 rounded shadow-sm flex items-center gap-3 text-xs font-medium">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
            {{ session('error') }}
        </div>
    @endif

    <!-- Summary Analysis Cards -->
    <!-- Summary Analysis Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <!-- Total Aset -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-100 dark:border-gray-700 shadow-sm p-5 hover:shadow-md transition-all duration-200 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Aset</span>
                    <div class="w-8 h-8 rounded-full bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                </div>
                <div class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white tracking-tight truncate" title="Rp {{ number_format($summary['total_assets'], 0, ',', '.') }}">
                    Rp {{ number_format($summary['total_assets'], 0, ',', '.') }}
                </div>
                <div class="mt-1 text-xs text-gray-400 dark:text-gray-500">
                    Total aktiva saldo awal
                </div>
            </div>
            <div class="w-full h-1 bg-gray-100 dark:bg-gray-700 rounded-full mt-4 overflow-hidden">
                <div class="h-full bg-blue-600 rounded-full" style="width: 100%;"></div>
            </div>
        </div>

        <!-- Total Liabilitas -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-100 dark:border-gray-700 shadow-sm p-5 hover:shadow-md transition-all duration-200 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Liabilitas</span>
                    <div class="w-8 h-8 rounded-full bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white tracking-tight truncate" title="Rp {{ number_format($summary['total_liabilities'], 0, ',', '.') }}">
                    Rp {{ number_format($summary['total_liabilities'], 0, ',', '.') }}
                </div>
                <div class="mt-1 text-xs text-gray-400 dark:text-gray-500">
                    Total kewajiban saldo awal
                </div>
            </div>
            <div class="w-full h-1 bg-gray-100 dark:bg-gray-700 rounded-full mt-4 overflow-hidden">
                <div class="h-full bg-rose-500 rounded-full" style="width: 100%;"></div>
            </div>
        </div>

        <!-- Total Ekuitas -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-100 dark:border-gray-700 shadow-sm p-5 hover:shadow-md transition-all duration-200 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Ekuitas</span>
                    <div class="w-8 h-8 rounded-full bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                </div>
                <div class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white tracking-tight truncate" title="Rp {{ number_format($summary['total_equity'], 0, ',', '.') }}">
                    Rp {{ number_format($summary['total_equity'], 0, ',', '.') }}
                </div>
                <div class="mt-1 text-xs text-gray-400 dark:text-gray-500">
                    Total modal pemegang saham
                </div>
            </div>
            <div class="w-full h-1 bg-gray-100 dark:bg-gray-700 rounded-full mt-4 overflow-hidden">
                <div class="h-full bg-indigo-600 rounded-full" style="width: 100%;"></div>
            </div>
        </div>

        <!-- Selisih (Balance Check) -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border {{ $summary['is_balanced'] ? 'border-emerald-200 dark:border-emerald-900/50' : 'border-amber-200 dark:border-amber-900/50' }} shadow-sm p-5 hover:shadow-md transition-all duration-200 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-bold {{ $summary['is_balanced'] ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }} uppercase tracking-wider">
                        Balance Check
                    </span>
                    <div class="w-8 h-8 rounded-full {{ $summary['is_balanced'] ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400' : 'bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400' }} flex items-center justify-center">
                        @if($summary['is_balanced'])
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                        @else
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        @endif
                    </div>
                </div>
                <div class="text-2xl lg:text-3xl font-bold {{ $summary['is_balanced'] ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }} tracking-tight truncate" title="Rp {{ number_format(abs($summary['difference']), 0, ',', '.') }}">
                    Rp {{ number_format(abs($summary['difference']), 0, ',', '.') }}
                </div>
                <div class="mt-1 text-xs font-semibold {{ $summary['is_balanced'] ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }}">
                    {{ $summary['is_balanced'] ? 'Neraca Seimbang (Balanced)' : 'Neraca Belum Seimbang!' }}
                </div>
            </div>
            <div class="w-full h-1 bg-gray-100 dark:bg-gray-700 rounded-full mt-4 overflow-hidden">
                <div class="h-full {{ $summary['is_balanced'] ? 'bg-emerald-500' : 'bg-amber-500' }} rounded-full" style="width: 100%;"></div>
            </div>
        </div>
    </div>

    <!-- Form Section -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <form wire:submit.prevent="save">
            <div class="p-8 space-y-12">
                <!-- 0. Metadata -->
                <section class="bg-gray-50 p-6 rounded-xl border border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal Saldo Awal</label>
                            <x-date-picker 
                                wire:model.live="balance_date" 
                                :disabled="$is_locked || !auth()->user()->can('edit opening balances')"
                                class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100 disabled:text-gray-500" />
                            <p class="text-[10px] text-gray-500 mt-1 italic">Sangat disarankan: Gunakan H-1 sebelum toko mulai beroperasi di sistem ini.</p>
                            @error('balance_date') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </section>
                
                <!-- 1. Aset Lancar -->
                <section>
                    <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-3 border-b pb-2">
                        <span class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-sm">1</span>
                        Aset Lancar (Kas & Bank)
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Saldo Awal Kas (Tunai)</label>
                            <div class="relative group" x-data="money($wire.entangle('cash_amount').live)">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 group-focus-within:text-blue-500 transition-colors">Rp.</span>
                                <input type="text" x-bind="input" placeholder="0"
                                    {{ $is_locked || !auth()->user()->can('edit opening balances') ? 'disabled' : '' }}
                                    class="w-full pl-12 rounded-lg border-gray-200 focus:ring-blue-500 focus:border-blue-500 transition-all font-bold text-lg py-3 disabled:bg-gray-50 disabled:text-gray-500">
                            </div>
                            @error('cash_amount') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Saldo Awal Bank</label>
                            <div class="relative group" x-data="money($wire.entangle('bank_amount').live)">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 group-focus-within:text-blue-500 transition-colors">Rp.</span>
                                <input type="text" x-bind="input" placeholder="0"
                                    {{ $is_locked || !auth()->user()->can('edit opening balances') ? 'disabled' : '' }}
                                    class="w-full pl-12 rounded-lg border-gray-200 focus:ring-blue-500 focus:border-blue-500 transition-all font-bold text-lg py-3 disabled:bg-gray-50 disabled:text-gray-500">
                            </div>
                            @error('bank_amount') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Saldo Persediaan Barang</label>
                            <div class="relative group" x-data="money($wire.entangle('inventory_amount').live)">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 group-focus-within:text-blue-500 transition-colors">Rp.</span>
                                <input type="text" x-bind="input" placeholder="0"
                                    {{ $is_locked || !auth()->user()->can('edit opening balances') ? 'disabled' : '' }}
                                    class="w-full pl-12 rounded-lg border-gray-200 focus:ring-blue-500 focus:border-blue-500 transition-all font-bold text-lg py-3 disabled:bg-gray-50 disabled:text-gray-500">
                            </div>
                            <div class="flex justify-between items-center mt-1">
                                <p class="text-[10px] text-gray-500 italic">Total nilai rupiah stok saat ini.</p>
                                <button type="button" wire:click="calculateInventoryFromDb" 
                                    @if($is_locked || !auth()->user()->can('edit opening balances')) disabled @endif
                                    class="text-[10px] text-blue-600 font-bold hover:underline flex items-center gap-1 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                                    Hitung dari Stok
                                </button>
                            </div>
                            @error('inventory_amount') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </section>

                <!-- 2. Aset Tetap -->
                <section>
                    <div class="flex justify-between items-center mb-6 border-b pb-2">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full bg-green-100 text-green-600 flex items-center justify-center text-sm">2</span>
                            Aset Tetap
                        </h3>
                        <button type="button" wire:click="addAsset"
                            @if($is_locked || !auth()->user()->can('edit opening balances')) disabled @endif
                            class="btn btn-sm btn-primary disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            Tambah Aset
                        </button>
                    </div>
                    
                    <div class="space-y-4">
                        @foreach($assets as $index => $asset)
                            <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 p-4 bg-gray-50 rounded-xl border border-gray-100 items-end">
                                <div class="md:col-span-2">
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Nama Aset (Contoh: Peralatan Toko, Etalase)</label>
                                    <input type="text" wire:model.live="assets.{{ $index }}.asset_name" 
                                        {{ $is_locked || !auth()->user()->can('edit opening balances') ? 'disabled' : '' }}
                                        class="w-full rounded-lg border-gray-200 focus:ring-blue-500 focus:border-blue-500 text-sm disabled:bg-gray-50">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Nilai Aset (Rp)</label>
                                    <div x-data="money($wire.entangle('assets.{{ $index }}.amount').live)">
                                    <input type="text" x-bind="input" placeholder="0"
                                        {{ $is_locked || !auth()->user()->can('edit opening balances') ? 'disabled' : '' }}
                                        class="w-full rounded-lg border-gray-200 focus:ring-blue-500 focus:border-blue-500 text-sm font-bold disabled:bg-gray-50">
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <div class="flex-1">
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Tgl Perolehan</label>
                                        <x-date-picker wire:model.live="assets.{{ $index }}.acquisition_date" 
                                            {{ $is_locked || !auth()->user()->can('edit opening balances') ? 'disabled' : '' }}
                                            class="w-full rounded-lg border-gray-200 focus:ring-blue-500 focus:border-blue-500 text-sm disabled:bg-gray-50" />
                                    </div>
                                    <button type="button" wire:click="removeAsset({{ $index }})" 
                                        @if($is_locked || !auth()->user()->can('edit opening balances')) disabled @endif
                                        class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition mb-1 disabled:opacity-30 disabled:cursor-not-allowed">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>

                <!-- 3. Liabilitas (Utang) -->
                <section>
                    <div class="flex justify-between items-center mb-6 border-b pb-2">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full bg-red-100 text-red-600 flex items-center justify-center text-sm">3</span>
                            Liabilitas (Utang Awal)
                        </h3>
                        <button type="button" wire:click="addDebt"
                            @if($is_locked || !auth()->user()->can('edit opening balances')) disabled @endif
                            class="btn btn-sm btn-primary disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            Tambah Utang
                        </button>
                    </div>
                    
                    <div class="space-y-4">
                        @foreach($debts as $index => $debt)
                            <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 p-4 bg-gray-50 rounded-xl border border-gray-100 items-end">
                                <div class="md:col-span-1">
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Jenis Utang</label>
                                    <select wire:model.live="debts.{{ $index }}.debt_type" 
                                        {{ $is_locked || !auth()->user()->can('edit opening balances') ? 'disabled' : '' }}
                                        class="w-full rounded-lg border-gray-200 focus:ring-blue-500 focus:border-blue-500 text-sm disabled:bg-gray-50">
                                        <option value="supplier">Utang Usaha (Supplier)</option>
                                        <option value="bank">Utang Bank</option>
                                    </select>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Nama Pemberi Pinjaman</label>
                                    <input type="text" wire:model.live="debts.{{ $index }}.debt_name" 
                                        {{ $is_locked || !auth()->user()->can('edit opening balances') ? 'disabled' : '' }}
                                        class="w-full rounded-lg border-gray-200 focus:ring-blue-500 focus:border-blue-500 text-sm disabled:bg-gray-50"
                                        placeholder="Contoh: Bank Mandiri, PBF Kimia Farma">
                                </div>
                                <div class="flex gap-2">
                                    <div class="flex-1">
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Nominal (Rp)</label>
                                        <div x-data="money($wire.entangle('debts.{{ $index }}.amount').live)">
                                        <input type="text" x-bind="input" placeholder="0"
                                            {{ $is_locked || !auth()->user()->can('edit opening balances') ? 'disabled' : '' }}
                                            class="w-full rounded-lg border-gray-200 focus:ring-blue-500 focus:border-blue-500 text-sm font-bold disabled:bg-gray-50">
                                        </div>
                                    </div>
                                    <button type="button" wire:click="removeDebt({{ $index }})" 
                                        @if($is_locked || !auth()->user()->can('edit opening balances')) disabled @endif
                                        class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition mb-1 disabled:opacity-30 disabled:cursor-not-allowed">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>

                <!-- 4. Ekuitas (Modal) -->
                <section>
                    <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-3 border-b pb-2">
                        <span class="w-8 h-8 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center text-sm">4</span>
                        Ekuitas (Modal Awal)
                    </h3>
                    <div class="max-w-md">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Modal Awal Pemilik</label>
                        <div class="relative group" x-data="money($wire.entangle('capital_amount').live)">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 group-focus-within:text-purple-500 transition-colors">Rp.</span>
                            <input type="text" x-bind="input" placeholder="0"
                                {{ $is_locked || !auth()->user()->can('edit opening balances') ? 'disabled' : '' }}
                                class="w-full pl-12 rounded-lg border-gray-200 focus:ring-purple-500 focus:border-purple-500 transition-all font-bold text-xl py-3 border-2 border-purple-100 bg-purple-50/10 disabled:bg-gray-100 disabled:text-gray-500">
                        </div>
                        <p class="text-[10px] text-gray-500 mt-2 italic font-medium">Saldo ini akan dicatat sebagai Modal Awal pada sisi Pasiva.</p>
                        @error('capital_amount') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </section>
            </div>

            <!-- Action Bottom -->
            <div class="px-8 py-6 bg-gray-50 border-t flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-2 text-sm">
                    @if($summary['is_balanced'])
                        <div class="px-3 py-1 bg-green-100 text-green-700 rounded-full font-bold flex items-center gap-2 border border-green-200">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            Neraca Siap Disimpan
                        </div>
                    @else
                        <div class="px-3 py-1 bg-orange-100 text-orange-700 rounded-full font-bold flex items-center gap-2 border border-orange-200">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                            Gagal Simpan: Neraca Belum Seimbang
                        </div>
                    @endif
                </div>
                
                <div class="flex items-center gap-3">
                    <button type="button" onclick="history.back()" class="btn btn-lg btn-secondary">
                        Batal
                    </button>
                    <button type="submit"
                        {{ !$summary['is_balanced'] || $is_locked || !auth()->user()->can('edit opening balances') ? 'disabled' : '' }}
                        class="btn btn-lg btn-primary disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-gray-400">
                        <svg class="w-5 h-5 font-bold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                        Simpan & Posting Jurnal
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Instructions -->
    <div class="mt-8 bg-blue-50 rounded-xl p-6 border border-blue-100">
        <h4 class="text-blue-800 font-bold mb-2 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Petunjuk Pengisian
        </h4>
        <ul class="text-xs text-blue-700 space-y-2 list-disc pl-5">
            <li>Data ini diinput untuk menetapkan saldo pembukaan sistem. Pastikan <strong>Total Aset</strong> sama dengan <strong>Liabilitas + Ekuitas</strong>.</li>
            <li>Setelah disimpan, sistem akan otomatis membuat 1 Jurnal Umum jenis Jurnal Pembukaan.</li>
            <li>Jika dikemudian hari ada kesalahan, Admin dapat mengedit data ini dan sistem akan mengupdate jurnal terkait secara otomatis.</li>
            <li>Saldo Kas dan Bank yang Anda input akan langsung muncul di Laporan Neraca dan Riwayat Akun terkait.</li>
        </ul>
    </div>
</div>
