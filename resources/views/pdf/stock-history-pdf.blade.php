<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Stok - {{ $product->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 9pt;
            line-height: 1.3;
            color: #333;
        }

        .container {
            padding: 15px;
        }

        /* Header Styles */
        .header {
            border-bottom: 3px solid #2563eb;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }

        .header-top {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }

        .header-left {
            display: table-cell;
            width: 70%;
            vertical-align: top;
        }

        .header-right {
            display: table-cell;
            width: 30%;
            text-align: right;
            vertical-align: top;
        }

        .apotek-name {
            font-size: 16pt;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 3px;
        }

        .doc-title {
            font-size: 13pt;
            font-weight: bold;
            color: #374151;
            margin-bottom: 3px;
        }

        .product-name {
            font-size: 11pt;
            font-weight: bold;
            color: #059669;
        }

        .period {
            font-size: 10pt;
            color: #6b7280;
            font-weight: bold;
        }

        .print-info {
            font-size: 8pt;
            color: #6b7280;
        }

        /* Product Info Section */
        .product-info {
            background-color: #f0fdf4;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 15px;
            border-left: 4px solid #059669;
        }

        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 5px;
        }

        .info-row:last-child {
            margin-bottom: 0;
        }

        .info-label {
            display: table-cell;
            width: 25%;
            font-weight: bold;
            color: #065f46;
            font-size: 9pt;
        }

        .info-value {
            display: table-cell;
            width: 75%;
            color: #111827;
            font-size: 9pt;
        }

        /* Table Styles */
        .section-title {
            font-size: 11pt;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 8px;
            margin-top: 15px;
            padding-bottom: 4px;
            border-bottom: 2px solid #e5e7eb;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 8pt;
        }

        thead {
            background-color: #1e40af;
            color: white;
        }

        th {
            padding: 6px 4px;
            text-align: left;
            font-weight: bold;
            font-size: 8pt;
        }

        th.center, td.center {
            text-align: center;
        }

        th.right, td.right {
            text-align: right;
        }

        tbody tr {
            border-bottom: 1px solid #e5e7eb;
        }

        tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }

        td {
            padding: 5px 4px;
            font-size: 8pt;
        }

        .batch-number {
            font-family: 'Courier New', monospace;
            font-size: 7pt;
        }

        /* Transaction Type Badges */
        .type-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7pt;
            font-weight: bold;
        }

        .type-in {
            background-color: #d1fae5;
            color: #065f46;
        }

        .type-out {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .type-sale {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .type-adjustment {
            background-color: #e9d5ff;
            color: #6b21a8;
        }

        /* Summary Section */
        .summary-section {
            background-color: #f3f4f6;
            padding: 12px;
            border-radius: 6px;
            margin-top: 15px;
        }

        .summary-title {
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 10pt;
            color: #1f2937;
        }

        .summary-grid {
            display: table;
            width: 100%;
        }

        .summary-item {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            padding: 8px;
        }

        .summary-label {
            font-size: 8pt;
            color: #6b7280;
            margin-bottom: 4px;
        }

        .summary-value {
            font-size: 12pt;
            font-weight: bold;
        }

        .value-green {
            color: #059669;
        }

        .value-red {
            color: #dc2626;
        }

        .value-blue {
            color: #2563eb;
        }

        /* Signature Section */
        .signature-section {
            margin-top: 30px;
            page-break-inside: avoid;
        }

        .signature-row {
            display: table;
            width: 100%;
        }

        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding: 8px;
        }

        .signature-label {
            font-weight: bold;
            margin-bottom: 50px;
            display: block;
            font-size: 9pt;
        }

        .signature-line {
            border-top: 1px solid #333;
            padding-top: 4px;
            font-size: 9pt;
        }

        /* Quantity coloring */
        .qty-positive {
            color: #059669;
            font-weight: bold;
        }

        .qty-negative {
            color: #dc2626;
            font-weight: bold;
        }

        .balance {
            color: #2563eb;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-top">
                <div class="header-left">
                    <div class="apotek-name">{{ $apotekName }}</div>
                    <div class="doc-title">KARTU STOK</div>
                    <div class="product-name">{{ $product->name }}</div>
                    <div class="period">Periode: {{ $period }}</div>
                </div>
                <div class="header-right">
                    <div class="print-info">
                        Dicetak: {{ $printedAt }}<br>
                        Oleh: {{ $printedBy }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Information -->
        <div class="product-info">
            <div class="info-row">
                <div class="info-label">Nama Produk:</div>
                <div class="info-value">{{ $product->name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Kategori:</div>
                <div class="info-value">{{ $product->category->name ?? '-' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Satuan:</div>
                <div class="info-value">{{ $product->unit->name ?? 'pcs' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Total Stok Saat Ini:</div>
                <div class="info-value">
                    <strong>{{ number_format($product->batches()->sum('stock_current'), 0, ',', '.') }}</strong> {{ $product->unit->name ?? 'pcs' }}
                </div>
            </div>
        </div>

        <!-- Active Batches -->
        @if($activeBatches->count() > 0)
        <div class="section-title">Batch Aktif</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 30%;">Batch No</th>
                    <th style="width: 25%;" class="center">Tanggal Kadaluarsa</th>
                    <th style="width: 20%;" class="right">Stok Saat Ini</th>
                    <th style="width: 25%;" class="center">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($activeBatches as $batch)
                <tr>
                    <td class="batch-number">{{ $batch->batch_no }}</td>
                    <td class="center">{{ $batch->expired_date ? \Carbon\Carbon::parse($batch->expired_date)->format('d/m/Y') : '-' }}</td>
                    <td class="right"><strong>{{ number_format($batch->stock_current, 0, ',', '.') }}</strong></td>
                    <td class="center">
                        @if($batch->expired_date < now())
                            <span class="type-badge type-out">Expired</span>
                        @elseif($batch->expired_date < now()->addMonths(3))
                            <span class="type-badge" style="background-color: #fef3c7; color: #92400e;">Near Exp</span>
                        @else
                            <span class="type-badge type-in">Good</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        <!-- Transaction History -->
        <div class="section-title">Riwayat Transaksi ({{ $movements->count() }} Transaksi)</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 12%;">Tanggal</th>
                    <th style="width: 10%;" class="center">Tipe</th>
                    <th style="width: 13%;">Batch</th>
                    <th style="width: 10%;" class="right">Masuk</th>
                    <th style="width: 10%;" class="right">Keluar</th>
                    <th style="width: 10%;" class="right">Saldo</th>
                    <th style="width: 15%;">Referensi</th>
                    <th style="width: 20%;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($movements as $movement)
                <tr>
                    <td>{{ $movement->created_at->format('d/m/y H:i') }}</td>
                    <td class="center">
                        @if($movement->type == 'in')
                            <span class="type-badge type-in">Masuk</span>
                        @elseif($movement->type == 'out')
                            <span class="type-badge type-out">Keluar</span>
                        @elseif($movement->type == 'sale')
                            <span class="type-badge type-sale">Jual</span>
                        @elseif($movement->type == 'adjustment')
                            <span class="type-badge type-adjustment">Opname</span>
                        @endif
                    </td>
                    <td class="batch-number">{{ $movement->batch->batch_no ?? '-' }}</td>
                    <td class="right">
                        @if($movement->quantity > 0)
                            <span class="qty-positive">{{ number_format($movement->quantity, 0, ',', '.') }}</span>
                        @else
                            -
                        @endif
                    </td>
                    <td class="right">
                        @if($movement->quantity < 0)
                            <span class="qty-negative">{{ number_format(abs($movement->quantity), 0, ',', '.') }}</span>
                        @else
                            -
                        @endif
                    </td>
                    <td class="right balance">{{ number_format($movement->running_balance, 0, ',', '.') }}</td>
                    <td>{{ $movement->doc_ref ?? '-' }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($movement->description, 30) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="center" style="padding: 20px;">Tidak ada riwayat transaksi untuk periode ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Summary -->
        <div class="summary-section">
            <div class="summary-title">Ringkasan Pergerakan Stok</div>
            <div class="summary-grid">
                <div class="summary-item">
                    <div class="summary-label">Total Masuk</div>
                    <div class="summary-value value-green">+{{ number_format($totalIn, 0, ',', '.') }}</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Total Keluar</div>
                    <div class="summary-value value-red">-{{ number_format($totalOut, 0, ',', '.') }}</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Perubahan (Net)</div>
                    <div class="summary-value {{ $netChange >= 0 ? 'value-green' : 'value-red' }}">
                        {{ $netChange >= 0 ? '+' : '' }}{{ number_format($netChange, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-row">
                <div class="signature-box">
                    <span class="signature-label">Manajer</span>
                    <div class="signature-line">(...........................)</div>
                </div>
                <div class="signature-box">
                    <span class="signature-label">Petugas Apotek</span>
                    <div class="signature-line">(...........................)</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
