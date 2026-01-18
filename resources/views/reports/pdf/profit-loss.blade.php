<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta charset="UTF-8">
    <title>Laporan Laba Rugi - {{ $storeName }}</title>
    <style>
        @page { margin: 20pt 30pt; }
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: 11pt;
            line-height: 1.4;
            color: #1a1a1a;
            margin: 0;
            padding: 0;
        }
        .header-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 30px; 
            table-layout: fixed;
        }
        .header-table td { 
            text-align: center; 
            padding: 0;
        }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; table-layout: fixed; word-wrap: break-word; }
        
        .p-l-table td { padding: 8px 5px; vertical-align: top; }
        .p-l-table .label { width: 60%; }
        .p-l-table .amount { width: 40%; text-align: right; }
        
        .section-title { 
            font-weight: bold; 
            text-decoration: underline;
            padding-top: 15px !important;
        }
        .sub-item { padding-left: 20px !important; }
        .total-item { font-weight: bold; border-top: 1px solid #000; }
        .grand-total { 
            font-size: 13pt; 
            font-weight: bold; 
            border-top: 2px solid #000; 
            border-bottom: 2px solid #000;
            padding: 10px 5px !important;
        }
        
        .footer {
            position: fixed;
            bottom: -30px;
            left: 0;
            right: 0;
            font-size: 8pt;
            color: #666;
            text-align: left;
            border-top: 1px solid #000;
            padding-top: 5px;
        }
        .footer .right {
            float: right;
        }
        .signature-box {
            float: right;
            width: 200px;
            text-align: center;
            margin-top: 40px;
        }
        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #000;
        }
    </style>
</head>
<body>
    <table class="header-table" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <div style="font-size: 20pt; font-weight: bold; text-transform: uppercase; text-align: center;">{{ $storeName }}</div>
                <div style="font-size: 10pt; font-style: italic; text-align: center;">{{ $storeAddress }}</div>
                <div style="font-size: 16pt; font-weight: bold; margin-top: 15px; text-align: center;">LAPORAN LABA RUGI</div>
                <div style="font-size: 11pt; margin-top: 5px; text-align: center;">
                    Periode: {{ \Carbon\Carbon::parse($startDate)->format('d F Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}
                </div>
            </td>
        </tr>
    </table>

    <table class="p-l-table">
        <tbody>
            <!-- PENDAPATAN -->
            <tr>
                <td class="label section-title">PENDAPATAN</td>
                <td class="amount"></td>
            </tr>
            <tr>
                <td class="label sub-item">Penjualan Kotor</td>
                <td class="amount">Rp {{ number_format($revenue + $totalDiscount, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label sub-item">Potongan Penjualan (Diskon)</td>
                <td class="amount">({{ number_format($totalDiscount, 0, ',', '.') }})</td>
            </tr>
            <tr class="total-item">
                <td class="label">PENJUALAN BERSIH</td>
                <td class="amount">Rp {{ number_format($revenue, 0, ',', '.') }}</td>
            </tr>

            <!-- HPP -->
            <tr>
                <td class="label section-title">HARGA POKOK PENJUALAN</td>
                <td class="amount"></td>
            </tr>
            <tr>
                <td class="label sub-item">Total Harga Pokok Penjualan (HPP)</td>
                <td class="amount">Rp {{ number_format($cogs, 0, ',', '.') }}</td>
            </tr>
            <tr class="total-item">
                <td class="label">TOTAL HPP</td>
                <td class="amount">Rp {{ number_format($cogs, 0, ',', '.') }}</td>
            </tr>

            <!-- LABA KOTOR -->
            <tr class="total-item" style="height: 20px;"><td></td><td></td></tr>
            <tr class="total-item">
                <td class="label">LABA KOTOR (GROSS PROFIT)</td>
                <td class="amount">Rp {{ number_format($grossProfit, 0, ',', '.') }}</td>
            </tr>

            <!-- BIAYA OPERASIONAL -->
            <tr>
                <td class="label section-title">BIAYA OPERASIONAL / PENGELUARAN</td>
                <td class="amount"></td>
            </tr>
            @forelse($expenseDetails->groupBy('category') as $category => $items)
            <tr>
                <td class="label sub-item">{{ strtoupper($category) }}</td>
                <td class="amount">Rp {{ number_format($items->sum('amount'), 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td class="label sub-item">- Tidak ada pengeluaran -</td>
                <td class="amount">Rp 0</td>
            </tr>
            @endforelse
            <tr class="total-item">
                <td class="label">TOTAL BIAYA OPERASIONAL</td>
                <td class="amount">Rp {{ number_format($expenses, 0, ',', '.') }}</td>
            </tr>

            <!-- LABA BERSIH -->
            <tr style="height: 30px;"><td></td><td></td></tr>
            <tr class="grand-total">
                <td class="label">LABA BERSIH (NET PROFIT)</td>
                <td class="amount">Rp {{ number_format($netProfit, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="signature-box">
        <p>Dicetak Oleh,</p>
        <div class="signature-line"></div>
        <p>{{ $printedBy }}</p>
    </div>
    <div class="footer">
        Dicetak oleh: {{ $printedBy }}
        <span class="right">Waktu Cetak: {{ $printedAt }}</span>
    </div>
</body>
</html>
