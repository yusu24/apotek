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
    <table class="header-table">
        <tr>
            <td>
                <div class="store-name">{{ $store['name'] }}</div>
                <div class="report-title">LABA RUGI</div>
                <div class="period">
                    Periode: {{ \Carbon\Carbon::parse($startDate)->format('d F Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}
                </div>
                <div class="currency">(dalam IDR)</div>
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

            {{-- Beban Pokok Pendapatan --}}
            <tr class="section-header"><td colspan="2">Beban Pokok Pendapatan</td></tr>
            @foreach($reportData['cogs_accounts'] as $account)
                 <tr class="account-row">
                    <td>{{ $account->code ?? '' }} {{ $account->name }}</td>
                    <td class="text-right">( {{ number_format($account->balance, 2, ',', '.') }} )</td>
                </tr>
            @endforeach
             <tr class="subtotal-row">
                <td>Total dari Beban Pokok Pendapatan</td>
                <td class="text-right">( {{ number_format($reportData['total_cogs'], 2, ',', '.') }} )</td>
            </tr>

             {{-- Laba Kotor --}}
            <tr class="section-total-row">
                <td>Laba Kotor</td>
                <td class="text-right">{{ number_format($reportData['gross_profit'], 2, ',', '.') }}</td>
            </tr>

            {{-- Beban Umum dan Administrasi --}}
            <tr class="section-header"><td colspan="2">Beban Umum dan Administrasi</td></tr>
             @foreach($reportData['operating_expense_accounts'] as $account)
                 <tr class="account-row">
                    <td>{{ $account->code ?? '' }} {{ $account->name }}</td>
                    <td class="text-right">( {{ number_format($account->balance, 2, ',', '.') }} )</td>
                </tr>
            @endforeach
            <tr class="subtotal-row">
                <td>Total dari Beban Umum dan Administrasi</td>
                <td class="text-right">( {{ number_format($reportData['total_operating_expenses'], 2, ',', '.') }} )</td>
            </tr>

            {{-- Pendapatan (Beban Lain-lain) title only to match image logic if needed contextually --}}
             <tr class="section-header"><td colspan="2">Pendapatan (Beban Lain-lain)</td></tr>
             
             @if($reportData['other_expense_accounts']->count() > 0)
                <tr class="section-header" style="padding-left: 10px;"><td colspan="2" style="font-weight: normal; font-style: italic;">Beban Lain-Lain</td></tr>
                @foreach($reportData['other_expense_accounts'] as $account)
                     <tr class="account-row">
                        <td>{{ $account->code ?? '' }} {{ $account->name }}</td>
                        <td class="text-right">{{ number_format($account->balance, 2, ',', '.') }}</td> {{-- Usually negative --}}
                    </tr>
                @endforeach
             @endif
            
             <tr class="subtotal-row">
                <td>Total dari Pendapatan (Beban Lain-lain)</td>
                <td class="text-right">{{ number_format($reportData['total_other_expenses'] * -1, 2, ',', '.') }}</td>
            </tr>

            {{-- EBITDA - Assuming we don't calculate Depreciation separately yet, using Net Income logic for now --}}
            {{-- For simplicity matching current logic to image structure --}}
            
             <tr class="grand-total-row">
                <td>EAT (Earnings After Tax Expense)</td>
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
