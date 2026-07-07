@php
    $navy = '#1e40af';
    $subtotalBg = '#dbeafe';
    $totalBg = '#f3f4f6';
    $headerBg = '#f3f4f6';
    $black = '#000000';
    $white = '#ffffff';
    $colspan = $showComparison ? 3 : 2;

    $fmt = fn($v) => 'Rp ' . number_format((float) $v, 0, ',', '.');
    $fmtSigned = fn($v) => $v < 0 ? '(' . $fmt(abs($v)) . ')' : $fmt($v);

    $revCatCurrent = collect($current['revenueByCategory'])->keyBy('category_name');
    $revCatPrevious = $showComparison ? collect($previous['revenueByCategory'])->keyBy('category_name') : collect();
    $revCategories = $revCatCurrent->keys()->merge($revCatPrevious->keys())->unique()->sort()->values();

    $opCatCurrent = $current['expenseDetails']->groupBy('category')->map(fn($items) => $items->sum('amount'));
    $opCatPrevious = ($showComparison ? $previous['expenseDetails'] : collect())->groupBy('category')->map(fn($items) => $items->sum('amount'));
    $opCategories = $opCatCurrent->keys()->merge($opCatPrevious->keys())->unique()->sort()->values();

    $taxCatCurrent = $current['taxDetails']->groupBy('category')->map(fn($items) => $items->sum('amount'));
    $taxCatPrevious = ($showComparison ? $previous['taxDetails'] : collect())->groupBy('category')->map(fn($items) => $items->sum('amount'));
    $taxCategories = $taxCatCurrent->keys()->merge($taxCatPrevious->keys())->unique()->sort()->values();

    $hasOtherIncomeExpense = $current['otherIncomeAmount'] > 0 || $current['otherExpenseAmount'] > 0
        || ($showComparison && ($previous['otherIncomeAmount'] > 0 || $previous['otherExpenseAmount'] > 0));
