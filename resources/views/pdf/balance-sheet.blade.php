<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Neraca (Balance Sheet)</title>
    <style>
        @page { margin: 20px 40px 60px 40px; }
        body { font-family: sans-serif; font-size: 10pt; color: #333; }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; text-align: center; }
        .header-table td { text-align: center; }
        .store-name { font-size: 16pt; font-weight: bold; text-transform: uppercase; margin-bottom: 5px; text-align: center; width: 100%; }
        .report-title { font-size: 12pt; font-weight: bold; text-transform: uppercase; color: #555; text-align: center; width: 100%; }
        .period { font-size: 10pt; margin-top: 5px; color: #666; text-align: center; width: 100%; }
        
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
        
        .subsection-header {
            font-weight: bold;
            padding-top: 8px;
            padding-left: 15px;
            color: #444;
        }

        .account-row td { padding-left: 25px; border-bottom: 1px solid #eee; }
        
        .total-row td { 
            font-weight: bold; 
            background-color: #f0f9ff; 
            color: #000;
            border-top: 1px solid #cbd5e1;
            padding-top: 8px;
            padding-bottom: 8px;
        }
        
        .grand-total-row td {
            font-weight: bold;
            font-size: 11pt;
            background-color: #333;
            color: white;
            padding: 10px;
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
            bottom: -30px;
            width: 100%;
            font-size: 8pt;
            color: #999;
            text-align: left;
            border-top: 1px solid #eee;
            padding-top: 5px;
        }
        .footer .right {
            float: right;
        }
    </style>
</head>
<body>
    <div style="width: 100%; text-align: center; margin-bottom: 30px;">
        <table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin: 0 auto;">
            <tr>
                <td align="center" style="text-align: center;">
                    <div style="font-size: 16pt; font-weight: bold; text-transform: uppercase;">{{ $store['name'] }}</div>
                    <div style="font-size: 12pt; font-weight: bold; text-transform: uppercase; color: #555; margin-top: 5px;">NERACA (BALANCE SHEET)</div>
                    <div style="font-size: 10pt; color: #666; margin-top: 5px;">
                        Periode: {{ \Carbon\Carbon::parse($startDate)->format('d F Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}
                    </div>
                    <div style="font-size: 9pt; margin-top: 5px; font-style: italic; color: #666;">(dalam Mata Uang Rupiah IDR)</div>
                </td>
            </tr>
        </table>
    </div>

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
                <td class="text-right">{{ format_accounting($account->balance) }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td>Total Aset Lancar</td>
                <td class="text-right">{{ format_accounting($reportData['total_current_assets']) }}</td>
            </tr>

            <tr class="subsection-header"><td colspan="2">Aset Tetap</td></tr>
            @foreach($reportData['fixed_assets'] as $account)
            <tr class="account-row">
                <td>{{ $account->name }}</td>
                <td class="text-right">{{ format_accounting($account->balance) }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td>Total Aset Tetap</td>
                <td class="text-right">{{ format_accounting($reportData['total_fixed_assets']) }}</td>
            </tr>

            <tr class="grand-total-row">
                <td>TOTAL ASET</td>
                <td class="text-right">{{ format_accounting($reportData['total_assets']) }}</td>
            </tr>

            {{-- SPACING --}}
            <tr><td colspan="2" style="height: 20px;"></td></tr>

            {{-- LIABILITAS --}}
            <tr class="section-header"><td colspan="2">LIABILITAS</td></tr>
            
            <tr class="subsection-header"><td colspan="2">Liabilitas Lancar</td></tr>
            @foreach($reportData['current_liabilities'] as $account)
            <tr class="account-row">
                <td>{{ $account->name }}</td>
                <td class="text-right">{{ format_accounting($account->balance) }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td>Total Liabilitas Lancar</td>
                <td class="text-right">{{ format_accounting($reportData['total_current_liabilities']) }}</td>
            </tr>

            <tr class="subsection-header"><td colspan="2">Liabilitas Jangka Panjang</td></tr>
            @foreach($reportData['long_term_liabilities'] as $account)
            <tr class="account-row">
                <td>{{ $account->name }}</td>
                <td class="text-right">{{ format_accounting($account->balance) }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td>Total Liabilitas Jangka Panjang</td>
                <td class="text-right">{{ format_accounting($reportData['total_long_term_liabilities']) }}</td>
            </tr>

            {{-- EKUITAS --}}
            <tr class="section-header"><td colspan="2">EKUITAS</td></tr>
            @foreach($reportData['equity'] as $account)
            <tr class="account-row">
                <td>{{ $account->name }}</td>
                <td class="text-right">{{ format_accounting($account->balance) }}</td>
            </tr>
            @endforeach
            <tr class="account-row">
                <td>Laba Bersih Periode Berjalan</td>
                <td class="text-right">{{ format_accounting($reportData['net_income']) }}</td>
            </tr>
            <tr class="total-row">
                <td>Total Ekuitas</td>
                <td class="text-right">{{ format_accounting($reportData['total_equity'] + $reportData['net_income']) }}</td>
            </tr>

            <tr class="grand-total-row">
                <td>TOTAL LIABILITAS + EKUITAS</td>
                <td class="text-right">{{ format_accounting($reportData['total_liabilities'] + $reportData['total_equity'] + $reportData['net_income']) }}</td>
            </tr>
        </tbody>
    </table>

    @php
    function format_accounting($number) {
        if ($number < 0) {
            return '( ' . number_format(abs($number), 0, ',', '.') . ' )';
        }
        return number_format($number, 0, ',', '.');
    }
    @endphp
    <div class="footer">
        Dicetak oleh: {{ $printedBy }}
        <span class="right">Waktu Cetak: {{ $printedAt }}</span>
    </div>
</body>
</html>
