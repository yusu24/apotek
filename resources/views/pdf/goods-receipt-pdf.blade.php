<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penerimaan Barang - {{ $receipt->delivery_note_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #333;
        }

        .container {
            padding: 20px;
        }

        /* Header Styles */
        .header {
            border-bottom: 3px solid #2563eb;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .header-top {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }

        .header-left {
            display: table-cell;
            width: 60%;
            vertical-align: top;
        }

        .header-right {
            display: table-cell;
            width: 40%;
            text-align: right;
            vertical-align: top;
        }

        .apotek-name {
            font-size: 18pt;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 5px;
        }

        .doc-title {
            font-size: 14pt;
            font-weight: bold;
            color: #374151;
        }

        .print-info {
            font-size: 9pt;
            color: #6b7280;
        }

        /* Receipt Info Section */
        .receipt-info {
            background-color: #f3f4f6;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }

        .info-row:last-child {
            margin-bottom: 0;
        }

        .info-label {
            display: table-cell;
            width: 30%;
            font-weight: bold;
            color: #4b5563;
        }

        .info-value {
            display: table-cell;
            width: 70%;
            color: #111827;
        }

        /* Items Table */
        .section-title {
            font-size: 12pt;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #e5e7eb;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        thead {
            background-color: #1e40af;
            color: white;
        }

        th {
            padding: 10px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 10pt;
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
            padding: 8px;
            font-size: 10pt;
        }

        .product-name {
            font-weight: bold;
            color: #111827;
        }

        .batch-number {
            font-family: 'Courier New', monospace;
            background-color: #e5e7eb;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9pt;
        }

        /* Notes Section */
        .notes-section {
            background-color: #fef3c7;
            padding: 12px;
            border-left: 4px solid #f59e0b;
            margin-bottom: 20px;
            font-size: 10pt;
        }

        .notes-label {
            font-weight: bold;
            color: #92400e;
            margin-bottom: 5px;
        }

        /* Footer / Signature Section */
        .signature-section {
            margin-top: 40px;
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
            padding: 10px;
        }

        .signature-label {
            font-weight: bold;
            margin-bottom: 60px;
            display: block;
        }

        .signature-line {
            border-top: 1px solid #333;
            padding-top: 5px;
            font-size: 10pt;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 9pt;
            font-weight: bold;
        }

        .status-paid {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-partial {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-pending {
            background-color: #fee2e2;
            color: #991b1b;
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
                    <div class="doc-title">PENERIMAAN BARANG</div>
                </div>
                <div class="header-right">
                    <div class="print-info">
                        Dicetak: {{ $printedAt }}<br>
                        Oleh: {{ $printedBy }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Receipt Information -->
        <div class="receipt-info">
            <div class="info-row">
                <div class="info-label">No. Surat Jalan:</div>
                <div class="info-value">{{ $receipt->delivery_note_number }}</div>
            </div>
            @if($receipt->purchaseOrder)
            <div class="info-row">
                <div class="info-label">No. Purchase Order:</div>
                <div class="info-value">{{ $receipt->purchaseOrder->po_number }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Supplier:</div>
                <div class="info-value">{{ $receipt->purchaseOrder->supplier->name ?? '-' }}</div>
            </div>
            @endif
            <div class="info-row">
                <div class="info-label">Tanggal Terima:</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($receipt->received_date)->format('d F Y') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Total Pembelian:</div>
                <div class="info-value">Rp {{ number_format($receipt->total_amount, 0, ',', '.') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Status Pembayaran:</div>
                <div class="info-value">
                    <span class="status-badge status-{{ $receipt->payment_status_color }}">
                        {{ $receipt->payment_status_label }}
                    </span>
                </div>
            </div>
            @if($receipt->due_date)
            <div class="info-row">
                <div class="info-label">Jatuh Tempo:</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($receipt->due_date)->format('d F Y') }}</div>
            </div>
            @endif
        </div>

        <!-- Items Table -->
        <div class="section-title">Daftar Barang yang Diterima</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;" class="center">No</th>
                    <th style="width: 40%;">Nama Produk</th>
                    <th style="width: 15%;" class="center">Jumlah</th>
                    <th style="width: 15%;" class="center">Satuan</th>
                    <th style="width: 25%;" class="right">Batch No</th>
                </tr>
            </thead>
            <tbody>
                @foreach($receipt->items as $index => $item)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td class="product-name">{{ $item->product->name }}</td>
                    <td class="center">{{ number_format($item->qty_received, 0, ',', '.') }}</td>
                    <td class="center">{{ $item->unit->name ?? '-' }}</td>
                    <td class="right">
                        @if($item->batch_no)
                            <span class="batch-number">{{ $item->batch_no }}</span>
                        @else
                            -
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Notes (if exists) -->
        @if($receipt->notes)
        <div class="notes-section">
            <div class="notes-label">Catatan:</div>
            <div>{{ $receipt->notes }}</div>
        </div>
        @endif

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
