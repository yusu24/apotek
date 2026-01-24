<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Stok Barang (Standar)</title>
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
        .period-info { 
            font-size: 10pt; 
            margin-top: 5px; 
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

        .grand-total-label { 
            font-weight: bold; 
            text-transform: uppercase;
            padding-top: 10px;
        }
        .grand-total-value { 
            font-weight: bold; 
            border-top: 0.5pt solid #000; 
            border-bottom: 3pt double #000;
            text-align: right;
            padding-top: 2px;
        }

    </style>
</head>
<body>
    <div class="timestamp">
        Waktu Cetak: {{ $printedAt }}
    </div>

    <div class="report-header">
        <div class="store-name uppercase">{{ trim($store['name']) }}</div>
        <div class="report-title">LAPORAN STOK BARANG</div>
    </div>

    <table>
        <thead>
            <tr class="column-headers">
                <th style="width: 5%">No</th>
                <th style="width: 15%">Kode Barang</th>
                <th style="width: 35%">Nama Barang</th>
                <th style="width: 10%">Satuan</th>
                <th style="width: 10%; text-align: right;">Stok</th>
                <th style="width: 12%; text-align: right;">Harga Beli</th>
                <th style="width: 13%; text-align: right;">Saldo</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $index => $product)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $product->barcode ?? $product->id }}</td>
                <td>{{ $product->name }}</td>
                <td>{{ $product->unit->name ?? '-' }}</td>
                <td class="text-right">{{ number_format($product->total_stock, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($product->avg_buy_price, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($product->total_value, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center italic" style="padding: 20px; color: #777;">Tidak ada data barang.</td>
            </tr>
            @endforelse
            
            @if($products->count() > 0)
            <tr>
                <td colspan="6" class="grand-total-label text-right">TOTAL NILAI PERSEDIAAN</td>
                <td class="grand-total-value">{{ number_format($products->sum('total_value'), 0, ',', '.') }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    </body>
</html>
