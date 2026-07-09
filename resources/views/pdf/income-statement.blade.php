<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laba Rugi (Standar)</title>
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

        .full-width { width: 100%; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }

        .report-header {
            margin-bottom: 16px;
            text-align: center;
        }
        .store-name {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .report-title {
            font-size: 14pt;
            font-weight: bold;
            letter-spacing: 1px;
            color: #000000;
            margin-top: 4px;
        }
        .period-info {
            font-size: 10pt;
            margin-top: 3px;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 16px;
            font-size: 12pt;
        }

        /* Table Header Style */
        .column-headers td {
            padding: 6px;
            background-color: #1e40af;
            color: #ffffff;
            font-weight: bold;
        }

        td {
            padding: 4px 6px;
            vertical-align: bottom;
        }

        /* Hierarchy Levels */
        .level-0 { font-weight: bold; padding-top: 10px; text-transform: uppercase; }
        .level-1 { font-weight: bold; padding-left: 15px; padding-top: 5px; }
        .level-2 { font-weight: normal; padding-left: 30px; }
        .level-3 { font-weight: normal; padding-left: 45px; }

        /* Summary Lines */
        .summary-label { font-weight: bold; background-color: #dbeafe; }
        .summary-value {
            font-weight: bold;
            background-color: #dbeafe;
            text-align: right;
            width: 35%;
        }

        .grand-total-label {
            font-weight: bold;
            text-transform: uppercase;
            background-color: #1e40af;
            color: #ffffff;
            padding: 8px 6px;
        }
        .grand-total-value {
            font-weight: bold;
            text-align: right;
            background-color: #1e40af;
            color: #ffffff;
            padding: 8px 6px;
        }
    </style>
</head>
<body>
    <div class="report-header">
        <div class="store-name">{{ trim($store['name']) }}</div>
        <div class="report-title">LABA RUGI (STANDAR)</div>
        <div class="period-info">
            Untuk Periode {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
        </div>
    </div>

    <table>
        <thead>
            <tr class="column-headers">
                <td style="width: 65%">Deskripsi</td>
                <td style="width: 35%; text-align: right;">Nilai</td>
            </tr>
        </thead>
        <tbody>
            {{-- PENDAPATAN --}}
            <tr class="level-0"><td colspan="2">PENDAPATAN</td></tr>
            <tr class="level-1"><td colspan="2">PENDAPATAN OPERASIONAL</td></tr>
            @foreach($reportData['revenue_accounts'] as $account)
            <tr class="level-2"><td colspan="2">{{ $account->name }}</td></tr>
            <tr class="level-3">
                <td>Jumlah {{ $account->name }}</td>
                <td class="summary-value">{{ format_accounting_standard($account->amount) }}</td>
            </tr>
            @endforeach
            
            <tr class="level-1">
                <td class="summary-label">Jumlah Pendapatan</td>
                <td class="summary-value">{{ format_accounting_standard($reportData['total_revenue']) }}</td>
            </tr>

            {{-- HARGA POKOK PENJUALAN --}}
            <tr class="level-0"><td colspan="2">HARGA POKOK PENJUALAN</td></tr>
            @foreach($reportData['cogs_accounts'] as $account)
            <tr class="level-1"><td colspan="2">{{ $account->name }}</td></tr>
            <tr class="level-2">
                <td>Jumlah {{ $account->name }}</td>
                <td class="summary-value">{{ format_accounting_standard($account->amount) }}</td>
            </tr>
            @endforeach
            
            <tr class="level-0">
                <td class="summary-label">JUMLAH BEBAN POKOK PENJUALAN</td>
                <td class="summary-value">{{ format_accounting_standard($reportData['total_cogs']) }}</td>
            </tr>

            <tr class="grand-total-label">
                <td>LABA KOTOR</td>
                <td class="grand-total-value">{{ format_accounting_standard($reportData['gross_profit']) }}</td>
            </tr>

            {{-- BEBAN OPERASIONAL --}}
            <tr class="level-0"><td colspan="2">BEBAN</td></tr>
            <tr class="level-1"><td colspan="2">BEBAN OPERASIONAL</td></tr>
            @foreach($reportData['operating_expense_accounts'] as $account)
            <tr class="level-2"><td colspan="2">{{ $account->name }}</td></tr>
            <tr class="level-3">
                <td>Jumlah {{ $account->name }}</td>
                <td class="summary-value">{{ format_accounting_standard($account->amount) }}</td>
            </tr>
            @endforeach
            
            <tr class="level-1">
                <td class="summary-label">Jumlah Beban Operasional</td>
                <td class="summary-value">{{ format_accounting_standard($reportData['total_operating_expenses']) }}</td>
            </tr>

            {{-- BEBAN LAIN-LAIN --}}
            @if($reportData['other_expense_accounts']->count() > 0)
            <tr class="level-1"><td colspan="2">BEBAN LAIN-LAIN</td></tr>
            @foreach($reportData['other_expense_accounts'] as $account)
            <tr class="level-2"><td colspan="2">{{ $account->name }}</td></tr>
            <tr class="level-3">
                <td>Jumlah {{ $account->name }}</td>
                <td class="summary-value">{{ format_accounting_standard($account->amount) }}</td>
            </tr>
            @endforeach
            <tr class="level-1">
                <td class="summary-label">Jumlah Beban Lain-lain</td>
                <td class="summary-value">{{ format_accounting_standard($reportData['total_other_expenses']) }}</td>
            </tr>
            @endif

            <tr class="grand-total-label">
                <td>LABA SEBELUM PAJAK</td>
                <td class="grand-total-value">{{ format_accounting_standard($reportData['net_income_before_tax']) }}</td>
            </tr>

            {{-- PAJAK --}}
            @if($reportData['tax_accounts']->count() > 0)
            <tr class="level-0"><td colspan="2">BEBAN PAJAK</td></tr>
            @foreach($reportData['tax_accounts'] as $account)
            <tr class="level-1"><td colspan="2">{{ $account->name }}</td></tr>
            <tr class="level-2">
                <td>Jumlah {{ $account->name }}</td>
                <td class="summary-value">{{ format_accounting_standard($account->amount) }}</td>
            </tr>
            @endforeach
            <tr class="level-0">
                <td class="summary-label">JUMLAH BEBAN PAJAK</td>
                <td class="summary-value">{{ format_accounting_standard($reportData['total_tax_expenses']) }}</td>
            </tr>
            @endif

            <tr class="grand-total-label">
                <td>LABA BERSIH</td>
                <td class="grand-total-value">{{ format_accounting_standard($reportData['net_income']) }}</td>
            </tr>
        </tbody>
    </table>

</body>
</html>
