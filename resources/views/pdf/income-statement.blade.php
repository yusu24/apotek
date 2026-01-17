<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Laba Rugi</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; color: #333; }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .header-table td { text-align: center; }
        .store-name { font-size: 16pt; font-weight: bold; text-transform: uppercase; margin-bottom: 5px; text-align: center; }
        .report-title { font-size: 12pt; font-weight: bold; text-transform: uppercase; color: #555; text-align: center; }
        .period { font-size: 10pt; margin-top: 5px; color: #666; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 8px 10px; }
        thead th { background-color: #00BFFF; color: white; text-align: left; font-weight: bold; border-bottom: 2px solid #009ACD; }
        
        .section-header { 
            background-color: #f3f4f6; 
            font-weight: bold; 
            padding-top: 15px; 
            padding-bottom: 5px;
            color: #111;
            border-bottom: 1px solid #ddd;
        }
        
        .account-row td { padding-left: 20px; border-bottom: 1px solid #eee; }
        
        .subtotal-row td { 
            font-weight: bold; 
            background-color: #fce7f3; /* Light pink for expense subtotal */
            color: #be123c;
            border-top: 1px solid #fecdd3;
            padding-top: 5px;
            padding-bottom: 10px;
        }

        /* Specific style for Revenue total if needed different */
        .revenue-total-row td {
            font-weight: bold;
            background-color: #e0f2fe;
            color: #0369a1;
            border-top: 1px solid #bae6fd;
            padding-top: 5px;
            padding-bottom: 10px;
        }
        
        .section-total-row td {
            font-weight: bold;
            padding-top: 8px;
            padding-bottom: 8px;
            background-color: #f0f9ff;
            color: #0e7490;
        }

        .grand-total-row td {
            font-weight: bold;
            font-size: 12pt;
            background-color: #333; /* Dark background like Cash Flow */
            color: white;
            padding: 10px;
        }
        
        .text-right { text-align: right; }
        
        .footer { 
            margin-top: 50px; 
            border-top: 1px solid #eee; 
            padding-top: 10px; 
            font-size: 8pt; 
            color: #999; 
            display: flex; 
            justify-content: space-between;
        }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td>
                <div class="store-name">{{ $store['name'] }}</div>
                <div class="report-title">LABA RUGI</div>
                <div class="period">
                    Periode: {{ \Carbon\Carbon::parse($startDate)->format('d F Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}
                </div>
                <div style="font-size: 9pt; margin-top: 5px; font-style: italic; color: #666;">(dalam IDR)</div>
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th style="width: 70%">Tanggal</th>
                <th style="width: 30%; text-align: right;">{{ \Carbon\Carbon::parse($endDate)->format('Y') }}</th>
            </tr>
        </thead>
        <tbody>
            {{-- Pendapatan --}}
            <tr class="section-header"><td colspan="2">Pendapatan</td></tr>
            @foreach($reportData['revenue_accounts'] as $account)
                <tr class="account-row">
                    <td>{{ $account->code ?? '' }} {{ $account->name }}</td>
                    <td class="text-right">{{ number_format($account->balance, 2, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr class="subtotal-row">
                <td>Total dari Pendapatan</td>
                <td class="text-right">{{ number_format($reportData['total_revenue'], 2, ',', '.') }}</td>
            </tr>

            {{-- Harga Pokok Penjualan (HPP) --}}
            <tr class="section-header"><td colspan="2">Harga Pokok Penjualan (HPP)</td></tr>
            @foreach($reportData['cogs_accounts'] as $account)
                 <tr class="account-row">
                    <td>{{ $account->code ?? '' }} {{ $account->name }}</td>
                    <td class="text-right">( {{ number_format($account->balance, 2, ',', '.') }} )</td>
                </tr>
            @endforeach
             <tr class="subtotal-row">
                <td>Total HPP</td>
                <td class="text-right">( {{ number_format($reportData['total_cogs'], 2, ',', '.') }} )</td>
            </tr>

             {{-- Laba Kotor --}}
            <tr class="section-total-row">
                <td>Laba Kotor</td>
                <td class="text-right">{{ number_format($reportData['gross_profit'], 2, ',', '.') }}</td>
            </tr>

            {{-- Beban Operasional --}}
            <tr class="section-header"><td colspan="2">Beban Operasional</td></tr>
             @foreach($reportData['operating_expense_accounts'] as $account)
                 <tr class="account-row">
                    <td>{{ $account->code ?? '' }} {{ $account->name }}</td>
                    <td class="text-right">( {{ number_format($account->balance, 2, ',', '.') }} )</td>
                </tr>
            @endforeach
            <tr class="subtotal-row">
                <td>Total Beban Operasional</td>
                <td class="text-right">( {{ number_format($reportData['total_operating_expenses'], 2, ',', '.') }} )</td>
            </tr>

            {{-- Beban Lain-lain --}}
             @if($reportData['other_expense_accounts']->count() > 0)
                 <tr class="section-header"><td colspan="2">Beban Lain-Lain</td></tr>
                @foreach($reportData['other_expense_accounts'] as $account)
                     <tr class="account-row">
                        <td>{{ $account->code ?? '' }} {{ $account->name }}</td>
                        <td class="text-right">( {{ number_format($account->balance, 2, ',', '.') }} )</td>
                    </tr>
                @endforeach
                 <tr class="subtotal-row">
                    <td>Total Beban Lain-lain</td>
                    <td class="text-right">( {{ number_format($reportData['total_other_expenses'], 2, ',', '.') }} )</td>
                </tr>
             @endif
             
            {{-- Laba Sebelum Pajak --}}
            <tr class="section-total-row">
                <td>Laba Sebelum Pajak</td>
                <td class="text-right">{{ number_format($reportData['net_income_before_tax'] ?? $reportData['net_income'], 2, ',', '.') }}</td>
            </tr>

            {{-- Beban Pajak --}}
            @if(isset($reportData['tax_accounts']) && $reportData['tax_accounts']->count() > 0)
                <tr class="section-header"><td colspan="2">Beban Pajak (PPh)</td></tr>
                @foreach($reportData['tax_accounts'] as $account)
                     <tr class="account-row">
                        <td>{{ $account->code ?? '' }} {{ $account->name }}</td>
                        <td class="text-right">( {{ number_format($account->balance, 2, ',', '.') }} )</td>
                    </tr>
                @endforeach
                 <tr class="subtotal-row">
                    <td>Total Beban Pajak (PPh)</td>
                    <td class="text-right">( {{ number_format($reportData['total_tax_expenses'], 2, ',', '.') }} )</td>
                </tr>
            @endif

             <tr class="grand-total-row">
                <td>Laba Bersih</td>
                <td class="text-right">{{ number_format($reportData['net_income'], 2, ',', '.') }}</td>
            </tr>

        </tbody>
    </table>

    <div class="footer">
        <div style="float: left;">Laba Rugi : {{ $store['name'] }} {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</div>
        <div style="float: right;">Page 1 of 1</div>
    </div>
</body>
</html>
