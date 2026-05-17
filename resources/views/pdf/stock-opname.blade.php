<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Stok & Opname</title>
    <style>
        @page { 
            size: A4; 
            margin: 15mm 1cm 10mm 1cm; 
        }
        
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: 9pt; 
            color: #333; 
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
            margin-bottom: 20px; 
            text-align: center;
        }
        .store-name {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .report-title { 
            font-size: 12pt; 
            font-weight: bold; 
            color: #34495e;
            margin: 0;
        }
        
        .timestamp {
            position: fixed;
            top: -10mm;
            right: 0;
            font-size: 7pt;
            color: #7f8c8d;
        }

        .meta-info {
            margin-bottom: 15px;
            font-size: 9pt;
            color: #555;
        }

        table { 
            width: 100%; 
            border-collapse: collapse; 
            table-layout: fixed; 
            margin-top: 10px;
        }
        
        /* Table Header Style */
        thead th {
            padding: 8px 5px;
            background-color: #f8f9fa;
            border-top: 1.5pt solid #2c3e50;
            border-bottom: 1.5pt solid #2c3e50;
            font-weight: bold;
            color: #2c3e50;
            text-align: left;
            text-transform: uppercase;
            font-size: 8pt;
        }

        tbody td { 
            padding: 6px 5px; 
            vertical-align: middle; 
            border-bottom: 0.5pt solid #ecf0f1;
            font-size: 8.5pt;
        }

        .status-badge {
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 7.5pt;
            font-weight: bold;
            display: inline-block;
        }
        
        .status-aman { background-color: #e8f5e9; color: #2e7d32; }
        .status-menipis { background-color: #fff8e1; color: #f57f17; }
        .status-habis { background-color: #ffebee; color: #c62828; }

    </style>
</head>
<body>
    <div class="timestamp">
        Waktu Cetak: {{ $printedAt }} | Oleh: {{ $printedBy }}
    </div>

    <div class="report-header">
        <div class="store-name uppercase">{{ $storeName }}</div>
        <div class="report-title">Laporan Stok & Opname</div>
    </div>

    <div class="meta-info">
        @if($filterStatus !== 'all')
        <div>Filter: <span class="font-bold">Stok Menipis</span></div>
        @endif
        @if(!empty($search))
        <div>Pencarian: <span class="font-bold">{{ $search }}</span></div>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%" class="text-center">No</th>
                <th style="width: 15%">Kode/Barcode</th>
                <th style="width: 30%">Nama Produk</th>
                <th style="width: 15%">Kategori</th>
                <th style="width: 10%" class="text-right">Jumlah</th>
                <th style="width: 10%">Satuan</th>
                <th style="width: 15%" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $index => $product)
                @php
                    $stock = $product->total_stock ?? 0;
                    $statusClass = 'status-aman';
                    $statusText = 'Aman';
                    
                    if ($stock <= 0) {
                        $statusClass = 'status-habis';
                        $statusText = 'Habis';
                    } elseif ($stock <= $product->min_stock) {
                        $statusClass = 'status-menipis';
                        $statusText = 'Menipis';
                    }
                @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $product->barcode ?? '-' }}</td>
                <td class="font-bold">{{ $product->name }}</td>
                <td>{{ $product->category->name ?? '-' }}</td>
                <td class="text-right font-bold {{ $stock <= 0 ? 'color: #c62828;' : '' }}">{{ number_format($stock, 0, ',', '.') }}</td>
                <td>{{ $product->unit->name ?? 'pcs' }}</td>
                <td class="text-center">
                    <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center italic" style="padding: 20px; color: #7f8c8d;">Data produk tidak ditemukan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
