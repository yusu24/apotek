<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan (Standar)</title>
    <style>
        @page { margin: 1cm 1.2cm; }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12pt;
            line-height: 1.4;
            color: #1a1a1a;
            margin: 0;
            padding: 0;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        .italic { font-style: italic; }

        .report-header {
            margin-bottom: 16px;
            text-align: center;
        }
        .store-name {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .report-title {
            font-size: 14pt;
            font-weight: bold;
            letter-spacing: 1px;
            color: #1e40af;
            margin-top: 4px;
        }
        .period-info {
            font-size: 10pt;
            margin-top: 3px;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 16px;
            font-size: 8pt;
        }

        .column-headers th {
            padding: 6px;
            background-color: #1e40af;
            color: #ffffff;
            font-weight: bold;
            text-align: left;
        }

        td {
            padding: 4px 6px;
            vertical-align: top;
            border-bottom: 0.5pt solid #eee;
        }

        .total-row td {
            font-weight: bold;
            background-color: #f3f4f6;
            border-top: 1pt solid #999;
            padding-top: 6px;
            padding-bottom: 6px;
        }
        .subtotal-row td {
            font-weight: bold;
            background-color: #dbeafe;
            border-top: 1pt solid #999;
            border-bottom: 1pt solid #999;
            padding-top: 6px;
            padding-bottom: 6px;
        }

        .grand-total-label {
            font-weight: bold;
            text-transform: uppercase;
            background-color: #1e40af;
            color: #ffffff;
            padding: 8px 6px;
        }
        .grand-total-value {
            font-weight: bold;
            text-align: right;
            background-color: #1e40af;
            color: #ffffff;
            padding: 8px 6px;
        }

    </style>
</head>
<body>
    <div class="report-header">
        <div class="store-name">{{ trim($store['name']) }}</div>
        <div class="report-title">LAPORAN PENJUALAN</div>
        <div class="period-info">
            Untuk Periode {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
        </div>
        @if($paymentMethod !== 'all')
            <div class="period-info font-bold">Metode Pembayaran: {{ strtoupper($paymentMethod) }}</div>
        @endif
    </div>

    <table>
        <thead>
            <tr class="column-headers">
                <th style="width: 30%">No. Invois</th>
                <th style="width: 20%">Tanggal</th>
                <th style="width: 15%">Kasir</th>
                <th style="width: 10%">Metode</th>
                <th style="width: 25%; text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sales as $sale)
            <tr>
                <td class="font-bold">{{ $sale->invoice_no }}</td>
                <td>{{ \Carbon\Carbon::parse($sale->date)->format('d/m/Y H:i') }}</td>
                <td>{{ $sale->user->name }}</td>
                <td class="uppercase">{{ $sale->payment_method }}</td>
                <td class="text-right">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center italic" style="padding: 20px; color: #777;">Data Tidak Ditemukan</td>
            </tr>
            @endforelse
            
            @if($sales->count() > 0)
            <tr class="total-row">
                <td colspan="4" class="text-right uppercase">Total Penjualan Kotor</td>
                <td class="text-right">Rp {{ number_format($stats['total_sales'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="4" class="text-right italic" style="color: #666;">(-) Total PPN (Pajak)</td>
                <td class="text-right italic" style="color: #666;">Rp {{ number_format($stats['total_tax'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="4" class="text-right italic" style="color: #666;">(-) Total Pembulatan</td>
                <td class="text-right italic" style="color: #666;">Rp {{ number_format($stats['total_rounding'], 0, ',', '.') }}</td>
            </tr>
            <tr class="subtotal-row">
                <td colspan="4" class="text-right uppercase">Subtotal Bersih (DPP)</td>
                <td class="text-right">Rp {{ number_format($stats['total_dpp'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="4" class="text-right font-bold" style="color: #b91c1c;">(-) Total Retur</td>
                <td class="text-right font-bold" style="color: #b91c1c;">Rp {{ number_format($stats['total_returns'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="4" class="grand-total-label text-right">TOTAL PENJUALAN BERSIH</td>
                <td class="grand-total-value">Rp {{ number_format($stats['net_sales'], 0, ',', '.') }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    </body>
</html>
