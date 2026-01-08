<div class="p-6">
    <div class="flex justify-between items-center mb-6 text-gray-800">
        <h2 class="text-2xl font-bold">
            Pengaturan Kasir
        </h2>
    </div>

    @if($success_message)
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 flex justify-between items-center rounded-r-lg shadow-sm">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span class="font-bold">{{ $success_message }}</span>
            </div>
            <button @click="show = false" class="text-green-500 hover:text-green-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    @endif

    <div class="space-y-6">
        <div class="grid md:grid-cols-2 gap-6">
            <!-- Paper Size Setting -->
            <div class="bg-white p-6 rounded-lg shadow-md border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 mb-6 border-b pb-2 flex items-center gap-2 uppercase tracking-tight text-xs">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Ukuran Kertas Nota
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- 58mm -->
                    <label class="relative cursor-pointer group">
                        <input type="radio" wire:model.live="pos_paper_size" value="58mm" class="sr-only">
                        <div class="h-full p-4 rounded-xl flex flex-col items-center justify-center transition-all border-2 {{ $pos_paper_size === '58mm' ? 'border-blue-600 bg-blue-50' : 'border-gray-100 bg-gray-50 group-hover:bg-gray-100' }} relative overflow-hidden">
                            @if($pos_paper_size === '58mm')
                            <div class="absolute top-0 right-0 bg-blue-600 text-white text-[8px] font-bold px-2 py-1 rounded-bl-lg shadow-sm">
                                AKTIF
                            </div>
                            @endif
                            
                            <div class="w-8 h-12 border-2 border-gray-300 rounded mb-2 bg-white flex flex-col p-1 gap-1">
                                <div class="h-1 bg-gray-200 w-full"></div>
                                <div class="h-1 bg-gray-200 w-3/4"></div>
                                <div class="h-1 bg-gray-200 w-full"></div>
                            </div>
                            <span class="text-sm font-bold text-gray-700">58mm</span>
                            <span class="text-[10px] text-gray-400 uppercase">Kecil</span>
                        </div>
                    </label>

                    <!-- 80mm -->
                    <label class="relative cursor-pointer group">
                        <input type="radio" wire:model.live="pos_paper_size" value="80mm" class="sr-only">
                        <div class="h-full p-4 rounded-xl flex flex-col items-center justify-center transition-all border-2 {{ $pos_paper_size === '80mm' ? 'border-blue-600 bg-blue-50' : 'border-gray-100 bg-gray-50 group-hover:bg-gray-100' }} relative overflow-hidden">
                             @if($pos_paper_size === '80mm')
                             <div class="absolute top-0 right-0 bg-blue-600 text-white text-[8px] font-bold px-2 py-1 rounded-bl-lg shadow-sm">
                                 AKTIF
                             </div>
                             @endif

                            <div class="w-10 h-14 border-2 border-gray-300 rounded mb-2 bg-white flex flex-col p-1.5 gap-1">
                                <div class="h-1 bg-gray-200 w-full"></div>
                                <div class="h-1 bg-gray-200 w-3/4"></div>
                                <div class="h-1 bg-gray-200 w-full"></div>
                                <div class="h-1 bg-gray-200 w-1/2"></div>
                            </div>
                            <span class="text-sm font-bold text-gray-700">80mm</span>
                            <span class="text-[10px] text-gray-400 uppercase">Standar</span>
                        </div>
                    </label>

                    <!-- A4 -->
                    <label class="relative cursor-pointer group">
                        <input type="radio" wire:model.live="pos_paper_size" value="A4" class="sr-only">
                        <div class="h-full p-4 rounded-xl flex flex-col items-center justify-center transition-all border-2 {{ $pos_paper_size === 'A4' ? 'border-blue-600 bg-blue-50' : 'border-gray-100 bg-gray-50 group-hover:bg-gray-100' }} relative overflow-hidden">
                            @if($pos_paper_size === 'A4')
                            <div class="absolute top-0 right-0 bg-blue-600 text-white text-[8px] font-bold px-2 py-1 rounded-bl-lg shadow-sm">
                                AKTIF
                            </div>
                            @endif

                            <div class="w-12 h-16 border-2 border-gray-300 rounded mb-2 bg-white flex flex-col p-2 gap-1.5">
                                <div class="h-1.5 bg-gray-200 w-full"></div>
                                <div class="h-1.5 bg-gray-200 w-3/4"></div>
                                <div class="h-1.5 bg-gray-200 w-full"></div>
                            </div>
                            <span class="text-sm font-bold text-gray-700">A4</span>
                            <span class="text-[10px] text-gray-400 uppercase">Dokumen</span>
                        </div>
                    </label>
                </div>
                
                <p class="mt-6 text-[10px] text-gray-400 leading-relaxed italic border-t border-gray-100 pt-4">
                    * Pilih ukuran yang sesuai dengan printer thermal (58mm/80mm) atau printer kantor (A4) Anda.
                </p>
            </div>

            <!-- Tax / PPN Settings -->
            <div class="bg-white p-6 rounded-lg shadow-md border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 mb-6 border-b pb-2 flex items-center gap-2 uppercase tracking-tight text-xs">
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Pajak & PPN
                </h3>

                <div class="space-y-4 mb-6">
                    <!-- PPN Rate Input -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2 uppercase tracking-wider">Persentase PPN (%)</label>
                        <div class="flex items-center gap-3">
                            <input type="number" step="0.1" wire:model="pos_ppn_rate" class="block w-24 border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm font-bold text-center">
                            <span class="text-gray-600 font-bold">%</span>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-4">
                        <label class="block text-xs font-bold text-gray-700 mb-3 uppercase tracking-wider">Mode Perhitungan</label>
                        <div class="space-y-3">
                            <label class="flex items-start gap-3 p-3 rounded-lg border transition-all cursor-pointer hover:bg-gray-50 {{ $pos_ppn_mode === 'off' ? 'border-amber-400 bg-amber-50' : 'border-gray-100' }}">
                                <input type="radio" wire:model.live="pos_ppn_mode" value="off" class="mt-1 text-amber-600 focus:ring-amber-500">
                                <div>
                                    <span class="block text-sm font-bold text-gray-800">PPN Off</span>
                                    <span class="text-[10px] text-gray-500">Tanpa kalkulasi pajak.</span>
                                </div>
                            </label>

                            <label class="flex items-start gap-3 p-3 rounded-lg border transition-all cursor-pointer hover:bg-gray-50 {{ $pos_ppn_mode === 'inclusive' ? 'border-amber-400 bg-amber-50' : 'border-gray-100' }}">
                                <input type="radio" wire:model.live="pos_ppn_mode" value="inclusive" class="mt-1 text-amber-600 focus:ring-amber-500">
                                <div>
                                    <span class="block text-sm font-bold text-gray-800">PPN Inclusive</span>
                                    <span class="text-[10px] text-gray-500">Pajak sudah termasuk harga.</span>
                                </div>
                            </label>

                            <label class="flex items-start gap-3 p-3 rounded-lg border transition-all cursor-pointer hover:bg-gray-50 {{ $pos_ppn_mode === 'exclusive' ? 'border-amber-400 bg-amber-50' : 'border-gray-100' }}">
                                <input type="radio" wire:model.live="pos_ppn_mode" value="exclusive" class="mt-1 text-amber-600 focus:ring-amber-500">
                                <div>
                                    <span class="block text-sm font-bold text-gray-800">PPN Exclusive</span>
                                    <span class="text-[10px] text-gray-500">Pajak luar harga jual.</span>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Actions with Save Button Small Right -->
        <div class="mt-8 flex justify-end gap-3 border-t pt-6">
            <x-button wire:click="save" variant="primary" class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Simpan
            </x-button>
        </div>
    </div>
</div>
