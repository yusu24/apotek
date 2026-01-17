<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Riwayat Transaksi</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 9px;
            margin: 0.5cm;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            font-size: 14px;
            text-transform: uppercase;
        }
        .header p {
            margin: 1px 0;
            font-size: 9px;
            color: #555;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
            table-layout: fixed;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 4px 3px;
            text-align: left;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .badge {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 7px;
        }
        .bg-sale { color: #004085; }
        .bg-in { color: #155724; }
        .bg-adjustment { color: #856404; }
        .bg-return { color: #721c24; }
        .footer {
            margin-top: 15px;
            font-size: 7px;
            text-align: right;
            color: #888;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $store['name'] }}</h1>
        <p>{{ $store['address'] }}</p>
        <p>{{ $store['phone'] }}</p>
        <h2 style="margin-top: 10px; font-size: 14px;">LAPORAN RIWAYAT TRANSAKSI</h2>
        <p>Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</p>
    </div>

    <table style="margin-bottom: 10px;">
        <thead>
            <tr>
                <th width="15">No</th>
                <th width="85">Tanggal</th>
                <th width="80">Kode</th>
                <th>Nama Produk</th>
                <th width="65">Tipe</th>
                <th width="30" class="text-right">Qty</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $item->product->barcode ?? '-' }}</td>
                    <td>{{ $item->product->name ?? '-' }}</td>
                    <td>
                        @php
                            $labels = [
                                'sale' => 'Penjualan',
                                'in' => 'Masuk',
                                'adjustment' => 'Opname',
                                'return' => 'Retur Jual',
                                'return-supplier' => 'Retur Beli',
                            ];
                            $classes = [
                                'sale' => 'bg-sale',
                                'in' => 'bg-in',
                                'adjustment' => 'bg-adjustment',
                                'return' => 'bg-return',
                                'return-supplier' => 'bg-return',
                            ];
                        @endphp
                        <span class="badge {{ $classes[$item->type] ?? '' }}">
                            {{ $labels[$item->type] ?? ucfirst($item->type) }}
                        </span>
                    </td>
                    <td class="text-right">
                        {{ $item->quantity > 0 ? '+' : '' }}{{ number_format($item->quantity, 0) }}
                    </td>
                </tr>
            @endforeach
            
            @if(count($transactions) === 0)
                <tr>
                    <td colspan="6" class="text-center" style="padding: 20px;">Tidak ada data transaksi.</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        Dicetak oleh: {{ $printedBy }} pada {{ $printedAt }}
    </div>
</body>
</html>
