<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penerimaan Barang - {{ $receipt->delivery_note_number }}</title>
    <style>
        @page { 
            size: A4; 
            margin:    10mm 1cm 10mm 1cm; 
        }
        
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: 10pt; 
            color: #1a1a1a; 
            margin: 0; 
            padding: 0; 
            line-height: 1.4;
        }

        .full-width { width: 100%; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }

        .report-header { 
            margin-bottom: 30px; 
            display: block;
            width: 100%;
        }
        .store-name { 
            font-size: 16pt; 
            font-weight: bold; 
            margin: 0; 
        }
        .report-title { 
            font-size: 13pt; 
            font-weight: bold; 
            color: #333; 
            margin-top: 4px;
            letter-spacing: 1px;
        }

        /* Receipt Info Section */
        .receipt-info {
            background-color: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            border: 1px solid #e2e8f0;
        }

        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
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
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 2px solid #cbd5e1;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th { 
            padding: 10px 12px;
            background-color: #f8fafc;
            color: #1e293b;
            text-align: left;
            font-weight: bold;
            border-top: 2pt solid #1e293b;
            border-bottom: 1pt solid #cbd5e1;
            font-size: 11pt;
        }
        td { 
            padding: 8px 12px; 
            vertical-align: middle; 
            font-size: 11pt;
            border-bottom: 1px solid #f1f5f9;
        }

        .product-name { font-weight: bold; color: #111827; }

        .batch-number {
            font-family: 'Courier New', monospace;
            background-color: #f1f5f9;
            padding: 2px 6px;
            border-radius: 3px;
        }

        .notes-section {
            background-color: #fffbeb;
            padding: 15px;
            border-left: 4px solid #f59e0b;
            margin-bottom: 25px;
            font-size: 11pt;
            border: 1px solid #fef3c7;
        }

        .notes-label { font-weight: bold; color: #92400e; margin-bottom: 4px; }

        .signature-section {
            margin-top: 40px;
            width: 100% !important;
        }
        .signature-box { text-align: center; width: 45%; }
        .signature-label { font-weight: bold; margin-bottom: 50px; display: block; }
        .signature-line {
            border-top: 1px solid #1e293b;
            padding-top: 6px;
            font-weight: bold;
        }

        /* Status Badge adapted for simple table */
        .status-badge {
            padding: 4px 10px;
            border-radius: 4px;
            font-weight: bold;
            display: inline-block;
        }
        .status-paid { background-color: #dcfce7; color: #166534; }
        .status-partial { background-color: #fef3c7; color: #92400e; }
        .status-pending { background-color: #fee2e2; color: #991b1b; }

        </style>
</head>
<body>
    <div class="container">
    <div class="report-header text-center">
        <div class="store-name uppercase">{{ trim($apotekName) }}</div>
        <div class="report-title">PENERIMAAN BARANG</div>
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
        <table class="signature-section">
            <tr>
                <td class="signature-box" style="border:none">
                    <span class="signature-label">Manajer</span>
                    <div class="signature-line">(...........................)</div>
                </td>
                <td style="width:10%; border:none"></td>
                <td class="signature-box" style="border:none">
                    <span class="signature-label">Petugas Apotek</span>
                    <div class="signature-line">(...........................)</div>
                </td>
            </tr>
        </table>
    </div>
    </body>
</html>
