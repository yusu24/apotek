<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Barang</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 9pt;
            line-height: 1.3;
            color: #333;
        }

        .container {
            padding: 20px;
        }

        /* Header Styles */
        .header {
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
            display: table;
            width: 100%;
        }

        .logo-placeholder {
            display: table-cell;
            width: 80px;
            vertical-align: top;
            padding-right: 15px;
        }

        .logo-box {
            width: 70px;
            height: 70px;
            border: 1px dashed #999;
            background-color: #f5f5f5;
            display: flex; /* Flex doesn't work well in DomPDF sometimes, but trying strict styling */
            align-items: center;
            justify-content: center;
            text-align: center;
            font-size: 8pt;
            color: #666;
            line-height: 70px; /* For vertical alignment fallback */
        }

        .store-info {
            display: table-cell;
            vertical-align: top;
        }

        .store-name {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .store-address {
            font-size: 9pt;
            color: #555;
        }

        .report-title {
            font-size: 11pt;
            font-weight: bold;
            margin-bottom: 15px;
            text-transform: uppercase;
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
        }

        thead {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
        }

        th {
            padding: 5px;
            text-align: left;
            font-weight: bold;
            text-transform: uppercase;
        }

        td {
            padding: 5px;
            vertical-align: top;
        }

        .no-col { width: 5%; text-align: center; }
        .code-col { width: 15%; }
        .name-col { width: 35%; }
        .unit-col { width: 10%; }
        .stock-col { width: 10%; text-align: right; }
        .price-col { width: 12%; text-align: right; }
        .total-col { width: 13%; text-align: right; }

        .text-right { text-align: right; }
        .text-center { text-align: center; }

        /* Striped rows usually not in this specific style, but cleaner to verify rows */
        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .footer {
            margin-top: 30px;
            font-size: 8pt;
            text-align: right;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo-placeholder">
                <div class="logo-box">Logo</div>
            </div>
            <div class="store-info">
                <div class="store-name">{{ $store['name'] }}</div>
                <div class="store-address">{{ $store['address'] }}</div>
                <div class="store-address">{{ $store['phone'] }}</div>
            </div>
        </div>

        <div class="report-title">LAPORAN BARANG</div>

        <table>
            <thead>
                <tr>
                    <th class="no-col">NO.</th>
                    <th class="code-col">KODE BARANG</th>
                    <th class="name-col">NAMA BARANG</th>
                    <th class="unit-col">SATUAN</th>
                    <th class="stock-col">STOK</th>
                    <th class="price-col">HARGA BELI</th>
                    <th class="total-col">SALDO</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $index => $product)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $product->barcode ?? $product->id }}</td> <!-- Using barcode as code, or ID fallback -->
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->unit->name ?? '-' }}</td>
                    <td class="text-right">{{ number_format($product->total_stock, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($product->avg_buy_price, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($product->total_value, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 20px;">Tidak ada data barang.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Grand Total (Optional, but usually helpful) -->
        @if($products->count() > 0)
        <div style="margin-top: 10px; border-top: 1px solid #000; padding-top: 5px; text-align: right; font-weight: bold; font-size: 9pt;">
            Total Nilai Persediaan: {{ number_format($products->sum('total_value'), 0, ',', '.') }}
        </div>
        @endif

        <div class="footer">
            Dicetak pada: {{ $printedAt }} oleh {{ $printedBy }}
        </div>
    </div>
</body>
</html>
