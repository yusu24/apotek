<div class="p-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            Neraca Awal (Saldo Awal)
        </h2>
        <p class="text-sm text-gray-500 mt-1">Input saldo awal untuk menyeimbangkan laporan neraca Anda.</p>
    </div>

    @if (session('success'))
        <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded shadow-sm flex items-center gap-3">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded shadow-sm flex items-center gap-3 font-bold">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
            {{ session('error') }}
        </div>
    @endif

    <!-- Summary Analysis Cards -->
    <div class="grid grid-cols-4 gap-6 mb-8" style="grid-template-columns: repeat(4, minmax(0, 1fr));">
        <div class="bg-white p-6 rounded-xl shadow-md border-b-4 border-blue-500">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Total Aset</p>
            <p class="text-2xl font-black text-gray-800">Rp {{ number_format($summary['total_assets'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-md border-b-4 border-red-500">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Total Liabilitas</p>
            <p class="text-2xl font-black text-gray-800">Rp {{ number_format($summary['total_liabilities'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-md border-b-4 border-purple-500">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Total Ekuitas</p>
            <p class="text-2xl font-black text-gray-800">Rp {{ number_format($summary['total_equity'], 0, ',', '.') }}</p>
        </div>
        <div class="p-6 rounded-xl shadow-md border-b-4 {{ $summary['is_balanced'] ? 'bg-green-50 border-green-500' : 'bg-orange-50 border-orange-500' }}">
            <p class="text-xs font-bold {{ $summary['is_balanced'] ? 'text-green-600' : 'text-orange-600' }} uppercase tracking-wider mb-1">Selisih (Balance Check)</p>
            <div class="flex items-center gap-2">
                <p class="text-2xl font-black {{ $summary['is_balanced'] ? 'text-green-700' : 'text-orange-700' }}">
                    Rp {{ number_format(abs($summary['difference']), 0, ',', '.') }}
                </p>
                @if($summary['is_balanced'])
                    <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                @else
                    <svg class="w-6 h-6 text-orange-600 animate-pulse" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                @endif
            </div>
            @if(!$summary['is_balanced'])
                <p class="text-[10px] text-orange-600 font-bold mt-1 italic uppercase tracking-tighter">Neraca Belum Seimbang!</p>
            @else
                <p class="text-[10px] text-green-600 font-bold mt-1 italic uppercase tracking-tighter">Neraca Seimbang (Balanced)</p>
            @endif
        </div>
    </div>

    <!-- Form Section -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <form wire:submit.prevent="save">
            <div class="p-8 space-y-12">
                
                <!-- 1. Aset Lancar -->
                <section>
                    <h3 class="text-lg font-black text-gray-800 mb-6 flex items-center gap-3 border-b pb-2">
                        <span class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-sm">1</span>
                        Aset Lancar (Kas & Bank)
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Saldo Awal Kas (Tunai)</label>
                            <div class="relative group">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 group-focus-within:text-blue-500 transition-colors">Rp</span>
                                <input type="number" wire:model.live="cash_amount" step="0.01"
                                    class="w-full pl-12 rounded-lg border-gray-200 focus:ring-blue-500 focus:border-blue-500 transition-all font-bold text-lg py-3">
                            </div>
                            @error('cash_amount') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Saldo Awal Bank</label>
                            <div class="relative group">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 group-focus-within:text-blue-500 transition-colors">Rp</span>
                                <input type="number" wire:model.live="bank_amount" step="0.01"
                                    class="w-full pl-12 rounded-lg border-gray-200 focus:ring-blue-500 focus:border-blue-500 transition-all font-bold text-lg py-3">
                            </div>
                            @error('bank_amount') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </section>

                <!-- 2. Aset Tetap -->
                <section>
                    <div class="flex justify-between items-center mb-6 border-b pb-2">
                        <h3 class="text-lg font-black text-gray-800 flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full bg-green-100 text-green-600 flex items-center justify-center text-sm">2</span>
                            Aset Tetap
                        </h3>
                        <button type="button" wire:click="addAsset" 
                            class="text-sm bg-green-50 text-green-600 px-4 py-2 rounded-lg font-bold hover:bg-green-100 transition flex items-center gap-2 border border-green-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            Tambah Aset
                        </button>
                    </div>
                    
                    <div class="space-y-4">
                        @foreach($assets as $index => $asset)
                            <div class="grid grid-cols-4 gap-4 p-4 bg-gray-50 rounded-xl border border-gray-100 items-end">
                                <div class="md:col-span-2">
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Nama Aset (Contoh: Peralatan Toko, Etalase)</label>
                                    <input type="text" wire:model.live="assets.{{ $index }}.asset_name" 
                                        class="w-full rounded-lg border-gray-200 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Nilai Aset (Rp)</label>
                                    <input type="number" wire:model.live="assets.{{ $index }}.amount" step="0.01"
                                        class="w-full rounded-lg border-gray-200 focus:ring-blue-500 focus:border-blue-500 text-sm font-bold">
                                </div>
                                <div class="flex gap-2">
                                    <div class="flex-1">
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Tgl Perolehan</label>
                                        <input type="date" wire:model.live="assets.{{ $index }}.acquisition_date" 
                                            class="w-full rounded-lg border-gray-200 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                    </div>
                                    <button type="button" wire:click="removeAsset({{ $index }})" 
                                        class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition mb-1">
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
                        <h3 class="text-lg font-black text-gray-800 flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full bg-red-100 text-red-600 flex items-center justify-center text-sm">3</span>
                            Liabilitas (Utang Awal)
                        </h3>
                        <button type="button" wire:click="addDebt" 
                            class="text-sm bg-red-50 text-red-600 px-4 py-2 rounded-lg font-bold hover:bg-red-100 transition flex items-center gap-2 border border-red-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            Tambah Utang
                        </button>
                    </div>
                    
                    <div class="space-y-4">
                        @foreach($debts as $index => $debt)
                            <div class="grid grid-cols-4 gap-4 p-4 bg-gray-50 rounded-xl border border-gray-100 items-end">
                                <div class="md:col-span-1">
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Jenis Utang</label>
                                    <select wire:model.live="debts.{{ $index }}.debt_type" 
                                        class="w-full rounded-lg border-gray-200 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                        <option value="supplier">Utang Usaha (Supplier)</option>
                                        <option value="bank">Utang Bank</option>
                                    </select>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Nama Pemberi Pinjaman</label>
                                    <input type="text" wire:model.live="debts.{{ $index }}.debt_name" 
                                        class="w-full rounded-lg border-gray-200 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                        placeholder="Contoh: Bank Mandiri, PBF Kimia Farma">
                                </div>
                                <div class="flex gap-2">
                                    <div class="flex-1">
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Nominal (Rp)</label>
                                        <input type="number" wire:model.live="debts.{{ $index }}.amount" step="0.01"
                                            class="w-full rounded-lg border-gray-200 focus:ring-blue-500 focus:border-blue-500 text-sm font-bold">
                                    </div>
                                    <button type="button" wire:click="removeDebt({{ $index }})" 
                                        class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition mb-1">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>

                <!-- 4. Ekuitas (Modal) -->
                <section>
                    <h3 class="text-lg font-black text-gray-800 mb-6 flex items-center gap-3 border-b pb-2">
                        <span class="w-8 h-8 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center text-sm">4</span>
                        Ekuitas (Modal Awal)
                    </h3>
                    <div class="max-w-md">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Modal Awal Pemilik</label>
                        <div class="relative group">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 group-focus-within:text-purple-500 transition-colors">Rp</span>
                            <input type="number" wire:model.live="capital_amount" step="0.01"
                                class="w-full pl-12 rounded-lg border-gray-200 focus:ring-purple-500 focus:border-purple-500 transition-all font-bold text-xl py-3 border-2 border-purple-100 bg-purple-50/10">
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
                    <button type="button" onclick="history.back()" class="px-6 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-bold shadow-sm">
                        Batal
                    </button>
                    <button type="submit" 
                        {{ !$summary['is_balanced'] ? 'disabled' : '' }}
                        class="px-8 py-2.5 {{ $summary['is_balanced'] ? 'bg-blue-600 hover:bg-blue-700 shadow-blue-500/30' : 'bg-gray-400 cursor-not-allowed opacity-50' }} text-white rounded-lg transition font-bold shadow-lg flex items-center gap-2">
                        <svg class="w-5 h-5 font-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
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
