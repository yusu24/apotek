<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Margin Produk (Standar)</title>
    <style>
        @page { 
            size: A4; 
            margin:  15mm 1cm 10mm 1cm; 
        }
        
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: 9pt; 
            color: #000; 
            margin: 0; 
            padding: 0; 
            line-height: 1.2;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        .italic { font-style: italic; }

        .report-header { 
            margin-bottom: 25px; 
            text-align: center;
        }
        .store-name { 
            font-size: 11pt; 
            margin-bottom: 5px; 
        }
        .report-title { 
            font-size: 16pt; 
            font-weight: bold; 
            color: #800000; /* Maroon */
            margin: 0;
        }
        
        .timestamp {
            position: fixed;
            top: -10mm;
            right: 0;
            font-size: 7pt;
            color: #666;
        }

        table { 
            width: 100%; 
            border-collapse: collapse; 
            table-layout: fixed; 
            margin-top: 10px;
        }
        
        /* Table Header Style - Unified Border */
        .column-headers th {
            padding: 5px 0;
            border-bottom: 1.5pt solid #4a7ebb; /* Blue-ish line */
            font-weight: bold;
            color: #4a7ebb;
            text-align: left;
        }

        td { 
            padding: 5px 0; 
            vertical-align: top; 
            border-bottom: 0.5pt solid #eee;
        }

        .summary-box {
            margin-bottom: 20px;
            width: 100%;
        }
        .summary-box table { border: none; margin-top: 0; }
        .summary-box td { border: none; padding: 2px 0; }
        .summary-label { width: 40%; font-weight: bold; }
        
        .margin-positive { color: #065f46; font-weight: bold; }
        .margin-negative { color: #991b1b; font-weight: bold; }

    </style>
</head>
<body>
    <div class="timestamp">
        Waktu Cetak: {{ $printedAt }}
    </div>

    <div class="report-header">
        <div class="store-name uppercase">{{ trim($store['name']) }}</div>
        <div class="report-title">LAPORAN MARGIN PRODUK</div>
    </div>

    <div class="summary-box">
        <table>
            <tr>
                <td class="summary-label">Total Produk Dianalisis</td>
                <td>: {{ $statistics['total_products'] }}</td>
            </tr>
            <tr>
                <td class="summary-label">Produk dengan Margin Positif</td>
                <td>: {{ $statistics['products_with_positive_margin'] }}</td>
            </tr>
            <tr>
                <td class="summary-label">Rata-rata Margin Persentase</td>
                <td>: {{ number_format($statistics['average_margin_percentage'], 2, ',', '.') }}%</td>
            </tr>
            @if($marginFilter !== 'all')
            <tr>
                <td class="summary-label">Filter Margin</td>
                <td class="uppercase">: {{ $marginFilter }}</td>
            </tr>
            @endif
        </table>
    </div>

    <table>
        <thead>
            <tr class="column-headers">
                <th style="width: 35%">Produk</th>
                <th style="width: 15%; text-align: right;">Beli Terakhir</th>
                <th style="width: 15%; text-align: right;">Harga Jual</th>
                <th style="width: 15%; text-align: right;">Margin (Rp)</th>
                <th style="width: 20%; text-align: right;">Margin (%)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
            <tr>
                <td>
                    <div class="font-bold">{{ $product->name }}</div>
                    <div style="font-size: 7pt; color: #666;">{{ $product->barcode ?? '-' }}</div>
                </td>
                <td class="text-right">{{ number_format($product->last_buy_price, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($product->sell_price, 0, ',', '.') }}</td>
                <td class="text-right {{ $product->margin_amount >= 0 ? 'margin-positive' : 'margin-negative' }}">
                    {{ number_format($product->margin_amount, 0, ',', '.') }}
                </td>
                <td class="text-right {{ $product->margin_percentage >= 0 ? 'margin-positive' : 'margin-negative' }}">
                    {{ number_format($product->margin_percentage, 2, ',', '.') }}%
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center italic" style="padding: 20px; color: #777;">Data Tidak Ditemukan</td>
            </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
