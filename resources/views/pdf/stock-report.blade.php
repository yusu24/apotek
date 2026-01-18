<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Barang</title>
    <style>
        @page { margin: 20pt 30pt; }
        body { font-family: 'Arial', sans-serif; font-size: 9pt; line-height: 1.3; color: #333; margin: 0; padding: 0; }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; table-layout: fixed; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header-table td { text-align: center; padding: 0; }
        .store-name { font-size: 14pt; font-weight: bold; text-align: center; margin: 0; }
        .store-address { font-size: 9pt; color: #555; text-align: center; margin-top: 3px; }
        .report-title { font-size: 11pt; font-weight: bold; text-align: center; margin: 15px 0; text-transform: uppercase; }
        
        table { width: 100%; border-collapse: collapse; font-size: 8pt; table-layout: fixed; word-wrap: break-word; }
        thead { border-top: 1px solid #000; border-bottom: 1px solid #000; }
        th { padding: 5px; text-align: left; font-weight: bold; text-transform: uppercase; }
        td { padding: 5px; vertical-align: top; }
        
        .no-col { width: 5%; text-align: center; }
        .code-col { width: 15%; }
        .name-col { width: 35%; }
        .unit-col { width: 10%; }
        .stock-col { width: 10%; text-align: right; }
        .price-col { width: 12%; text-align: right; }
        .total-col { width: 13%; text-align: right; }
        .text-right { text-align: right; }
        .footer {
            position: fixed;
            bottom: -30px;
            left: 0;
            right: 0;
            font-size: 8pt;
            color: #777;
            text-align: left;
            border-top: 1px solid #eee;
            padding-top: 5px;
        }
        .footer .right { float: right; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
    <table class="header-table" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <div class="store-name">{{ $store['name'] }}</div>
                <div class="store-address">{{ $store['address'] }}</div>
                <div class="store-address">{{ $store['phone'] }}</div>
            </td>
        </tr>
    </table>

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

    </div>
    <div class="footer">
        Dicetak oleh: {{ $printedBy }}
        <span class="right">Waktu Cetak: {{ $printedAt }}</span>
    </div>
</body>
</html>
