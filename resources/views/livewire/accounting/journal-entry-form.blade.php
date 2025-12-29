<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            Input Jurnal Umum
        </h2>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <form wire:submit.prevent="save">
                <!-- Header Fields -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 bg-gray-50 p-4 rounded-lg border border-gray-100">
                    <div>
                        <label for="date" class="block text-sm font-bold text-gray-700 mb-1">Tanggal Transaksi</label>
                        <input type="date" wire:model="date" id="date" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        @error('date') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label for="description" class="block text-sm font-bold text-gray-700 mb-1">Keterangan / Deskripsi</label>
                        <input type="text" wire:model="description" id="description" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="Contoh: Setoran Modal Awal">
                        @error('description') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Error Messages -->
                @error('balance')
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                        <p class="font-bold">{{ $message }}</p>
                    </div>
                @enderror

                @error('system')
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                        <p class="font-bold">{{ $message }}</p>
                    </div>
                @enderror

                <!-- Journal Lines Table -->
                <div class="border rounded-lg overflow-hidden mb-6 shadow-sm">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-800 text-white">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider w-1/3">Akun</th>
                                <th class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wider w-1/6">Debit (Rp)</th>
                                <th class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wider w-1/6">Kredit (Rp)</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider">Catatan Detail</th>
                                <th class="px-4 py-3 w-10"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($lines as $index => $line)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="px-4 py-2">
                                    <select wire:model="lines.{{ $index }}.account_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                        <option value="">-- Pilih Akun --</option>
                                        @foreach($accounts as $account)
                                            <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
                                        @endforeach
                                    </select>
                                    @error("lines.{$index}.account_id") <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </td>
                                <td class="px-4 py-2">
                                    <input type="number" wire:model.live.debounce.300ms="lines.{{ $index }}.debit" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm text-right font-mono" min="0">
                                </td>
                                <td class="px-4 py-2">
                                    <input type="number" wire:model.live.debounce.300ms="lines.{{ $index }}.credit" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm text-right font-mono" min="0">
                                </td>
                                <td class="px-4 py-2">
                                    <input type="text" wire:model="lines.{{ $index }}.notes" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="Opsional">
                                </td>
                                <td class="px-4 py-2 text-center">
                                    <button type="button" wire:click="removeLine({{ $index }})" class="text-red-400 hover:text-red-700 transition duration-150" title="Hapus Baris">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-100 font-bold border-t-2 border-gray-300">
                            <tr>
                                <td class="px-4 py-3 text-right uppercase text-xs tracking-wider text-gray-600">Total</td>
                                <td class="px-4 py-3 text-right text-blue-800 font-mono">{{ number_format($totalDebit, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right text-blue-800 font-mono">{{ number_format($totalCredit, 0, ',', '.') }}</td>
                                <td colspan="2" class="px-4 py-3 text-center">
                                    @if(abs($difference) < 0.01)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                            SEIMBANG
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800">
                                            SELISIH: {{ number_format(abs($difference), 0, ',', '.') }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <div class="flex justify-between items-center bg-gray-50 p-4 rounded-lg border border-gray-100">
                    <button type="button" wire:click="addLine" class="inline-flex items-center px-4 py-2 border border-blue-300 shadow-sm text-sm font-bold rounded-lg text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none transition duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        Tambah Baris Akun
                    </button>
                    
                    <div class="flex space-x-3">
                        <a href="{{ route('accounting.journals.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-bold rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none transition duration-150">
                            Batal
                        </a>
                        <button type="submit" class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-bold rounded-lg shadow-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 transform hover:scale-105">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                            Simpan & Posting Jurnal
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
