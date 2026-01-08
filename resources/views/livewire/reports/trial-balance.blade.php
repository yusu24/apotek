<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            Neraca Saldo (Trial Balance)
        </h2>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Filter Periode</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                <input type="date" wire:model="startDate" 
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
                <input type="date" wire:model="endDate" 
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div class="flex items-end">
                <button wire:click="generateReport" class="btn btn-lg btn-primary">
                    Generate
                </button>
            </div>
        </div>

        <div class="flex gap-2">
            <button wire:click="setThisMonth" class="btn btn-secondary">
                Bulan Ini
            </button>
            <button wire:click="setLastMonth" class="btn btn-secondary">
                Bulan Lalu
            </button>
            <button wire:click="setThisYear" class="btn btn-secondary">
                Tahun Ini
            </button>
        </div>
    </div>

    <!-- Validation Alert -->
    @if(isset($reportData['is_balanced']))
    <div class="mb-6">
        @if($reportData['is_balanced'])
            <div class="bg-green-50 border-l-4 border-green-400 p-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <p class="text-green-700 font-bold">Neraca Balance ✓ (Debit = Kredit)</p>
                </div>
            </div>
        @else
            <div class="bg-red-50 border-l-4 border-red-400 p-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <p class="text-red-700 font-bold">Neraca Tidak Balance!</p>
                        <p class="text-red-600 text-sm">Selisih: Rp {{ number_format(abs($reportData['difference']), 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
    @endif

    <!-- Trial Balance Report -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6 border-b bg-blue-50">
            <h3 class="text-lg font-bold text-gray-900">
                Neraca Saldo
            </h3>
            <p class="text-sm text-gray-600 mt-1">
                Periode: {{ \Carbon\Carbon::parse($reportData['start_date'] ?? now())->format('d M Y') }} - 
                {{ \Carbon\Carbon::parse($reportData['end_date'] ?? now())->format('d M Y') }}
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Akun</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Debit</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Kredit</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    
                    <!-- ASET -->
                    @if(isset($reportData['assets']) && $reportData['assets']->count() > 0)
                    <tr class="bg-blue-100">
                        <td colspan="4" class="px-6 py-3 text-sm font-bold text-gray-900 uppercase">ASET</td>
                    </tr>
                    @foreach($reportData['assets'] as $account)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $account->code }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $account->name }}</td>
                        <td class="px-6 py-4 text-sm text-right font-medium text-gray-900">
                            {{ $account->total_debit > 0 ? 'Rp ' . number_format($account->total_debit, 0, ',', '.') : '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-right font-medium text-gray-900">
                            {{ $account->total_credit > 0 ? 'Rp ' . number_format($account->total_credit, 0, ',', '.') : '-' }}
                        </td>
                    </tr>
                    @endforeach
                    <tr class="bg-blue-50 font-bold">
                        <td colspan="2" class="px-6 py-3 text-sm text-gray-900">Subtotal Aset</td>
                        <td class="px-6 py-3 text-sm text-right text-blue-600">
                            Rp {{ number_format($reportData['total_assets_debit'] ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-3 text-sm text-right text-blue-600">
                            Rp {{ number_format($reportData['total_assets_credit'] ?? 0, 0, ',', '.') }}
                        </td>
                    </tr>
                    @endif

                    <!-- KEWAJIBAN -->
                    @if(isset($reportData['liabilities']) && $reportData['liabilities']->count() > 0)
                    <tr class="bg-yellow-100">
                        <td colspan="4" class="px-6 py-3 text-sm font-bold text-gray-900 uppercase">KEWAJIBAN</td>
                    </tr>
                    @foreach($reportData['liabilities'] as $account)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $account->code }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $account->name }}</td>
                        <td class="px-6 py-4 text-sm text-right font-medium text-gray-900">
                            {{ $account->total_debit > 0 ? 'Rp ' . number_format($account->total_debit, 0, ',', '.') : '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-right font-medium text-gray-900">
                            {{ $account->total_credit > 0 ? 'Rp ' . number_format($account->total_credit, 0, ',', '.') : '-' }}
                        </td>
                    </tr>
                    @endforeach
                    <tr class="bg-yellow-50 font-bold">
                        <td colspan="2" class="px-6 py-3 text-sm text-gray-900">Subtotal Kewajiban</td>
                        <td class="px-6 py-3 text-sm text-right text-yellow-600">
                            Rp {{ number_format($reportData['total_liabilities_debit'] ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-3 text-sm text-right text-yellow-600">
                            Rp {{ number_format($reportData['total_liabilities_credit'] ?? 0, 0, ',', '.') }}
                        </td>
                    </tr>
                    @endif

                    <!-- EKUITAS -->
                    @if(isset($reportData['equity']) && $reportData['equity']->count() > 0)
                    <tr class="bg-purple-100">
                        <td colspan="4" class="px-6 py-3 text-sm font-bold text-gray-900 uppercase">EKUITAS</td>
                    </tr>
                    @foreach($reportData['equity'] as $account)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $account->code }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $account->name }}</td>
                        <td class="px-6 py-4 text-sm text-right font-medium text-gray-900">
                            {{ $account->total_debit > 0 ? 'Rp ' . number_format($account->total_debit, 0, ',', '.') : '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-right font-medium text-gray-900">
                            {{ $account->total_credit > 0 ? 'Rp ' . number_format($account->total_credit, 0, ',', '.') : '-' }}
                        </td>
                    </tr>
                    @endforeach
                    <tr class="bg-purple-50 font-bold">
                        <td colspan="2" class="px-6 py-3 text-sm text-gray-900">Subtotal Ekuitas</td>
                        <td class="px-6 py-3 text-sm text-right text-purple-600">
                            Rp {{ number_format($reportData['total_equity_debit'] ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-3 text-sm text-right text-purple-600">
                            Rp {{ number_format($reportData['total_equity_credit'] ?? 0, 0, ',', '.') }}
                        </td>
                    </tr>
                    @endif

                    <!-- PENDAPATAN -->
                    @if(isset($reportData['revenue']) && $reportData['revenue']->count() > 0)
                    <tr class="bg-green-100">
                        <td colspan="4" class="px-6 py-3 text-sm font-bold text-gray-900 uppercase">PENDAPATAN</td>
                    </tr>
                    @foreach($reportData['revenue'] as $account)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $account->code }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $account->name }}</td>
                        <td class="px-6 py-4 text-sm text-right font-medium text-gray-900">
                            {{ $account->total_debit > 0 ? 'Rp ' . number_format($account->total_debit, 0, ',', '.') : '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-right font-medium text-gray-900">
                            {{ $account->total_credit > 0 ? 'Rp ' . number_format($account->total_credit, 0, ',', '.') : '-' }}
                        </td>
                    </tr>
                    @endforeach
                    <tr class="bg-green-50 font-bold">
                        <td colspan="2" class="px-6 py-3 text-sm text-gray-900">Subtotal Pendapatan</td>
                        <td class="px-6 py-3 text-sm text-right text-green-600">
                            Rp {{ number_format($reportData['total_revenue_debit'] ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-3 text-sm text-right text-green-600">
                            Rp {{ number_format($reportData['total_revenue_credit'] ?? 0, 0, ',', '.') }}
                        </td>
                    </tr>
                    @endif

                    <!-- BEBAN -->
                    @if(isset($reportData['expenses']) && $reportData['expenses']->count() > 0)
                    <tr class="bg-red-100">
                        <td colspan="4" class="px-6 py-3 text-sm font-bold text-gray-900 uppercase">BEBAN</td>
                    </tr>
                    @foreach($reportData['expenses'] as $account)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $account->code }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $account->name }}</td>
                        <td class="px-6 py-4 text-sm text-right font-medium text-gray-900">
                            {{ $account->total_debit > 0 ? 'Rp ' . number_format($account->total_debit, 0, ',', '.') : '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-right font-medium text-gray-900">
                            {{ $account->total_credit > 0 ? 'Rp ' . number_format($account->total_credit, 0, ',', '.') : '-' }}
                        </td>
                    </tr>
                    @endforeach
                    <tr class="bg-red-50 font-bold">
                        <td colspan="2" class="px-6 py-3 text-sm text-gray-900">Subtotal Beban</td>
                        <td class="px-6 py-3 text-sm text-right text-red-600">
                            Rp {{ number_format($reportData['total_expenses_debit'] ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-3 text-sm text-right text-red-600">
                            Rp {{ number_format($reportData['total_expenses_credit'] ?? 0, 0, ',', '.') }}
                        </td>
                    </tr>
                    @endif

                    <!-- GRAND TOTAL -->
                    <tr class="bg-gray-800 text-white font-bold">
                        <td colspan="2" class="px-6 py-4 text-sm uppercase">TOTAL</td>
                        <td class="px-6 py-4 text-sm text-right">
                            Rp {{ number_format($reportData['grand_total_debit'] ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-right">
                            Rp {{ number_format($reportData['grand_total_credit'] ?? 0, 0, ',', '.') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        @if(!isset($reportData['accounts']) || $reportData['accounts']->count() == 0)
        <div class="p-6 text-center text-gray-500">
            Tidak ada transaksi jurnal pada periode ini.
        </div>
        @endif
    </div>
</div>
