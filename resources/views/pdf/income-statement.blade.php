<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Laba Rugi</title>
    <style>
        @page { margin: 20px 40px; }
        body { font-family: sans-serif; font-size: 9pt; color: #000; margin: 0; padding: 0; }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .header-table td { text-align: center; }
        .store-name { font-size: 16pt; font-weight: bold; text-transform: uppercase; margin-bottom: 5px; text-align: center; }
        .report-title { font-size: 12pt; font-weight: bold; text-transform: uppercase; color: #555; text-align: center; }
        .period { font-size: 10pt; margin-top: 5px; color: #666; text-align: center; }
        .currency { font-size: 9pt; margin-top: 5px; font-style: italic; color: #666; text-align: center; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        th, td { padding: 4px 5px; vertical-align: top; }
        
        thead th { 
            background-color: #00BFFF; /* Cyan color matching image */
            color: white; 
            text-align: left; 
            font-weight: normal; 
            padding: 5px 10px;
        }
        
        .section-header { 
            font-weight: bold; 
            background-color: #f9fafb;
            padding-top: 10px;
        }
        
        .account-row td { padding-left: 20px; }
        
        .subtotal-row td { 
            font-weight: bold; 
            border-top: 1px solid #ccc;
            padding-top: 5px;
            padding-bottom: 10px;
        }
        
        .section-total-row td {
            font-weight: bold;
            padding-top: 5px;
            padding-bottom: 10px;
        }

        .grand-total-row td {
            font-weight: bold;
            border-top: 1px solid #000;
            padding-top: 5px;
        }
        
        .text-right { text-align: right; }
        
        .footer { 
            position: fixed; 
            bottom: 0px; 
            left: 0px; 
            right: 0px; 
            height: 20px; 
            font-size: 8pt;
            border-top: 1px solid #000;
            padding-top: 2px;
        }
    </style>
</head>
<body>
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 25px;">
        <tr>
            <td align="center" style="text-align: center; vertical-align: middle;">
                <center>
                    <div class="store-name" style="font-size: 16pt; font-weight: bold; text-transform: uppercase; margin-bottom: 5px; text-align: center;">{{ $store['name'] }}</div>
                    <div class="report-title" style="font-size: 12pt; font-weight: bold; text-transform: uppercase; color: #555; text-align: center;">LABA RUGI</div>
                    <div class="period" style="font-size: 10pt; margin-top: 5px; color: #666; text-align: center;">
                        Periode: {{ \Carbon\Carbon::parse($startDate)->format('d F Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}
                    </div>
                    <div class="currency" style="font-size: 9pt; margin-top: 5px; font-style: italic; color: #666; text-align: center;">(dalam IDR)</div>
                </center>
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
