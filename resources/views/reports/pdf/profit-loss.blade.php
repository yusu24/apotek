<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Laba Rugi</title>
    <style>
        body { font-family: sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .company-name { font-size: 24px; font-weight: bold; }
        .report-title { font-size: 18px; margin-top: 5px; }
        .period { font-size: 14px; color: #555; }
        
        .summary-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .summary-table th, .summary-table td { border: 1px solid #ddd; padding: 10px; }
        .summary-table th { background-color: #f2f2f2; text-align: left; }
        .summary-table td.amount { text-align: right; }
        
        .section-header { background-color: #e9e9e9; font-weight: bold; }
        .total-row { font-weight: bold; background-color: #f8f8f8; }
        .net-profit { font-size: 16px; font-weight: bold; background-color: #e6fffa; }
        .net-loss { font-size: 16px; font-weight: bold; background-color: #ffe6e6; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">{{ config('app.name', 'Apotek') }}</div>
        <div class="report-title">Laporan Laba Rugi</div>
        <div class="period">
            Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
        </div>
    </div>

    <table class="summary-table">
        <tbody>
            <!-- REVENUE -->
            <tr class="section-header">
                <td colspan="2">PENDAPATAN</td>
            </tr>
            <tr>
                <td>Pendapatan Bersih (Penjualan - Pajak)</td>
                <td class="amount">Rp {{ number_format($revenue, 0, ',', '.') }}</td>
            </tr>
            <tr class="total-row">
                <td>Total Pendapatan</td>
                <td class="amount">Rp {{ number_format($revenue, 0, ',', '.') }}</td>
            </tr>
            
            <!-- COGS -->
            <tr class="section-header">
                <td colspan="2">HARGA POKOK PENJUALAN (HPP)</td>
            </tr>
            <tr>
                <td>HPP (Metode FIFO)</td>
                <td class="amount">(Rp {{ number_format($cogs, 0, ',', '.') }})</td>
            </tr>
            
            <!-- GROSS PROFIT -->
            <tr class="total-row">
                <td>LABA KOTOR</td>
                <td class="amount">Rp {{ number_format($grossProfit, 0, ',', '.') }}</td>
            </tr>
            
            <!-- EXPENSES -->
            <tr class="section-header">
                <td colspan="2">BEBAN OPERASIONAL</td>
            </tr>
            <tr>
                <td>Total Beban Operasional</td>
                <td class="amount">(Rp {{ number_format($expenses, 0, ',', '.') }})</td>
            </tr>
            
            <!-- NET PROFIT -->
            <tr class="{{ $netProfit >= 0 ? 'net-profit' : 'net-loss' }}">
                <td>LABA BERSIH</td>
                <td class="amount">Rp {{ number_format($netProfit, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
    
    <div style="margin-top: 30px; font-size: 12px; color: #777;">
        Dicetak pada: {{ now()->format('d M Y H:i:s') }} oleh {{ auth()->user()->name }}
    </div>
</body>
</html>
