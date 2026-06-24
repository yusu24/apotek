<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pengeluaran</title>
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
            line-height: 1.3;
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
            border-bottom: 1.5pt solid #4a7ebb;
            padding-bottom: 10px;
        }
        .store-name {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .store-details {
            font-size: 8pt;
            color: #666;
            margin-bottom: 10px;
        }
        .report-title { 
            font-size: 16pt; 
            font-weight: bold; 
            color: #800000; /* Maroon */
            margin: 5px 0 0 0;
        }
        .filter-info { 
            font-size: 9pt; 
            margin-top: 5px; 
            color: #444;
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
            margin-top: 10px;
        }
        
        .column-headers th {
            padding: 6px 4px;
            border-bottom: 1.5pt solid #4a7ebb;
            font-weight: bold;
            color: #4a7ebb;
            text-align: left;
            font-size: 9pt;
        }

        td { 
            padding: 6px 4px; 
            vertical-align: middle; 
            border-bottom: 0.5pt solid #eee;
            font-size: 8.5pt;
        }

        .total-row td { 
            font-weight: bold; 
            border-top: 1.5pt solid #4a7ebb;
            border-bottom: 1.5pt solid #4a7ebb;
            padding-top: 8px;
            padding-bottom: 8px;
            background-color: #f8fafc;
        }

    </style>
</head>
<body>
    <div class="timestamp">
        Dicetak Oleh: {{ $printedBy }} | Waktu Cetak: {{ $printedAt }}
    </div>

    <div class="report-header">
        <div class="store-name uppercase">{{ trim($store['name']) }}</div>
        <div class="store-details">
            {{ $store['address'] ?? '' }} | Telp: {{ $store['phone'] ?? '' }}
        </div>
        <div class="report-title">Laporan Pengeluaran (Biaya)</div>
        @if(isset($periodLabel) && $periodLabel !== 'Semua Periode')
            <div class="filter-info">Periode: <strong>{{ $periodLabel }}</strong></div>
        @endif
        @if($search)
            <div class="filter-info">Pencarian Kata Kunci: <strong>"{{ $search }}"</strong></div>
        @endif
    </div>

    <table>
        <thead>
            <tr class="column-headers">
                <th style="width: 12%">Tanggal</th>
                <th style="width: 18%">Kategori</th>
                <th style="width: 30%">Deskripsi</th>
                <th style="width: 25%">Metode Pembayaran (Akun)</th>
                <th style="width: 15%; text-align: right;">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @php $totalAmount = 0; @endphp
            @forelse($expenses as $expense)
            @php $totalAmount += $expense->amount; @endphp
            <tr>
                <td>{{ $expense->date ? $expense->date->format('d/m/Y') : '-' }}</td>
                <td class="font-bold">{{ $expense->category }}</td>
                <td>{{ $expense->description }}</td>
                <td>{{ $expense->account ? ($expense->account->code . ' - ' . $expense->account->name) : 'Tanpa Akun (Non-Jurnal)' }}</td>
                <td class="text-right font-bold">Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center italic" style="padding: 20px; color: #777;">Data Pengeluaran Tidak Ditemukan</td>
            </tr>
            @endforelse
            
            @if($expenses->count() > 0)
            <tr class="total-row">
                <td colspan="4" class="text-right uppercase font-bold" style="color: #4a7ebb;">TOTAL PENGELUARAN</td>
                <td class="text-right font-bold" style="color: #800000; font-size: 10pt;">Rp {{ number_format($totalAmount, 0, ',', '.') }}</td>
            </tr>
            @endif
        </tbody>
    </table>

</body>
</html>
