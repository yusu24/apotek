<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Margin Produk (Standar)</title>
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
            font-size: 10pt;
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

        .summary-box {
            margin-bottom: 16px;
            width: 100%;
        }
        .summary-box table { border: none; margin-top: 0; font-size: 10pt; }
        .summary-box td { border: none; padding: 2px 6px; }
        .summary-label { width: 40%; font-weight: bold; }

        .margin-positive { color: #065f46; font-weight: bold; }
        .margin-negative { color: #991b1b; font-weight: bold; }

    </style>
</head>
<body>
    <div class="report-header">
        <div class="store-name">{{ trim($storeName) }}</div>
        <div class="report-title">LAPORAN {{ $reportMode === 'realized' ? 'REALISASI ' : '' }}MARGIN PRODUK</div>
        @if($reportMode === 'realized')
            <div class="period-info">Untuk Periode {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</div>
        @endif
    </div>

    <div class="summary-box">
        <table style="width: 60%">
            <tr>
                <td class="summary-label">Tipe Laporan</td>
                <td>: {{ $reportMode === 'realized' ? 'Margin Realisasi (Penjualan)' : 'Potensi Margin (Stok)' }}</td>
            </tr>
            <tr>
                <td class="summary-label">Total Produk Dianalisis</td>
                <td>: {{ number_format($stats['total_products']) }}</td>
            </tr>
            <tr>
                <td class="summary-label">Total {{ $reportMode === 'realized' ? 'Keuntungan' : 'Nilai Margin' }}</td>
                <td class="font-bold">: Rp. {{ number_format($stats['total_margin_value'], 0, ',', '.') }},-</td>
            </tr>
            <tr>
                <td class="summary-label">Rata-rata Margin Persentase</td>
                <td>: {{ number_format($stats['average_margin_percentage'], 2, ',', '.') }}%</td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr class="column-headers">
                <th style="width: 30%">Produk</th>
                @if($reportMode === 'realized')
                    <th style="width: 10%; text-align: center;">Qty</th>
                @endif
                <th style="width: 17.5%; text-align: right;">{{ $reportMode === 'realized' ? 'HPP Rerata' : 'Beli Terakhir' }}</th>
                <th style="width: 17.5%; text-align: right;">{{ $reportMode === 'realized' ? 'Jual Rerata' : 'Harga Jual' }}</th>
                <th style="width: 15%; text-align: right;">Margin (Rp)</th>
                <th style="width: 10%; text-align: right;">%</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
            <tr>
                <td>
                    <div class="font-bold">{{ $product->name }}</div>
                    <div style="font-size: 7pt; color: #666;">{{ $product->barcode ?? '-' }}</div>
                </td>
                @if($reportMode === 'realized')
                    <td class="text-center">{{ number_format($product->total_sold) }}</td>
                @endif
                <td class="text-right">{{ number_format($product->avg_buy_price ?? $product->last_buy_price, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($product->avg_sell_price ?? $product->sell_price, 0, ',', '.') }}</td>
                <td class="text-right {{ $product->margin_amount >= 0 ? 'margin-positive' : 'margin-negative' }}">
                    {{ number_format($product->margin_amount, 0, ',', '.') }}
                </td>
                <td class="text-right {{ $product->margin_percentage >= 0 ? 'margin-positive' : 'margin-negative' }}">
                    {{ number_format($product->margin_percentage, 1, ',', '.') }}%
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="{{ $reportMode === 'realized' ? '6' : '5' }}" class="text-center italic" style="padding: 20px; color: #777;">Data Tidak Ditemukan</td>
            </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
