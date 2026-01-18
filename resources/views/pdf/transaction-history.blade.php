<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Riwayat Transaksi</title>
    <style>
        @page { margin: 1.5cm 1cm; }
        body { font-family: sans-serif; font-size: 9px; margin: 0; padding: 0; }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; table-layout: fixed; }
        .header-table td { padding: 0; vertical-align: top; }
        
        table { width: 100%; border-collapse: collapse; font-size: 9px; table-layout: fixed; word-wrap: break-word; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 4px 3px; text-align: left; vertical-align: top; }
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
            text-align: center;
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
            position: fixed;
            bottom: 10pt;
            left: 0;
            right: 0;
            font-size: 8pt;
            color: #888;
            text-align: left;
            border-top: 1px solid #ccc;
            padding-top: 5px;
            height: 30px;
        }
        .footer .right {
            float: right;
        }
    </style>
</head>
<body>
    <table class="header-table" width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td width="5%"></td>
            <td width="90%" align="center">
                <h1 style="margin: 0; font-size: 14px; text-transform: uppercase; text-align: center;">{{ trim($store['name']) }}</h1>
                <div style="margin: 1px 0; font-size: 9px; color: #555; text-align: center;">{{ trim($store['address']) }}</div>
                <div style="margin: 1px 0; font-size: 9px; color: #555; text-align: center;">{{ trim($store['phone']) }}</div>
                <h2 style="margin-top: 10px; font-size: 14px; text-transform: uppercase; text-align: center;">LAPORAN RIWAYAT TRANSAKSI</h2>
                <div style="margin: 1px 0; font-size: 9px; color: #555; text-align: center;">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</div>
            </td>
            <td width="5%"></td>
        </tr>
    </table>

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
        Dicetak oleh: {{ $printedBy }}
        <span class="right">Waktu Cetak: {{ $printedAt }}</span>
    </div>
</body>
</html>
