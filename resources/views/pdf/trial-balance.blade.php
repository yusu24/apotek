<!DOCTYPE html>
<html>
<head>
    <title>Neraca Saldo</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 10px;
        }
        .store-name {
            font-size: 18px;
            font-weight: bold;
            color: #1e3a8a;
            text-transform: uppercase;
        }
        .report-title {
            font-size: 16px;
            margin: 5px 0;
            font-weight: bold;
            text-align: center;
        }
        .period {
            font-size: 12px;
            color: #666;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #1e3a8a;
            color: white;
            padding: 8px;
            text-align: left;
            text-transform: uppercase;
            font-size: 10px;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #e5e7eb;
        }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .bg-gray-100 { background-color: #f3f4f6; }
        .bg-blue-50 { background-color: #eff6ff; }
        .bg-yellow-50 { background-color: #fefce8; }
        .bg-purple-50 { background-color: #faf5ff; }
        .bg-green-50 { background-color: #f0fdf4; }
        .bg-red-50 { background-color: #fef2f2; }
        .footer {
            position: fixed;
            bottom: -30px;
            left: 0;
            right: 0;
            font-size: 8pt;
            color: #666;
            text-align: left;
            border-top: 1px solid #eee;
            padding-top: 5px;
        }
        .footer .right {
            float: right;
        }
        .grand-total {
            background-color: #111827;
            color: white;
            font-weight: bold;
        }
        .section-header {
            font-weight: bold;
            background-color: #f1f5f9;
        }
    </style>
</head>
<body>
    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin-bottom: 30px; border-bottom: 2px solid #1e3a8a; padding-bottom: 10px;">
        <tr>
            <td align="center">
                <div style="font-size: 18px; font-weight: bold; color: #1e3a8a; text-transform: uppercase; text-align: center;">{{ $store['name'] }}</div>
                <div style="font-size: 16px; margin-top: 5px; font-weight: bold; text-align: center;">NERACA SALDO (TRIAL BALANCE)</div>
                <div style="font-size: 12px; color: #666; margin-top: 5px; text-align: center;">
                    Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
                </div>
            </td>
        </tr>
    </table>

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
                        <td colspan="2">Subtotal {{ $section['label'] }}</td>
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
        PERINGATAN: Neraca tidak balance! Selisih: Rp {{ number_format(abs($reportData['difference']), 0, ',', '.') }}
    </div>
    @endif
    <div class="footer">
        Dicetak oleh: {{ $printedBy }}
        <span class="right">Waktu Cetak: {{ $printedAt }}</span>
    </div>
</body>
</html>
