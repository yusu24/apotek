<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pesanan Pembelian - {{ $po->po_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            @if(isset($format) && $format == 'a4')
                font-size: 10px;
                padding: 15mm;
            @elseif(isset($format) && $format == 'ncr')
                font-size: 8px;
                padding: 6mm;
            @else
                font-size: 8.5px;
                padding: 8mm;
            @endif
            line-height: 1.3;
        }
        .header-section {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        .header-left {
            display: table-cell;
            width: 45%;
            vertical-align: top;
        }
        .header-right {
            display: table-cell;
            width: 55%;
            vertical-align: top;
            padding-left: 10px;
        }
        .supplier-box {
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            padding: 3px 0;
        }
        .supplier-label {
            font-weight: bold;
        }
        .title-box {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            border: 2px solid #000;
            padding: 4px;
            margin-bottom: 5px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            border: 1px solid #000;
            padding: 2px 5px;
            font-size: 9px;
        }
        .info-label {
            width: 50px;
            font-weight: normal;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        .items-table th,
        .items-table td {
            border: 1px solid #000;
            padding: 3px 5px;
            text-align: left;
        }
        .items-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
            font-size: 9px;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .terbilang-box {
            border: 1px solid #000;
            padding: 5px;
            margin: 10px 0;
            min-height: 30px;
        }
        .label-bold {
            font-weight: bold;
            margin-bottom: 2px;
        }
        .summary-section {
            float: right;
            width: 280px;
            margin-top: 10px;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }
        .summary-table td {
            border: 1px solid #000;
            padding: 2px 8px;
            font-size: 9px;
        }
        .summary-label {
            width: 140px;
        }
        .signature-section {
            clear: both;
            margin-top: 20px;
            display: table;
            width: 100%;
        }
        .signature-col {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: top;
        }
        .signature-line {
            margin-top: 40px;
            margin-bottom: 3px;
            border-bottom: 1px solid #000;
            width: 150px;
            display: inline-block;
        }
        .footer {
            position: absolute;
            bottom: 10mm;
            left: 10mm;
            right: 10mm;
            font-size: 7px;
            display: table;
            width: calc(100% - 20mm);
        }
        .footer-left {
            display: table-cell;
            text-align: left;
        }
        .footer-right {
            display: table-cell;
            text-align: right;
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <div class="header-section">
        <div class="header-left">
            <div class="supplier-box">
                <div class="supplier-label">Supplier :</div>
                <div>{{ $po->supplier->name ?? 'Toko Umum' }}</div>
            </div>
        </div>
        <div class="header-right">
            <div class="title-box">Pesanan Pembelian</div>
            <table class="info-table">
                <tr>
                    <td class="info-label">PO #</td>
                    <td>{{ $po->po_number }}</td>
                </tr>
                <tr>
                    <td class="info-label">Untuk</td>
                    <td>{{ $store['name'] ?? 'Group' }}</td>
                </tr>
                <tr>
                    <td class="info-label">Tanggal</td>
                    <td>{{ \Carbon\Carbon::parse($po->date)->format('d/m/Y') }}</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 80px;">Kode Barang</th>
                <th>Nama Barang</th>
                <th style="width: 40px;">Kts.</th>
                <th style="width: 70px;">@Harga</th>
                <th style="width: 90px;">Total Harga</th>
            </tr>
        </thead>
        <tbody>
            @php
                $subtotal = 0;
            @endphp
            @foreach($po->items as $item)
            @php
                $price = $item->price ?? 0;
                $total = $item->qty * $price;
                $subtotal += $total;
            @endphp
            <tr>
                <td>{{ $item->product->barcode ?? 'BU.00186' }}</td>
                <td>{{ $item->product->name }}</td>
                <td class="text-center">{{ $item->qty }}</td>
                <td class="text-right">{{ number_format($price, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($total, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Terbilang & Keterangan -->
    <div class="terbilang-box">
        <div class="label-bold">Terbilang :</div>
        <div style="margin-bottom: 5px;">{{ $terbilang ?? 'Tujuh ratus lima puluh ribu rupiah' }}</div>
        <div class="label-bold">Keterangan :</div>
        <div>{{ $po->notes ?? 'SS-IT- Kabel Sling untuk antena wifi gelam.' }}</div>
    </div>

    <!-- Summary -->
    <div class="summary-section">
        <table class="summary-table">
            <tr>
                <td class="summary-label">Sub Total</td>
                <td class="text-right">{{ number_format($subtotal, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="summary-label">PPN (%)</td>
                <td class="text-right">0</td>
            </tr>
            <tr>
                <td class="summary-label"><strong>Total</strong></td>
                <td class="text-right"><strong>{{ number_format($subtotal, 0, ',', '.') }}</strong></td>
            </tr>
            <tr>
                <td class="summary-label">PPh Jasa</td>
                <td class="text-right">0</td>
            </tr>
            <tr>
                <td class="summary-label"><strong>Total Bayar</strong></td>
                <td class="text-right"><strong>{{ number_format($subtotal, 0, ',', '.') }}</strong></td>
            </tr>
        </table>
    </div>

    <!-- Signatures -->
    <div class="signature-section">
        <div class="signature-col">
            <div>Dibuat Oleh,</div>
            <div class="signature-line"></div>
            <div><strong>{{ $po->user->name ?? 'M. Yusuf Effendi' }}</strong></div>
        </div>
        <div class="signature-col">
            <div>Disetujui Oleh,</div>
            <div class="signature-line"></div>
            <div><strong>{{ $approvedBy ?? 'Andri Halim Gunawan' }}</strong></div>
            <div>{{ \Carbon\Carbon::parse($po->date)->format('d/m/Y') }}</div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="footer-left">Tgl Cetak: {{ $printedAt }}</div>
        <div class="footer-right">Halaman 1 dari 1</div>
    </div>
</body>
</html>
