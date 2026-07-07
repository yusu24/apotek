<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Stok & Opname</title>
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
            color: #000000;
            margin-top: 4px;
        }

        .meta-info {
            margin-bottom: 12px;
            font-size: 10pt;
            color: #333;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 16px;
            font-size: 8pt;
        }

        /* Table Header Style */
        thead th {
            padding: 6px;
            background-color: #1e40af;
            color: #ffffff;
            font-weight: bold;
            text-align: left;
        }

        tbody td {
            padding: 4px 6px;
            vertical-align: middle;
            border-bottom: 0.5pt solid #eee;
        }

        .status-badge {
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 7pt;
            font-weight: bold;
            display: inline-block;
        }

        .status-aman { background-color: #e8f5e9; color: #2e7d32; }
        .status-menipis { background-color: #fff8e1; color: #f57f17; }
        .status-habis { background-color: #ffebee; color: #c62828; }

    </style>
</head>
<body>
    <div class="report-header">
        <div class="store-name">{{ $storeName }}</div>
        <div class="report-title">LAPORAN STOK & OPNAME</div>
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
