<!DOCTYPE html>
<html>
<head>
    <title>Neraca Saldo</title>
    <style>
        @page { 
            size: A4; 
            margin:    10mm 1cm 10mm 1cm; 
        }
        
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: 10pt; 
            color: #1a1a1a; 
            margin: 0; 
            padding: 0; 
            line-height: 1.4;
        }

        .full-width { width: 100%; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }

        .report-header { 
            margin-bottom: 30px; 
            display: block;
            width: 100%;
        }
        .store-name { 
            font-size: 16pt; 
            font-weight: bold; 
            margin: 0; 
        }
        .report-title { 
            font-size: 13pt; 
            font-weight: bold; 
            color: #333; 
            margin-top: 4px;
            letter-spacing: 1px;
        }
        .period-info { 
            font-size: 11pt; 
            color: #4b5563; 
            margin-top: 6px; 
        }
        
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 10px; 
            table-layout: fixed; 
        }
        th { 
            padding: 10px 12px;
            background-color: #f8fafc;
            color: #1e293b;
            text-align: left;
            font-weight: bold;
            border-top: 2pt solid #1e293b;
            border-bottom: 1pt solid #cbd5e1;
            font-size: 11pt;
        }
        td { 
            padding: 8px 12px; 
            vertical-align: middle; 
            font-size: 12pt;
            border-bottom: 1px solid #f1f5f9;
        }
        
        .section-header td { 
            background-color: #f1f5f9; 
            font-weight: bold; 
            padding-top: 12px; 
            padding-bottom: 10px;
            color: #0f172a;
        }
        
        .grand-total td {
            font-weight: bold;
            background-color: #1e293b;
            color: white;
            padding: 12px;
        }

        </style>
</head>
<body>
    <div class="report-header text-center">
        <div class="store-name uppercase">{{ trim($store['name']) }}</div>
        <div class="report-title">NERACA SALDO (TRIAL BALANCE)</div>
        <div class="period-info">
            Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
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
    </body>
</html>
