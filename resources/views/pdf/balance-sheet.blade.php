<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Neraca (Balance Sheet)</title>
    <style>
        @page { margin: 20px 40px; }
        body { font-family: sans-serif; font-size: 9pt; color: #000; margin: 0; padding: 0; }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .header-table td { text-align: center; }
        .store-name { font-size: 16pt; font-weight: bold; text-transform: uppercase; margin-bottom: 5px; text-align: center; }
        .report-title { font-size: 12pt; font-weight: bold; text-transform: uppercase; color: #555; text-align: center; }
        .period { font-size: 10pt; margin-top: 5px; color: #666; text-align: center; }
        .currency { font-size: 9pt; margin-top: 5px; font-style: italic; color: #666; text-align: center; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 5px; page-break-inside: auto; }
        th, td { padding: 5px 8px; vertical-align: top; }
        
        thead th { 
            background-color: #00BFFF; /* Cyan color consistent with P&L */
            color: white; 
            text-align: left; 
            font-weight: normal; 
            padding: 6px 10px;
        }
        
        .section-header { 
            font-weight: bold; 
            background-color: #f9fafb;
            padding-top: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .subsection-header {
            font-weight: bold;
            padding-top: 8px;
            padding-left: 5px;
            color: #444;
        }

        .account-row td { padding-left: 20px; border-bottom: 0.5px solid #f0f0f0; }
        
        .total-row td { 
            font-weight: bold; 
            border-top: 1px solid #ccc;
            padding-top: 6px;
            padding-bottom: 15px;
        }
        
        .grand-total-row td {
            font-weight: bold;
            font-size: 10pt;
            background-color: #f0f9ff;
            border-top: 2px solid #00BFFF;
            padding-top: 8px;
            padding-bottom: 8px;
        }

        .alert-unbalanced {
            background-color: #fee2e2;
            color: #b91c1c;
            padding: 10px;
            margin-bottom: 15px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #f87171;
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
                <div class="report-title">NERACA (BALANCE SHEET)</div>
                <div class="period">
                    Periode: {{ \Carbon\Carbon::parse($startDate)->format('d F Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}
                </div>
                <div class="currency">(dalam IDR)</div>
            </td>
        </tr>
    </table>

    @if(!$reportData['balance_check'])
    <div class="alert-unbalanced">
        PERHATIAN: NERACA TIDAK BALANCE! Total Aset ≠ Liabilitas + Ekuitas
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th style="width: 70%">Deskripsi Akun</th>
                <th style="width: 30%; text-align: right;">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            {{-- ASET --}}
            <tr class="section-header"><td colspan="2">ASET</td></tr>
            
            <tr class="subsection-header"><td colspan="2">Aset Lancar</td></tr>
            @foreach($reportData['current_assets'] as $account)
            <tr class="account-row">
                <td>{{ $account->name }}</td>
                <td class="text-right">{{ number_format($account->balance, 2, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td style="padding-left: 5px;">Total Aset Lancar</td>
                <td class="text-right">{{ number_format($reportData['total_current_assets'], 2, ',', '.') }}</td>
            </tr>

            <tr class="subsection-header"><td colspan="2">Aset Tetap</td></tr>
            @foreach($reportData['fixed_assets'] as $account)
            <tr class="account-row">
                <td>{{ $account->name }}</td>
                <td class="text-right">{{ number_format($account->balance, 2, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td style="padding-left: 5px;">Total Aset Tetap</td>
                <td class="text-right">{{ number_format($reportData['total_fixed_assets'], 2, ',', '.') }}</td>
            </tr>

            <tr class="grand-total-row">
                <td>TOTAL ASET</td>
                <td class="text-right">{{ number_format($reportData['total_assets'], 2, ',', '.') }}</td>
            </tr>

            {{-- SPACING --}}
            <tr><td colspan="2" style="height: 20px;"></td></tr>

            {{-- LIABILITAS --}}
            <tr class="section-header"><td colspan="2">LIABILITAS</td></tr>
            
            <tr class="subsection-header"><td colspan="2">Liabilitas Lancar</td></tr>
            @foreach($reportData['current_liabilities'] as $account)
            <tr class="account-row">
                <td>{{ $account->name }}</td>
                <td class="text-right">{{ number_format($account->balance, 2, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td style="padding-left: 5px;">Total Liabilitas Lancar</td>
                <td class="text-right">{{ number_format($reportData['total_current_liabilities'], 2, ',', '.') }}</td>
            </tr>

            <tr class="subsection-header"><td colspan="2">Liabilitas Jangka Panjang</td></tr>
            @foreach($reportData['long_term_liabilities'] as $account)
            <tr class="account-row">
                <td>{{ $account->name }}</td>
                <td class="text-right">{{ number_format($account->balance, 2, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td style="padding-left: 5px;">Total Liabilitas Jangka Panjang</td>
                <td class="text-right">{{ number_format($reportData['total_long_term_liabilities'], 2, ',', '.') }}</td>
            </tr>

            {{-- EKUITAS --}}
            <tr class="section-header"><td colspan="2">EKUITAS</td></tr>
            @foreach($reportData['equity'] as $account)
            <tr class="account-row">
                <td>{{ $account->name }}</td>
                <td class="text-right">{{ number_format($account->balance, 2, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="account-row">
                <td>Laba Bersih Periode Berjalan</td>
                <td class="text-right">{{ number_format($reportData['net_income'], 2, ',', '.') }}</td>
            </tr>
            <tr class="total-row">
                <td style="padding-left: 5px;">Total Ekuitas</td>
                <td class="text-right">{{ number_format($reportData['total_equity'] + $reportData['net_income'], 2, ',', '.') }}</td>
            </tr>

            <tr class="grand-total-row">
                <td>TOTAL LIABILITAS + EKUITAS</td>
                <td class="text-right">{{ number_format($reportData['total_liabilities'] + $reportData['total_equity'] + $reportData['net_income'], 2, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <div style="float: left;">Neraca : {{ $store['name'] }} | Dicetak oleh: {{ $printedBy }}</div>
        <div style="float: right;">{{ $printedAt }}</div>
    </div>
</body>
</html>
