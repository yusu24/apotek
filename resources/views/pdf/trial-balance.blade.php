<!DOCTYPE html>
<html>
<head>
    <title>Neraca Saldo</title>
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
        th {
            padding: 6px;
            background-color: #1e40af;
            color: #ffffff;
            text-align: left;
            font-weight: bold;
        }
        td {
            padding: 4px 6px;
            vertical-align: middle;
            border-bottom: 0.5pt solid #eee;
        }

        .section-header td {
            background-color: #eef2f9;
            font-weight: bold;
            color: #1e40af;
            padding-top: 6px;
            padding-bottom: 6px;
        }

        .grand-total td {
            font-weight: bold;
            background-color: #1e40af;
            color: #ffffff;
            padding: 8px 6px;
        }

        </style>
</head>
<body>
    <div class="report-header">
        <div class="store-name">{{ trim($store['name']) }}</div>
        <div class="report-title">NERACA SALDO (TRIAL BALANCE)</div>
        <div class="period-info">
            Untuk Periode {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="15%">KODE</th>
                <th width="45%">NAMA AKUN</th>
                <th width="20%" class="text-right">DEBIT</th>
                <th width="20%" class="text-right">KREDIT</th>
            </tr>
        </thead>
        <tbody>
            @php
                $sections = [
                    ['label' => 'ASET', 'data' => $reportData['assets'], 'debit' => $reportData['total_assets_debit'], 'credit' => $reportData['total_assets_credit'], 'class' => 'bg-blue-50'],
                    ['label' => 'KEWAJIBAN', 'data' => $reportData['liabilities'], 'debit' => $reportData['total_liabilities_debit'], 'credit' => $reportData['total_liabilities_credit'], 'class' => 'bg-yellow-50'],
                    ['label' => 'EKUITAS', 'data' => $reportData['equity'], 'debit' => $reportData['total_equity_debit'], 'credit' => $reportData['total_equity_credit'], 'class' => 'bg-purple-50'],
                    ['label' => 'PENDAPATAN', 'data' => $reportData['revenue'], 'debit' => $reportData['total_revenue_debit'], 'credit' => $reportData['total_revenue_credit'], 'class' => 'bg-green-50'],
                    ['label' => 'BEBAN', 'data' => $reportData['expenses'], 'debit' => $reportData['total_expenses_debit'], 'credit' => $reportData['total_expenses_credit'], 'class' => 'bg-red-50'],
                ];
            @endphp

            @foreach($sections as $section)
                @if(count($section['data']) > 0)
                    <tr class="section-header">
                        <td colspan="4">{{ $section['label'] }}</td>
                    </tr>
                    @foreach($section['data'] as $account)
                        <tr>
                            <td>{{ $account->code }}</td>
                            <td>{{ $account->name }}</td>
                            <td class="text-right">
                                {{ $account->total_debit > 0 ? number_format($account->total_debit, 0, ',', '.') : '-' }}
                            </td>
                            <td class="text-right">
                                {{ $account->total_credit > 0 ? number_format($account->total_credit, 0, ',', '.') : '-' }}
                            </td>
                        </tr>
                    @endforeach
                    <tr class="{{ $section['class'] }} font-bold">
                        <td colspan="2">Subtotal {{ ucwords(strtolower($section['label'])) }}</td>
                        <td class="text-right">{{ number_format($section['debit'], 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($section['credit'], 0, ',', '.') }}</td>
                    </tr>
                @endif
            @endforeach

            <tr class="grand-total">
                <td colspan="2">TOTAL</td>
                <td class="text-right">{{ number_format($reportData['grand_total_debit'], 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($reportData['grand_total_credit'], 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    @if(!$reportData['is_balanced'])
    <div style="color: #b91c1c; font-weight: bold; margin-bottom: 15px;">
        PERINGATAN: Neraca tidak balance! Selisih: Rp. {{ number_format(abs($reportData['difference']), 0, ',', '.') }},-
    </div>
    @endif
    </body>
</html>
