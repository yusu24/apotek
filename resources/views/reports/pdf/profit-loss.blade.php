<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta charset="UTF-8">
    <title>Laporan Laba Rugi - {{ $storeName }}</title>
    <style>
        @page {
            margin: 1.5cm;
        }
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: 11pt;
            line-height: 1.4;
            color: #1a1a1a;
        }
        .header { 
            text-align: center; 
            margin-bottom: 30px; 
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }
        .store-name { 
            font-size: 20pt; 
            font-weight: bold; 
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .store-address {
            font-size: 10pt;
            font-style: italic;
        }
        .report-title { 
            font-size: 16pt; 
            font-weight: bold; 
            margin-top: 20px;
            text-align: center;
        }
        .report-period {
            font-size: 11pt;
            text-align: center;
            margin-bottom: 30px;
        }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        
        .p-l-table td { padding: 8px 5px; }
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
            margin-top: 50px; 
            font-size: 9pt; 
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
    <div class="header">
        <div class="store-name">{{ $storeName }}</div>
        <div class="store-address">{{ $storeAddress }}</div>
    </div>

    <div class="report-title">LAPORAN LABA RUGI</div>
    <div class="report-period">
        Periode: {{ \Carbon\Carbon::parse($startDate)->format('d F Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}
    </div>

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

    <div class="footer">
        * Penjualan tidak termasuk PPN ({{ number_format($totalTax, 0, ',', '.') }})<br>
        Dicetak pada: {{ now()->format('d/m/Y H:i:s') }} oleh {{ auth()->user()->name }}
    </div>

    <div class="signature-box">
        <p>Dicetak Oleh,</p>
        <div class="signature-line"></div>
        <p>{{ auth()->user()->name }}</p>
    </div>
</body>
</html>
