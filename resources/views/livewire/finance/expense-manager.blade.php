<div class="p-6">
    <div class="flex justify-between items-center mb-6">
         <h2 class="text-2xl font-bold text-gray-800">
            Kelola Pengeluaran & Pemasukan
         </h2>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
            <!-- Left side: Search Box -->
            <div class="relative w-full sm:max-w-[200px]">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input wire:model.live.debounce.300ms="search" 
                    type="text" placeholder="Cari deskripsi..." 
                    class="w-full pl-10 pr-4 rounded-lg border-gray-300 text-sm py-2 focus:ring-2 focus:ring-blue-500 transition-all bg-white shadow-sm">
            </div>

            <!-- Right side: Actions & Exports -->
            <div class="flex items-center gap-3 flex-wrap w-full md:w-auto justify-end">
                <button wire:click="create" class="btn btn-primary flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span>Tambah Transaksi</span>
                </button>

                <!-- Export Buttons -->
                <div class="relative btn-export-dropdown" x-data="{ open: false }" @click.outside="open = false">
                    <button @click="open = !open" class="btn btn-export-excel" title="Export">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span class="hidden sm:inline ml-1">Export</span>
                        <svg class="w-3 h-3 ml-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="dropdown-menu" x-show="open" x-cloak style="display:none">
                        <button wire:click="exportExcel" @click="open = false" class="dropdown-item">
                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20" class="text-green-600">
                                <path d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"/>
                            </svg>
                            Excel (.xlsx)
                        </button>
                        <button wire:click="exportPdf" @click="open = false" class="dropdown-item">
                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20" class="text-red-600">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                            </svg>
                            PDF (.pdf)
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Period Filter Row -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 mb-6 p-3 bg-gray-50 rounded-lg border border-gray-200">
            <div class="flex items-center gap-2 shrink-0">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span class="text-sm font-semibold text-gray-600">Periode:</span>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                @php
                    $periods = [
                        'all' => 'Semua',
                        'today' => 'Hari Ini',
                        'this_week' => 'Minggu Ini',
                        'this_month' => 'Bulan Ini',
                        'custom' => 'Custom',
                    ];
                @endphp
                @foreach($periods as $key => $label)
                    <button wire:click="$set('filterPeriod', '{{ $key }}')"
                        class="px-3 py-1.5 rounded-full text-xs font-semibold transition-all duration-200
                            {{ $filterPeriod === $key 
                                ? 'bg-blue-600 text-white shadow-md ring-2 ring-blue-300' 
                                : 'bg-white text-gray-600 border border-gray-300 hover:bg-gray-100 hover:border-gray-400' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            {{-- Custom date range inputs --}}
            @if($filterPeriod === 'custom')
                <div class="flex items-center gap-2 ml-0 sm:ml-2 mt-2 sm:mt-0" x-data>
                    <div wire:ignore>
                        <input 
                            x-data="{
                                value: @entangle('filterDateFrom'),
                                instance: undefined,
                                init() {
                                    this.instance = flatpickr(this.$refs.filterFromInput, {
                                        dateFormat: 'Y-m-d',
                                        defaultDate: this.value || null,
                                        onChange: (selectedDates, dateStr) => {
                                            this.value = dateStr;
                                        }
                                    });
                                    this.$watch('value', val => {
                                        if (val && this.instance) this.instance.setDate(val);
                                        else if (this.instance) this.instance.clear();
                                    });
                                }
                            }"
                            x-ref="filterFromInput"
                            type="text"
                            placeholder="Dari tanggal"
                            class="w-32 rounded-lg border-gray-300 text-sm py-1.5 px-3 focus:ring-2 focus:ring-blue-500 bg-white shadow-sm"
                        />
                    </div>
                    <span class="text-gray-400 text-sm">—</span>
                    <div wire:ignore>
                        <input 
                            x-data="{
                                value: @entangle('filterDateTo'),
                                instance: undefined,
                                init() {
                                    this.instance = flatpickr(this.$refs.filterToInput, {
                                        dateFormat: 'Y-m-d',
                                        defaultDate: this.value || null,
                                        onChange: (selectedDates, dateStr) => {
                                            this.value = dateStr;
                                        }
                                    });
                                    this.$watch('value', val => {
                                        if (val && this.instance) this.instance.setDate(val);
                                        else if (this.instance) this.instance.clear();
                                    });
                                }
                            }"
                            x-ref="filterToInput"
                            type="text"
                            placeholder="Sampai tanggal"
                            class="w-32 rounded-lg border-gray-300 text-sm py-1.5 px-3 focus:ring-2 focus:ring-blue-500 bg-white shadow-sm"
                        />
                    </div>
                </div>
            @endif
        </div>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-6">
            {{-- Card: Total Pengeluaran --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-100 dark:border-gray-700 shadow-sm p-5 hover:shadow-md transition-all duration-200 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Pengeluaran</span>
                        <div class="w-8 h-8 rounded-full bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
                        </div>
                    </div>
                    <div class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">
                        Rp {{ number_format($totalExpense, 0, ',', '.') }}
                    </div>
                    <div class="mt-1 text-xs text-gray-400 dark:text-gray-500">
                        Arus kas keluar operasional
                    </div>
                </div>
                <div class="w-full h-1 bg-gray-100 dark:bg-gray-700 rounded-full mt-4 overflow-hidden">
                    <div class="h-full bg-rose-500 rounded-full" style="width: 100%;"></div>
                </div>
            </div>

            {{-- Card: Total Pemasukan --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-100 dark:border-gray-700 shadow-sm p-5 hover:shadow-md transition-all duration-200 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Pemasukan</span>
                        <div class="w-8 h-8 rounded-full bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                        </div>
                    </div>
                    <div class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">
                        Rp {{ number_format($totalIncome, 0, ',', '.') }}
                    </div>
                    <div class="mt-1 text-xs text-gray-400 dark:text-gray-500">
                        Arus kas masuk non-penjualan
                    </div>
                </div>
                <div class="w-full h-1 bg-gray-100 dark:bg-gray-700 rounded-full mt-4 overflow-hidden">
                    <div class="h-full bg-emerald-500 rounded-full" style="width: 100%;"></div>
                </div>
            </div>

            {{-- Card: Net --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-100 dark:border-gray-700 shadow-sm p-5 hover:shadow-md transition-all duration-200 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Net {{ $totalNet < 0 ? '(Defisit)' : '(Surplus)' }}</span>
                        <div class="w-8 h-8 rounded-full {{ $totalNet >= 0 ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' : 'bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400' }} flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                    </div>
                    <div class="text-2xl lg:text-3xl font-bold {{ $totalNet < 0 ? 'text-amber-600 dark:text-amber-400' : 'text-gray-900 dark:text-white' }} tracking-tight">
                        {{ $totalNet < 0 ? '-' : '' }}Rp {{ number_format(abs($totalNet), 0, ',', '.') }}
                    </div>
                    <div class="mt-1 text-xs text-gray-400 dark:text-gray-500">
                        Selisih kas operasional
                    </div>
                </div>
                <div class="w-full h-1 bg-gray-100 dark:bg-gray-700 rounded-full mt-4 overflow-hidden">
                    <div class="h-full {{ $totalNet >= 0 ? 'bg-blue-600' : 'bg-amber-500' }} rounded-full" style="width: 100%;"></div>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto rounded-lg">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-blue-950 text-white font-semibold uppercase text-xs tracking-wider border-b border-blue-900">
                    <tr>
                        <th wire:click="sortByColumn('date')" class="px-4 py-3 text-left cursor-pointer hover:bg-blue-900 transition-colors whitespace-nowrap">
                            <div class="flex items-center gap-1">
                                Tanggal
                                @if($sortBy === 'date')
                                    <svg class="w-3 h-3 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <th wire:click="sortByColumn('description')" class="px-4 py-3 text-left cursor-pointer hover:bg-blue-900 transition-colors whitespace-nowrap">
                            <div class="flex items-center gap-1">
                                Keterangan
                                @if($sortBy === 'description')
                                    <svg class="w-3 h-3 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <th class="px-4 py-3 text-left whitespace-nowrap">Tipe</th>
                        <th wire:click="sortByColumn('category')" class="px-4 py-3 text-left cursor-pointer hover:bg-blue-900 transition-colors whitespace-nowrap">
                            <div class="flex items-center gap-1">
                                Kategori
                                @if($sortBy === 'category')
                                    <svg class="w-3 h-3 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <th class="px-4 py-3 text-left whitespace-nowrap">Sumber Dana</th>
                        <th wire:click="sortByColumn('amount')" class="px-4 py-3 text-right cursor-pointer hover:bg-blue-900 transition-colors whitespace-nowrap">
                            <div class="flex items-center justify-end gap-1">
                                Jumlah (Rp)
                                @if($sortBy === 'amount')
                                    <svg class="w-3 h-3 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <th class="px-4 py-3 text-left whitespace-nowrap">User</th>
                        <th class="px-4 py-3 text-right whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($expenses as $expense)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ \Carbon\Carbon::parse($expense->date)->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $expense->description }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm">
                                @if($expense->type === 'income')
                                    <span class="px-2.5 py-0.5 bg-green-50 text-green-700 border border-green-200 rounded-md text-xs inline-flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                        Pemasukan
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 bg-red-50 text-red-700 border border-red-200 rounded-md text-xs inline-flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                        Pengeluaran
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $expense->category ?? '-' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                @if($expense->account)
                                    <span class="px-2 py-0.5 bg-gray-100 text-gray-700 rounded text-xs border border-gray-200">
                                        {{ $expense->account->name }}
                                    </span>
                                @else
                                    <span class="text-gray-400 italic">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($expense->amount, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $expense->user->name ?? 'System' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-right text-sm">
                                <div class="flex items-center justify-end gap-2">
                                    <button wire:click="edit({{ $expense->id }})" 
                                        class="text-blue-600 hover:text-blue-900 transition-colors" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <button wire:click="delete({{ $expense->id }})" wire:confirm="Yakin ingin menghapus data ini?" 
                                        class="text-red-600 hover:text-red-900 transition-colors" title="Hapus">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <x-empty-table colspan="8" />
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-50 border-t-2 border-gray-300">
                    <tr>
                        <td colspan="5" class="px-6 py-3 text-sm font-bold text-gray-700 text-right">Total ({{ $expenses->total() }} data):</td>
                        <td class="px-6 py-3 text-right">
                            <div class="flex flex-col items-end gap-0.5">
                                <span class="text-sm font-bold text-red-700">- Rp {{ number_format($totalExpense, 0, ',', '.') }}</span>
                                @if($totalIncome > 0)
                                    <span class="text-sm font-bold text-green-700">+ Rp {{ number_format($totalIncome, 0, ',', '.') }}</span>
                                @endif
                            </div>
                        </td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">
            @include('components.custom-pagination', ['items' => $expenses])
        </div>
    </div>

    <!-- Modal Form -->
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    {{ $isEditing ? ($type === 'income' ? 'Edit Pemasukan Lain-lain' : 'Edit Pengeluaran') : ($type === 'income' ? 'Tambah Pemasukan Lain-lain' : 'Tambah Pengeluaran') }}
                                </h3>
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Tanggal</label>
                                        <div wire:ignore class="w-full relative mt-1">
                                            <input 
                                                x-data="{
                                                    value: @entangle('date'),
                                                    instance: undefined,
                                                    init() {
                                                        this.instance = flatpickr(this.$refs.expenseDateInput, {
                                                            dateFormat: 'Y-m-d',
                                                            defaultDate: this.value,
                                                            maxDate: '{{ date('Y-m-d') }}',
                                                            onChange: (selectedDates, dateStr) => {
                                                                this.value = dateStr;
                                                            }
                                                        });
                                                        this.$watch('value', value => {
                                                            if (this.instance.selectedDates[0] !== undefined) {
                                                                let current = this.instance.formatDate(this.instance.selectedDates[0], 'Y-m-d');
                                                                if (current !== value) {
                                                                    this.instance.setDate(value);
                                                                }
                                                            } else if(value) {
                                                                this.instance.setDate(value);
                                                            } else {
                                                                this.instance.clear();
                                                            }
                                                        });
                                                    }
                                                }"
                                                x-ref="expenseDateInput"
                                                type="text"
                                                placeholder="Pilih tanggal..."
                                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm block"
                                            />
                                        </div>
                                        @error('date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Transaksi</label>
                                        <div class="grid grid-cols-2 gap-3">
                                            <label class="flex items-center justify-center gap-2 border-2 rounded-xl py-2.5 px-3 cursor-pointer text-sm transition-all {{ $type === 'expense' ? 'border-red-500 bg-red-50 text-red-700 font-bold shadow-sm' : 'border-gray-200 text-gray-500 hover:bg-gray-50' }}">
                                                <input type="radio" wire:model.live="type" value="expense" class="hidden">
                                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
                                                <span>Pengeluaran</span>
                                            </label>
                                            <label class="flex items-center justify-center gap-2 border-2 rounded-xl py-2.5 px-3 cursor-pointer text-sm transition-all {{ $type === 'income' ? 'border-green-500 bg-green-50 text-green-700 font-bold shadow-sm' : 'border-gray-200 text-gray-500 hover:bg-gray-50' }}">
                                                <input type="radio" wire:model.live="type" value="income" class="hidden">
                                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                                                <span>Pemasukan Lain-lain</span>
                                            </label>
                                        </div>
                                        @error('type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Keterangan</label>
                                        <input type="text" wire:model="description" placeholder="Contoh: Bayar Listrik" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Jumlah (Rp)</label>
                                        <div x-data="money($wire.entangle('amount'))">
                                            <input type="text" x-bind="input" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="0">
                                        </div>
                                        @error('amount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Kategori <span class="text-red-500">*</span></label>
                                        <select wire:model="category" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                            <option value="">Pilih Kategori</option>
                                            @foreach($categories as $cat)
                                                <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('category') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Sumber Dana (Pilih untuk Jurnal Otomatis)</label>
                                        <select wire:model="accountId" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                            <option value="">-- Tetapkan Nanti --</option>
                                            @foreach($accounts as $acc)
                                                <option value="{{ $acc->id }}">[{{ $acc->code }}] {{ $acc->name }} (Saldo: Rp. {{ number_format($acc->balance, 0, ',', '.') }},-)</option>
                                            @endforeach
                                        </select>
                                        <p class="mt-1 text-[10px] text-gray-500">Jika dipilih, transaksi akan otomatis tercatat di Jurnal Akuntansi.</p>
                                        @error('accountId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button wire:click="save" type="button" class="btn btn-primary w-full sm:w-auto sm:ml-3">
                            Simpan
                        </button>
                        <button wire:click="$set('showModal', false)" type="button" class="btn btn-secondary w-full sm:w-auto mt-3 sm:mt-0">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Category Management Modal -->

    @script
    <script>
        $wire.on('open-pdf', (event) => {
            window.open(event.url, '_blank');
        });
    </script>
    @endscript
</div>
