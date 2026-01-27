<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Riwayat Transaksi (Standar)</title>
    <style>
        @page { 
            size: A4; 
            margin: 15mm 1cm 10mm 1cm; 
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

        .badge { font-weight: bold; text-transform: uppercase; font-size: 8pt; }
        
    </style>
</head>
<body>
    <div class="timestamp">
        Waktu Cetak: {{ $printedAt }}
    </div>

    <div class="report-header">
        <div class="store-name uppercase">{{ trim($store['name']) }}</div>
        <div class="report-title">LAPORAN RIWAYAT TRANSAKSI</div>
        <div class="period-info">
            Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
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
                    <td class="font-bold uppercase" style="font-size: 8pt;">
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
