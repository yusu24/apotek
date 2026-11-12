<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Selisih Stok Opname</title>
    <style>
        @page { margin: 1cm 1.2cm; }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10pt;
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
            margin-bottom: 14px;
            text-align: center;
        }
        .store-name {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .report-title {
            font-size: 13pt;
            font-weight: bold;
            letter-spacing: 1px;
            color: #000000;
            margin-top: 3px;
        }
        .period-info {
            font-size: 9pt;
            margin-top: 2px;
            color: #333;
        }

        .summary-box {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            font-size: 8.5pt;
        }
        .summary-box td {
            border: 1pt solid #cbd5e1;
            padding: 8px 10px;
            width: 25%;
            vertical-align: top;
        }
        .summary-title {
            font-size: 7.5pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .summary-value {
            font-size: 11pt;
            font-weight: bold;
        }
        .summary-sub {
            font-size: 7.5pt;
            color: #475569;
            margin-top: 2px;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 10px;
            font-size: 8pt;
        }

        table.data-table thead th {
            padding: 6px;
            background-color: #1e40af;
            color: #ffffff;
            font-weight: bold;
            text-align: left;
        }

        table.data-table tbody td {
            padding: 4px 6px;
            vertical-align: top;
            border-bottom: 0.5pt solid #e2e8f0;
        }

        .total-row td {
            font-weight: bold;
            background-color: #f8fafc;
            border-top: 0.8pt solid #94a3b8;
            padding: 5px 6px;
        }

        .grand-total-row td {
            font-weight: bold;
            background-color: #dbeafe;
            border-top: 1pt solid #1e40af;
            border-bottom: 1pt solid #1e40af;
            padding: 6px;
        }

        .color-minus { color: #c62828; }
        .color-plus { color: #2e7d32; }
    </style>
</head>
<body>
    <div class="report-header">
        <div class="store-name">{{ trim($store['name']) }}</div>
        <div class="report-title">LAPORAN SELISIH STOK OPNAME</div>
        <div class="period-info">
            Periode: {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d/m/Y') : 'Awal' }} - {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d/m/Y') : 'Akhir' }}
        </div>
        @if($varianceType !== 'all')
            <div class="period-info font-bold">
                Filter Jenis: {{ $varianceType === 'minus' ? 'Stok Minus (-)' : 'Stok Plus (+)' }}
            </div>
        @endif
    </div>

    <!-- Summary Cards in PDF -->
    <table class="summary-box">
        <tr>
            <td style="background-color: #fff1f2;">
                <div class="summary-title color-minus">1. Stok Minus (Fisik &lt; Sistem)</div>
                <div class="summary-value color-minus">-{{ number_format($stats['minus_qty'], 0, ',', '.') }} Unit</div>
                <div class="summary-sub">Nilai: -Rp {{ number_format($stats['minus_value'], 0, ',', '.') }} ({{ $stats['minus_items'] }} produk)</div>
            </td>
            <td style="background-color: #ecfdf5;">
                <div class="summary-title color-plus">2. Stok Plus (Fisik &gt; Sistem)</div>
                <div class="summary-value color-plus">+{{ number_format($stats['plus_qty'], 0, ',', '.') }} Unit</div>
                <div class="summary-sub">Nilai: +Rp {{ number_format($stats['plus_value'], 0, ',', '.') }} ({{ $stats['plus_items'] }} produk)</div>
            </td>
            <td style="background-color: #eff6ff;">
                <div class="summary-title" style="color: #1e40af;">3A. Total Selisih Bersih (Nett)</div>
                <div class="summary-value {{ $stats['net_qty'] < 0 ? 'color-minus' : ($stats['net_qty'] > 0 ? 'color-plus' : '') }}">
                    {{ $stats['net_qty'] > 0 ? '+' : '' }}{{ number_format($stats['net_qty'], 0, ',', '.') }} Unit
                </div>
                <div class="summary-sub">
                    Nilai: {{ $stats['net_value'] > 0 ? '+' : ($stats['net_value'] < 0 ? '-' : '') }}Rp {{ number_format(abs($stats['net_value']), 0, ',', '.') }}
                </div>
            </td>
            <td style="background-color: #fffbeb;">
                <div class="summary-title" style="color: #b45309;">3B. Total Akumulasi Selisih</div>
                <div class="summary-value">{{ number_format($stats['abs_qty'], 0, ',', '.') }} Unit</div>
                <div class="summary-sub">Nilai Absolut: Rp {{ number_format($stats['abs_value'], 0, ',', '.') }}</div>
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 4%" class="text-center">No</th>
                <th style="width: 11%">Tanggal</th>
                <th style="width: 11%">No. Ref</th>
                <th style="width: 22%">Nama Produk</th>
                <th style="width: 9%">Batch</th>
                <th style="width: 8%" class="text-center">Jenis</th>
                <th style="width: 9%; text-align: right;">Qty Selisih</th>
                <th style="width: 9%; text-align: right;">HPP</th>
                <th style="width: 10%; text-align: right;">Nilai Selisih</th>
                <th style="width: 7%">Petugas</th>
            </tr>
        </thead>
        <tbody>
            @forelse($movements as $index => $item)
                @php
                    $qty = (int) $item->quantity;
                    $unitHpp = (float) ($item->unit_hpp ?? 0);
                    $val = $qty * $unitHpp;
                    $isMinus = $qty < 0;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->created_at ? $item->created_at->format('d/m/Y H:i') : '-' }}</td>
                    <td>{{ $item->doc_ref ?: '-' }}</td>
                    <td>
                        <div class="font-bold">{{ $item->product->name ?? '-' }}</div>
                        <div style="font-size: 7pt; color: #64748b;">{{ $item->product->barcode ?? '-' }} {{ $item->description ? '| ' . $item->description : '' }}</div>
                    </td>
                    <td>{{ $item->batch->batch_no ?? '-' }}</td>
                    <td class="text-center font-bold {{ $isMinus ? 'color-minus' : 'color-plus' }}">
                        {{ $isMinus ? 'Minus' : 'Plus' }}
                    </td>
                    <td class="text-right font-bold {{ $isMinus ? 'color-minus' : 'color-plus' }}">
                        {{ $qty > 0 ? '+' : '' }}{{ number_format($qty, 0, ',', '.') }} {{ $item->product->unit->name ?? '' }}
                    </td>
                    <td class="text-right">Rp {{ number_format($unitHpp, 0, ',', '.') }}</td>
                    <td class="text-right font-bold {{ $isMinus ? 'color-minus' : 'color-plus' }}">
                        {{ $val > 0 ? '+' : ($val < 0 ? '-' : '') }}Rp {{ number_format(abs($val), 0, ',', '.') }}
                    </td>
                    <td>{{ $item->user->name ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center italic" style="padding: 18px; color: #777;">
                        Tidak ada data selisih stok opname pada periode ini.
                    </td>
                </tr>
            @endforelse

            @if($movements->count() > 0)
                <tr class="total-row">
                    <td colspan="6" class="text-right uppercase color-minus">1. Total Stok Minus (Fisik &lt; Sistem)</td>
                    <td class="text-right color-minus">-{{ number_format($stats['minus_qty'], 0, ',', '.') }} Unit</td>
                    <td></td>
                    <td class="text-right color-minus">-Rp {{ number_format($stats['minus_value'], 0, ',', '.') }}</td>
                    <td></td>
                </tr>
                <tr class="total-row">
                    <td colspan="6" class="text-right uppercase color-plus">2. Total Stok Plus (Fisik &gt; Sistem)</td>
                    <td class="text-right color-plus">+{{ number_format($stats['plus_qty'], 0, ',', '.') }} Unit</td>
                    <td></td>
                    <td class="text-right color-plus">+Rp {{ number_format($stats['plus_value'], 0, ',', '.') }}</td>
                    <td></td>
                </tr>
                <tr class="grand-total-row">
                    <td colspan="6" class="text-right uppercase">3A. Total Selisih Bersih (Stok Plus - Stok Minus)</td>
                    <td class="text-right {{ $stats['net_qty'] < 0 ? 'color-minus' : ($stats['net_qty'] > 0 ? 'color-plus' : '') }}">
                        {{ $stats['net_qty'] > 0 ? '+' : '' }}{{ number_format($stats['net_qty'], 0, ',', '.') }} Unit
                    </td>
                    <td></td>
                    <td class="text-right {{ $stats['net_value'] < 0 ? 'color-minus' : ($stats['net_value'] > 0 ? 'color-plus' : '') }}">
                        {{ $stats['net_value'] > 0 ? '+' : ($stats['net_value'] < 0 ? '-' : '') }}Rp {{ number_format(abs($stats['net_value']), 0, ',', '.') }}
                    </td>
                    <td></td>
                </tr>
                <tr class="total-row" style="background-color: #fef3c7;">
                    <td colspan="6" class="text-right uppercase">3B. Total Akumulasi Selisih (Stok Plus + Stok Minus)</td>
                    <td class="text-right">{{ number_format($stats['abs_qty'], 0, ',', '.') }} Unit</td>
                    <td></td>
                    <td class="text-right">Rp {{ number_format($stats['abs_value'], 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            @endif
        </tbody>
    </table>
</body>
</html>
