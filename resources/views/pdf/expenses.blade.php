<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pengeluaran</title>
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
        .filter-info {
            font-size: 10pt;
            margin-top: 3px;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
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
            vertical-align: middle;
            border-bottom: 0.5pt solid #eee;
        }

        .total-row td {
            font-weight: bold;
            background-color: #1e40af;
            color: #ffffff;
            padding: 8px 6px;
        }

    </style>
</head>
<body>
    <div class="report-header">
        <div class="store-name">{{ trim($store['name']) }}</div>
        <div class="report-title">LAPORAN PENGELUARAN (BIAYA)</div>
        @if(isset($periodLabel) && $periodLabel !== 'Semua Periode')
            <div class="filter-info">Untuk Periode {{ $periodLabel }}</div>
        @endif
        @if($search)
            <div class="filter-info">Pencarian Kata Kunci: "{{ $search }}"</div>
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
                <td>{{ $expense->category }}</td>
                <td>{{ $expense->description }}</td>
                <td>{{ $expense->account ? ($expense->account->code . ' - ' . $expense->account->name) : 'Tanpa Akun (Non-Jurnal)' }}</td>
                <td class="text-right">Rp. {{ number_format($expense->amount, 0, ',', '.') }},-</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center italic" style="padding: 20px; color: #777;">Data Pengeluaran Tidak Ditemukan</td>
            </tr>
            @endforelse

            @if($expenses->count() > 0)
            <tr class="total-row">
                <td colspan="4" class="text-right uppercase">Total Pengeluaran</td>
                <td class="text-right">Rp. {{ number_format($totalAmount, 0, ',', '.') }},-</td>
            </tr>
            @endif
        </tbody>
    </table>

</body>
</html>
