<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Stok Barang (Standar)</title>
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