@endphp
<table>
    <thead>
        <tr>
            <th colspan="{{ $colspan }}" style="font-weight: bold; font-size: 16px; text-align: center; color: {{ $black }};">{{ $storeName }}</th>
        </tr>
        <tr>
            <th colspan="{{ $colspan }}" style="font-weight: bold; font-size: 13px; text-align: center; color: {{ $black }};">LAPORAN LABA RUGI</th>
        </tr>
        <tr>
            <th colspan="{{ $colspan }}" style="font-style: italic; font-size: 10px; text-align: center; color: {{ $black }};">Untuk Periode {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</th>
        </tr>
        <tr><th colspan="{{ $colspan }}"></th></tr>
        <tr>
            <th style="text-align: left; background-color: {{ $headerBg }}; color: {{ $black }}; font-weight: bold;">Keterangan</th>
            <th style="text-align: center; background-color: {{ $headerBg }}; color: {{ $black }}; font-weight: bold;">{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</th>
            @if($showComparison)
            <th style="text-align: center; background-color: {{ $headerBg }}; color: {{ $black }}; font-weight: bold;">{{ \Carbon\Carbon::parse($prevStartDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($prevEndDate)->format('d/m/Y') }}</th>
            @endif
        </tr>
    </thead>
    <tbody>
        {{-- PENDAPATAN --}}
        <tr>
            <td colspan="{{ $colspan }}" style="font-weight: bold; background-color: {{ $navy }}; color: {{ $white }};">PENDAPATAN</td>
        </tr>
        @foreach($revCategories as $catName)
        <tr>
            <td style="color: {{ $black }};">&nbsp;&nbsp;Penjualan {{ $catName }}</td>
            <td style="text-align: right; color: {{ $black }};">{{ $fmt($revCatCurrent[$catName]->total ?? 0) }}</td>
            @if($showComparison)<td style="text-align: right; color: {{ $black }};">{{ $fmt($revCatPrevious[$catName]->total ?? 0) }}</td>@endif
        </tr>
        @endforeach
        <tr>
            <td style="font-weight: bold; background-color: {{ $totalBg }}; color: {{ $black }};">Total Pendapatan (Omzet)</td>
            <td style="font-weight: bold; text-align: right; background-color: {{ $totalBg }}; color: {{ $black }};">{{ $fmt($current['revenue']) }}</td>
            @if($showComparison)<td style="font-weight: bold; text-align: right; background-color: {{ $totalBg }}; color: {{ $black }};">{{ $fmt($previous['revenue']) }}</td>@endif
        </tr>
        <tr><td colspan="{{ $colspan }}"></td></tr>

        {{-- HPP --}}
        <tr>
            <td colspan="{{ $colspan }}" style="font-weight: bold; background-color: {{ $navy }}; color: {{ $white }};">HARGA POKOK PENJUALAN</td>
        </tr>
        <tr>
            <td style="font-weight: bold; background-color: {{ $totalBg }}; color: {{ $black }};">Total HPP</td>
            <td style="font-weight: bold; text-align: right; background-color: {{ $totalBg }}; color: {{ $black }};">{{ $fmt($current['cogs']) }}</td>
            @if($showComparison)<td style="font-weight: bold; text-align: right; background-color: {{ $totalBg }}; color: {{ $black }};">{{ $fmt($previous['cogs']) }}</td>@endif
        </tr>
        <tr><td colspan="{{ $colspan }}"></td></tr>

        {{-- LABA KOTOR --}}
        <tr>
            <td style="font-weight: bold; background-color: {{ $subtotalBg }}; color: {{ $black }};">LABA KOTOR</td>
            <td style="font-weight: bold; text-align: right; background-color: {{ $subtotalBg }}; color: {{ $black }};">{{ $fmt($current['grossProfit']) }}</td>
            @if($showComparison)<td style="font-weight: bold; text-align: right; background-color: {{ $subtotalBg }}; color: {{ $black }};">{{ $fmt($previous['grossProfit']) }}</td>@endif
        </tr>
        <tr><td colspan="{{ $colspan }}"></td></tr>

        {{-- BEBAN OPERASIONAL --}}
        <tr>
            <td colspan="{{ $colspan }}" style="font-weight: bold; background-color: {{ $navy }}; color: {{ $white }};">BEBAN OPERASIONAL</td>
        </tr>
        @forelse($opCategories as $catName)
        <tr>
            <td style="color: {{ $black }};">&nbsp;&nbsp;{{ $catName }}</td>
            <td style="text-align: right; color: {{ $black }};">{{ $fmt($opCatCurrent[$catName] ?? 0) }}</td>
            @if($showComparison)<td style="text-align: right; color: {{ $black }};">{{ $fmt($opCatPrevious[$catName] ?? 0) }}</td>@endif
        </tr>
        @empty
        <tr>
            <td style="color: {{ $black }};">&nbsp;&nbsp;- Tidak ada beban operasional -</td>
            <td style="text-align: right; color: {{ $black }};">Rp 0</td>
            @if($showComparison)<td style="text-align: right; color: {{ $black }};">Rp 0</td>@endif
        </tr>
        @endforelse
        <tr>
            <td style="font-weight: bold; background-color: {{ $totalBg }}; color: {{ $black }};">Total Beban Operasional</td>
            <td style="font-weight: bold; text-align: right; background-color: {{ $totalBg }}; color: {{ $black }};">{{ $fmt($current['operatingExpenses']) }}</td>
            @if($showComparison)<td style="font-weight: bold; text-align: right; background-color: {{ $totalBg }}; color: {{ $black }};">{{ $fmt($previous['operatingExpenses']) }}</td>@endif
        </tr>
        <tr><td colspan="{{ $colspan }}"></td></tr>

        {{-- LABA USAHA (EBIT) --}}
        <tr>
            <td style="font-weight: bold; background-color: {{ $subtotalBg }}; color: {{ $black }};">LABA USAHA (EBIT)</td>
            <td style="font-weight: bold; text-align: right; background-color: {{ $subtotalBg }}; color: {{ $black }};">{{ $fmt($current['operatingProfit']) }}</td>
            @if($showComparison)<td style="font-weight: bold; text-align: right; background-color: {{ $subtotalBg }}; color: {{ $black }};">{{ $fmt($previous['operatingProfit']) }}</td>@endif
        </tr>

        @if($hasOtherIncomeExpense)
        <tr><td colspan="{{ $colspan }}"></td></tr>

        {{-- PENDAPATAN (BEBAN) LAIN-LAIN --}}
        <tr>
            <td colspan="{{ $colspan }}" style="font-weight: bold; background-color: {{ $navy }}; color: {{ $white }};">PENDAPATAN (BEBAN) LAIN-LAIN</td>
        </tr>
        @if($current['otherIncomeAmount'] > 0 || ($showComparison && $previous['otherIncomeAmount'] > 0))
        <tr>
            <td style="color: {{ $black }};">&nbsp;&nbsp;Pendapatan Lain-lain (Bunga, dsb.)</td>
            <td style="text-align: right; color: {{ $black }};">{{ $fmt($current['otherIncomeAmount']) }}</td>
            @if($showComparison)<td style="text-align: right; color: {{ $black }};">{{ $fmt($previous['otherIncomeAmount']) }}</td>@endif
        </tr>
        @endif
        @if($current['otherExpenseAmount'] > 0 || ($showComparison && $previous['otherExpenseAmount'] > 0))
        <tr>
            <td style="color: {{ $black }};">&nbsp;&nbsp;Beban Lain-lain (Bunga, dsb.)</td>
            <td style="text-align: right; color: {{ $black }};">{{ $fmtSigned(-$current['otherExpenseAmount']) }}</td>
            @if($showComparison)<td style="text-align: right; color: {{ $black }};">{{ $fmtSigned(-$previous['otherExpenseAmount']) }}</td>@endif
        </tr>
        @endif
        <tr>
            <td style="font-weight: bold; background-color: {{ $totalBg }}; color: {{ $black }};">Total Pendapatan (Beban) Lain-lain</td>
            <td style="font-weight: bold; text-align: right; background-color: {{ $totalBg }}; color: {{ $black }};">{{ $fmtSigned($current['otherIncomeExpenseNet']) }}</td>
            @if($showComparison)<td style="font-weight: bold; text-align: right; background-color: {{ $totalBg }}; color: {{ $black }};">{{ $fmtSigned($previous['otherIncomeExpenseNet']) }}</td>@endif
        </tr>
        @endif
        <tr><td colspan="{{ $colspan }}"></td></tr>

        {{-- LABA SEBELUM PAJAK --}}
        <tr>
            <td style="font-weight: bold; background-color: {{ $subtotalBg }}; color: {{ $black }};">LABA SEBELUM PAJAK</td>
            <td style="font-weight: bold; text-align: right; background-color: {{ $subtotalBg }}; color: {{ $black }};">{{ $fmt($current['netProfitBeforeTax']) }}</td>
            @if($showComparison)<td style="font-weight: bold; text-align: right; background-color: {{ $subtotalBg }}; color: {{ $black }};">{{ $fmt($previous['netProfitBeforeTax']) }}</td>@endif
        </tr>
        <tr><td colspan="{{ $colspan }}"></td></tr>

        {{-- BEBAN PAJAK --}}
        <tr>
            <td colspan="{{ $colspan }}" style="font-weight: bold; background-color: {{ $navy }}; color: {{ $white }};">BEBAN PAJAK PENGHASILAN</td>
        </tr>
        @if($current['taxScheme'] === 'umkm_final')
        <tr>
            <td style="color: {{ $black }};">&nbsp;&nbsp;Tarif PPh Final UMKM (dari Omzet)</td>
            <td style="text-align: right; color: {{ $black }};">{{ number_format($current['taxRateUmkm'], 2) }}%</td>
            @if($showComparison)<td style="text-align: right; color: {{ $black }};">{{ number_format($previous['taxRateUmkm'], 2) }}%</td>@endif
        </tr>
        <tr>
            <td style="color: {{ $black }};">&nbsp;&nbsp;Beban Pajak Penghasilan (Final)</td>
            <td style="text-align: right; color: {{ $black }};">{{ $fmt($current['taxExpenses']) }}</td>
            @if($showComparison)<td style="text-align: right; color: {{ $black }};">{{ $fmt($previous['taxExpenses']) }}</td>@endif
        </tr>
        @else
            @forelse($taxCategories as $catName)
            <tr>
                <td style="color: {{ $black }};">&nbsp;&nbsp;{{ $catName }}</td>
                <td style="text-align: right; color: {{ $black }};">{{ $fmt($taxCatCurrent[$catName] ?? 0) }}</td>
                @if($showComparison)<td style="text-align: right; color: {{ $black }};">{{ $fmt($taxCatPrevious[$catName] ?? 0) }}</td>@endif
            </tr>
            @empty
            <tr>
                <td style="color: {{ $black }};">&nbsp;&nbsp;- Tidak ada beban pajak -</td>
                <td style="text-align: right; color: {{ $black }};">Rp 0</td>
                @if($showComparison)<td style="text-align: right; color: {{ $black }};">Rp 0</td>@endif
            </tr>
            @endforelse
        @endif
        <tr>
            <td style="font-weight: bold; background-color: {{ $totalBg }}; color: {{ $black }};">Beban Pajak Penghasilan</td>
            <td style="font-weight: bold; text-align: right; background-color: {{ $totalBg }}; color: {{ $black }};">{{ $fmt($current['taxExpenses']) }}</td>
            @if($showComparison)<td style="font-weight: bold; text-align: right; background-color: {{ $totalBg }}; color: {{ $black }};">{{ $fmt($previous['taxExpenses']) }}</td>@endif
        </tr>
        <tr><td colspan="{{ $colspan }}"></td></tr>

        {{-- LABA BERSIH --}}
        <tr>
            <td style="font-weight: bold; background-color: {{ $navy }}; color: {{ $white }};">LABA (RUGI) BERSIH</td>
            <td style="font-weight: bold; text-align: right; background-color: {{ $navy }}; color: {{ $white }};">{{ $fmtSigned($current['netProfit']) }}</td>
            @if($showComparison)<td style="font-weight: bold; text-align: right; background-color: {{ $navy }}; color: {{ $white }};">{{ $fmtSigned($previous['netProfit']) }}</td>@endif
        </tr>
        <tr><td colspan="{{ $colspan }}"></td></tr>
        <tr><td colspan="{{ $colspan }}"></td></tr>

        {{-- CATATAN --}}
        <tr>
            <td colspan="{{ $colspan }}" style="font-weight: bold; color: {{ $black }};">Catatan Rumus Perhitungan:</td>
        </tr>
        <tr>
            <td colspan="{{ $colspan }}" style="font-style: italic; font-size: 9px; color: {{ $black }};">1. Total Pendapatan (Omzet) = Total Penjualan Kotor - Retur Penjualan</td>
        </tr>
        <tr>
            <td colspan="{{ $colspan }}" style="font-style: italic; font-size: 9px; color: {{ $black }};">2. Laba Kotor = Total Pendapatan (Omzet) - Total HPP</td>
        </tr>
        <tr>
            <td colspan="{{ $colspan }}" style="font-style: italic; font-size: 9px; color: {{ $black }};">3. Laba Usaha (EBIT) = Laba Kotor - Total Beban Operasional</td>
        </tr>
        <tr>
            <td colspan="{{ $colspan }}" style="font-style: italic; font-size: 9px; color: {{ $black }};">4. Laba Sebelum Pajak = Laba Usaha (EBIT) + Pendapatan (Beban) Lain-lain</td>
        </tr>
        <tr>
            <td colspan="{{ $colspan }}" style="font-style: italic; font-size: 9px; color: {{ $black }};">5. Laba (Rugi) Bersih = Laba Sebelum Pajak - Total Beban Pajak</td>
        </tr>
    </tbody>
</table>
