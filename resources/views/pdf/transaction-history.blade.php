<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Riwayat Transaksi (Standar)</title>
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


    </style>
</head>
<body>
    <div class="report-header">
        <div class="store-name">{{ trim($store['name']) }}</div>
        <div class="report-title">LAPORAN RIWAYAT TRANSAKSI</div>
        <div class="period-info">
            Untuk Periode {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
        </div>
    </div>

    <table>
        <thead>
            <tr class="column-headers">
                <th style="width: 5%">No</th>
                <th style="width: 18%">Tanggal</th>
                <th style="width: 15%">Kode</th>
                <th style="width: 37%">Nama Produk</th>
                <th style="width: 15%">Tipe</th>
                <th style="width: 10%; text-align: right;">Qty</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $item->product->barcode ?? '-' }}</td>
                    <td>{{ $item->product->name ?? '-' }}</td>
                    <td class="font-bold">
                        @php
                            $labels = [
                                'sale' => 'Penjualan',
                                'in' => 'Masuk',
                                'adjustment' => 'Opname',
                                'return' => 'Retur Jual',
                                'return-supplier' => 'Retur Beli',
                            ];
                        @endphp
                        {{ $labels[$item->type] ?? $item->type }}
                    </td>
                    <td class="text-right font-bold">
                        {{ $item->quantity > 0 ? '+' : '' }}{{ number_format($item->quantity, 0) }}
                    </td>
                </tr>
            @endforeach
            
            @if(count($transactions) === 0)
                <tr>
                    <td colspan="6" class="text-center italic" style="padding: 20px; color: #777;">Tidak ada data transaksi.</td>
                </tr>
            @endif
        </tbody>
    </table>

</body>
</html>
