<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan (Standar)</title>
    <style>
        @page { 
            size: A4; 
            margin:  15mm 1cm 10mm 1cm; 
        }
        
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: 8pt; 
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

        .total-row td { 
            font-weight: bold; 
            border-top: 1pt solid #000;
            padding-top: 8px;
            padding-bottom: 8px;
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
        <div class="report-title">LAPORAN PENJUALAN</div>
        <div class="period-info">
            Periode: {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}
        </div>
        @if($paymentMethod !== 'all')
            <div class="period-info font-bold">Metode Pembayaran: {{ strtoupper($paymentMethod) }}</div>
        @endif
    </div>

    <table>
        <thead>
            <tr class="column-headers">
                <th style="width: 30%">No. Invois</th>
                <th style="width: 20%">Tanggal</th>
                <th style="width: 15%">Kasir</th>
                <th style="width: 10%">Metode</th>
                <th style="width: 25%; text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sales as $sale)
            <tr>
                <td class="font-bold">{{ $sale->invoice_no }}</td>
                <td>{{ \Carbon\Carbon::parse($sale->date)->format('d/m/Y H:i') }}</td>
                <td>{{ $sale->user->name }}</td>
                <td class="uppercase">{{ $sale->payment_method }}</td>
                <td class="text-right">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center italic" style="padding: 20px; color: #777;">Data Tidak Ditemukan</td>
            </tr>
            @endforelse
            
            @if($sales->count() > 0)
            <tr>
                <td colspan="4" class="grand-total-label text-right">TOTAL PENJUALAN ({{ $stats['transaction_count'] }} Transaksi)</td>
                <td class="grand-total-value">Rp {{ number_format($stats['total_sales'], 0, ',', '.') }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    </body>
</html>
