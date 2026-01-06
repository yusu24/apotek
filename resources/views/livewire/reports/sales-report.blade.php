<div class="p-6 space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white leading-tight">
                {{ __('Laporan Penjualan Detail') }}
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Daftar transaksi penjualan terperinci per periode.</p>
        </div>
    </div>

    <!-- Filters & Stats Card -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl px-6 py-4 shadow-sm border border-gray-100 dark:border-gray-700 flex flex-wrap items-center gap-3 no-print">
        <!-- Search Box -->
        <div class="w-48 flex-shrink-0">
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 pointer-events-none">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari Invois/Kasir..." class="block w-full pl-8 pr-3 py-2 text-[11px] border border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-1 focus:ring-blue-500 outline-none">
            </div>
        </div>

        <!-- Date Range -->
        <div class="flex items-center gap-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl px-2">
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest pl-1">Periode:</span>
            <input type="date" wire:model.live="startDate" class="bg-transparent border-none text-[11px] text-gray-900 dark:text-white focus:ring-0 p-1.5 w-32">
            <span class="text-gray-300">/</span>
            <input type="date" wire:model.live="endDate" class="bg-transparent border-none text-[11px] text-gray-900 dark:text-white focus:ring-0 p-1.5 w-32">
        </div>

        <!-- Payment Method -->
        <div class="flex items-center gap-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl px-2">
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest pl-1 text-nowrap">Metode:</span>
            <select wire:model.live="paymentMethod" class="bg-transparent border-none text-[11px] text-gray-900 dark:text-white focus:ring-0 p-1.5 w-24">
                <option value="all">Semua</option>
                <option value="cash">Tunai</option>
                <option value="qris">QRIS</option>
                <option value="transfer">Transfer</option>
            </select>
        </div>

        <!-- Stats (Total Sales) -->
        <div class="bg-emerald-50 dark:bg-emerald-900/20 px-4 py-2 rounded-xl border border-emerald-100 dark:border-emerald-800 flex items-baseline gap-2 min-w-[180px]">
            <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-tight">Total:</span>
            <span class="text-sm font-black text-emerald-700 dark:text-emerald-300">Rp {{ number_format($stats['total_sales'], 0, ',', '.') }}</span>
        </div>

        <!-- Print Button -->
        <div class="ml-auto">
            <button onclick="window.print()" class="flex items-center gap-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-all font-bold text-[11px] uppercase tracking-wider h-[38px]">
                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak
            </button>
        </div>
    </div>

    <!-- Table Section -->
    <div class="max-w-screen-2xl mx-auto space-y-6">

            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-3xl overflow-hidden border border-gray-100 dark:border-gray-700">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-900/50">
                                <th class="px-6 py-4 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">No. Invois</th>
                                <th class="px-6 py-4 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-4 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kasir</th>
                                <th class="px-6 py-4 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Metode</th>
                                <th class="px-6 py-4 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">Total</th>
                                <th class="px-6 py-4 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($sales as $sale)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">
                                        {{ $sale->invoice_no }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                        {{ \Carbon\Carbon::parse($sale->date)->format('d M Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400 font-medium">
                                        {{ $sale->user->name }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 rounded-full text-[10px] font-black uppercase bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                                            {{ $sale->payment_method }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right font-black text-gray-900 dark:text-white">
                                        Rp {{ number_format($sale->grand_total, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-right no-print">
                                        <a href="{{ route('pos.receipt', $sale->id) }}" wire:navigate class="text-blue-600 hover:text-blue-700 transition-colors">
                                            <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400 italic">
                                        Tidak ada data penjualan pada periode ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-100 dark:border-gray-700">
                    {{ $sales->links() }}
                </div>
            </div>
    </div>
</div>
</div>
