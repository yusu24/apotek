<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta charset="UTF-8">
    <title>Laporan Laba Rugi - {{ $storeName }}</title>
    <style>
        @page { margin: 1cm 1.2cm; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12pt;
            line-height: 1.4;
            color: #1a1a1a;
            margin: 0;
            padding: 0;
        }

        /* ===== LETTERHEAD ===== */
        .letterhead { text-align: center; }
        .store-name { font-size: 14pt; font-weight: bold; text-transform: uppercase; text-align: center; }
        .report-title { font-size: 14pt; font-weight: bold; letter-spacing: 1px; color: #000000; text-align: center; margin-top: 4px; }
        .report-period { font-size: 10pt; text-align: center; margin-top: 3px; color: #333; }

        /* ===== BODY TABLE ===== */
        table.p-l-table { width: 100%; border-collapse: collapse; margin-top: 16px; table-layout: fixed; font-size: 12pt; }
        .p-l-table td, .p-l-table th { padding: 4px 6px; vertical-align: top; }
        .p-l-table .label { width: 46%; }
        .p-l-table .amount { width: 27%; text-align: right; }
        .p-l-table.single-period .label { width: 60%; }
        .p-l-table.single-period .amount { width: 40%; }

        .col-header th { font-size: 12pt; font-weight: bold; text-align: center; background-color: #1e40af; color: #fff; padding: 6px; }
        .col-header .label { text-align: left; }

        .section-title td { font-weight: bold; background-color: #eef2f9; color: #1e40af; padding-top: 6px; padding-bottom: 6px; }
        .sub-item .label { padding-left: 18px; }
        .total-item td { font-weight: bold; border-top: 1px solid #999; }
        .subtotal-item td { font-weight: bold; background-color: #f3f4f6; border-top: 1px solid #999; border-bottom: 1px solid #999; }
        .grand-total td { font-weight: bold; background-color: #1e40af; color: #ffffff; border-top: 2px solid #1e40af; border-bottom: 2px solid #1e40af; padding: 8px 6px; }
        .spacer td { height: 8px; padding: 0; }

        /* ===== FOOTER NOTES ===== */
        .notes { margin-top: 18px; font-size: 10pt; color: #555; }
        .notes p { margin: 3px 0; }
    </style>
</head>
<body>
    @php
        $colspan = $showComparison ? 3 : 2;

        $revCatCurrent = collect($current['revenueByCategory'])->keyBy('category_name');
        $revCatPrevious = $showComparison ? collect($previous['revenueByCategory'])->keyBy('category_name') : collect();
        $revCategories = $revCatCurrent->keys()->merge($revCatPrevious->keys())->unique()->sort()->values();

        $opCatCurrent = $current['expenseDetails']->groupBy('category')->map(fn($items) => $items->sum('amount'));
        $opCatPrevious = ($showComparison ? $previous['expenseDetails'] : collect())->groupBy('category')->map(fn($items) => $items->sum('amount'));
        $opCategories = $opCatCurrent->keys()->merge($opCatPrevious->keys())->unique()->sort()->values();

        $taxCatCurrent = $current['taxDetails']->groupBy('category')->map(fn($items) => $items->sum('amount'));
        $taxCatPrevious = ($showComparison ? $previous['taxDetails'] : collect())->groupBy('category')->map(fn($items) => $items->sum('amount'));
        $taxCategories = $taxCatCurrent->keys()->merge($taxCatPrevious->keys())->unique()->sort()->values();

        $fmt = fn($v) => 'Rp. ' . number_format((float) $v, 0, ',', '.') . ',-';
        $fmtSigned = fn($v) => $v < 0 ? '(' . $fmt(abs($v)) . ')' : $fmt($v);
    @endphp
    <div class="letterhead">
        <div class="store-name">{{ trim($storeName) }}</div>
    </div>
    <div class="report-title">LAPORAN LABA RUGI</div>
    <div class="report-period">
        Untuk Periode {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
    </div>

    <table class="p-l-table {{ $showComparison ? '' : 'single-period' }}">
        <tbody>
            <tr class="col-header">
                <th class="label">Keterangan</th>
                <th>{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</th>
                @if($showComparison)
                <th>{{ \Carbon\Carbon::parse($prevStartDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($prevEndDate)->format('d/m/Y') }}</th>
                @endif
            </tr>

            {{-- PENDAPATAN --}}
            <tr class="section-title">
                <td class="label">PENDAPATAN</td>
                <td class="amount"></td>
                @if($showComparison)<td class="amount"></td>@endif
            </tr>
            @foreach($revCategories as $catName)
            <tr class="sub-item">
                <td class="label">Penjualan {{ $catName }}</td>
                <td class="amount">{{ $fmt($revCatCurrent[$catName]->total ?? 0) }}</td>
                @if($showComparison)<td class="amount">{{ $fmt($revCatPrevious[$catName]->total ?? 0) }}</td>@endif
            </tr>
            @endforeach
            <tr class="total-item">
                <td class="label">TOTAL PENDAPATAN (OMZET)</td>
                <td class="amount">{{ $fmt($current['revenue']) }}</td>
                @if($showComparison)<td class="amount">{{ $fmt($previous['revenue']) }}</td>@endif
            </tr>

            <tr class="spacer"><td colspan="{{ $colspan }}"></td></tr>

            {{-- HPP --}}
            <tr class="section-title">
                <td class="label">HARGA POKOK PENJUALAN</td>
                <td class="amount"></td>
                @if($showComparison)<td class="amount"></td>@endif
            </tr>
            <tr class="total-item">
                <td class="label">TOTAL HPP</td>
                <td class="amount">{{ $fmt($current['cogs']) }}</td>
                @if($showComparison)<td class="amount">{{ $fmt($previous['cogs']) }}</td>@endif
            </tr>

            <tr class="spacer"><td colspan="{{ $colspan }}"></td></tr>

            {{-- LABA KOTOR --}}
            <tr class="subtotal-item">
                <td class="label">LABA KOTOR</td>
                <td class="amount">{{ $fmt($current['grossProfit']) }}</td>
                @if($showComparison)<td class="amount">{{ $fmt($previous['grossProfit']) }}</td>@endif
            </tr>

            <tr class="spacer"><td colspan="{{ $colspan }}"></td></tr>

            {{-- BEBAN OPERASIONAL --}}
            <tr class="section-title">
                <td class="label">BEBAN OPERASIONAL</td>
                <td class="amount"></td>
                @if($showComparison)<td class="amount"></td>@endif
            </tr>
            @forelse($opCategories as $catName)
            <tr class="sub-item">
                <td class="label">{{ $catName }}</td>
                <td class="amount">{{ $fmt($opCatCurrent[$catName] ?? 0) }}</td>
                @if($showComparison)<td class="amount">{{ $fmt($opCatPrevious[$catName] ?? 0) }}</td>@endif
            </tr>
            @empty
            <tr class="sub-item">
                <td class="label">- Tidak ada beban operasional -</td>
                <td class="amount">Rp. 0,-</td>
                @if($showComparison)<td class="amount">Rp. 0,-</td>@endif
            </tr>
            @endforelse
            <tr class="total-item">
                <td class="label">TOTAL BEBAN OPERASIONAL</td>
                <td class="amount">{{ $fmt($current['operatingExpenses']) }}</td>
                @if($showComparison)<td class="amount">{{ $fmt($previous['operatingExpenses']) }}</td>@endif
            </tr>

            <tr class="spacer"><td colspan="{{ $colspan }}"></td></tr>

            {{-- LABA USAHA (EBIT) --}}
            <tr class="subtotal-item">
                <td class="label">LABA USAHA (EBIT)</td>
                <td class="amount">{{ $fmt($current['operatingProfit']) }}</td>
                @if($showComparison)<td class="amount">{{ $fmt($previous['operatingProfit']) }}</td>@endif
            </tr>

            @php
                $hasOtherIncomeExpense = $current['otherIncomeAmount'] > 0 || $current['otherExpenseAmount'] > 0
                    || ($showComparison && ($previous['otherIncomeAmount'] > 0 || $previous['otherExpenseAmount'] > 0));
            @endphp
            @if($hasOtherIncomeExpense)
            <tr class="spacer"><td colspan="{{ $colspan }}"></td></tr>

            {{-- PENDAPATAN (BEBAN) LAIN-LAIN --}}
            <tr class="section-title">
                <td class="label">PENDAPATAN (BEBAN) LAIN-LAIN</td>
                <td class="amount"></td>
                @if($showComparison)<td class="amount"></td>@endif
            </tr>
            @if($current['otherIncomeAmount'] > 0 || ($showComparison && $previous['otherIncomeAmount'] > 0))
            <tr class="sub-item">
                <td class="label">Pendapatan Lain-lain (Bunga, dsb.)</td>
                <td class="amount">{{ $fmt($current['otherIncomeAmount']) }}</td>
                @if($showComparison)<td class="amount">{{ $fmt($previous['otherIncomeAmount']) }}</td>@endif
            </tr>
            @endif
            @if($current['otherExpenseAmount'] > 0 || ($showComparison && $previous['otherExpenseAmount'] > 0))
            <tr class="sub-item">
                <td class="label">Beban Lain-lain (Bunga, dsb.)</td>
                <td class="amount">{{ $fmtSigned(-$current['otherExpenseAmount']) }}</td>
                @if($showComparison)<td class="amount">{{ $fmtSigned(-$previous['otherExpenseAmount']) }}</td>@endif
            </tr>
            @endif
            <tr class="total-item">
                <td class="label">TOTAL PENDAPATAN (BEBAN) LAIN-LAIN</td>
                <td class="amount">{{ $fmtSigned($current['otherIncomeExpenseNet']) }}</td>
                @if($showComparison)<td class="amount">{{ $fmtSigned($previous['otherIncomeExpenseNet']) }}</td>@endif
            </tr>
            @endif

            <tr class="spacer"><td colspan="{{ $colspan }}"></td></tr>

            {{-- LABA SEBELUM PAJAK --}}
            <tr class="subtotal-item">
                <td class="label">LABA SEBELUM PAJAK</td>
                <td class="amount">{{ $fmt($current['netProfitBeforeTax']) }}</td>
                @if($showComparison)<td class="amount">{{ $fmt($previous['netProfitBeforeTax']) }}</td>@endif
            </tr>

            <tr class="spacer"><td colspan="{{ $colspan }}"></td></tr>

            {{-- BEBAN PAJAK --}}
            <tr class="section-title">
                <td class="label">BEBAN PAJAK PENGHASILAN</td>
                <td class="amount"></td>
                @if($showComparison)<td class="amount"></td>@endif
            </tr>
            @if($current['taxScheme'] === 'umkm_final')
            <tr class="sub-item">
                <td class="label">Tarif PPh Final UMKM (dari Omzet)</td>
                <td class="amount">{{ number_format($current['taxRateUmkm'], 2) }}%</td>
                @if($showComparison)<td class="amount">{{ number_format($previous['taxRateUmkm'], 2) }}%</td>@endif
            </tr>
            <tr class="sub-item">
                <td class="label">Beban Pajak Penghasilan (Final)</td>
                <td class="amount">{{ $fmt($current['taxExpenses']) }}</td>
                @if($showComparison)<td class="amount">{{ $fmt($previous['taxExpenses']) }}</td>@endif
            </tr>
            @else
                @forelse($taxCategories as $catName)
                <tr class="sub-item">
                    <td class="label">{{ $catName }}</td>
                    <td class="amount">{{ $fmt($taxCatCurrent[$catName] ?? 0) }}</td>
                    @if($showComparison)<td class="amount">{{ $fmt($taxCatPrevious[$catName] ?? 0) }}</td>@endif
                </tr>
                @empty
                <tr class="sub-item">
                    <td class="label">- Tidak ada beban pajak -</td>
                    <td class="amount">Rp. 0,-</td>
                    @if($showComparison)<td class="amount">Rp. 0,-</td>@endif
                </tr>
                @endforelse
            @endif
            <tr class="total-item">
                <td class="label">TOTAL BEBAN PAJAK</td>
                <td class="amount">{{ $fmt($current['taxExpenses']) }}</td>
                @if($showComparison)<td class="amount">{{ $fmt($previous['taxExpenses']) }}</td>@endif
            </tr>

            <tr class="spacer"><td colspan="{{ $colspan }}"></td></tr>

            {{-- LABA BERSIH --}}
            <tr class="grand-total">
                <td class="label">LABA (RUGI) BERSIH</td>
                <td class="amount">{{ $fmtSigned($current['netProfit']) }}</td>
                @if($showComparison)<td class="amount">{{ $fmtSigned($previous['netProfit']) }}</td>@endif
            </tr>
        </tbody>
    </table>

    <div class="notes">
        <p><strong>Catatan Rumus Perhitungan:</strong></p>
        <p>1. Total Pendapatan (Omzet) = Total Penjualan Kotor - Retur Penjualan</p>
        <p>2. Laba Kotor = Total Pendapatan (Omzet) - Total HPP</p>
        <p>3. Laba Usaha (EBIT) = Laba Kotor - Total Beban Operasional</p>
        <p>4. Laba Sebelum Pajak = Laba Usaha (EBIT) + Pendapatan (Beban) Lain-lain</p>
        <p>5. Laba (Rugi) Bersih = Laba Sebelum Pajak - Total Beban Pajak</p>
    </div>
</body>
</html>
