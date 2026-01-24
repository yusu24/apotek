<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Umur Hutang</title>
    <style>
        @page { 
            size: A4; 
            margin:    10mm 1cm 10mm 1cm; 
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #333;
        }
        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 12px;
            color: #666;
        }
        .summary-box {
            display: inline-block;
            width: 23%;
            background: #f5f5f5;
            padding: 10px;
            margin-right: 1.5%;
            margin-bottom: 20px;
            border-radius: 5px;
            vertical-align: top;
        }
        .summary-box:last-child {
            margin-right: 0;
        }
        .summary-title {
            font-size: 10px;
            font-weight: bold;
            color: #666;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .summary-value {
            font-size: 14px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .section-title {
            background: #333;
            color: white;
            padding: 8px 10px;
            font-weight: bold;
            font-size: 12px;
            margin-top: 20px;
            margin-bottom: 5px;
        }
        th {
            background: #f0f0f0;
            padding: 8px 6px;
            text-align: left;
            font-weight: bold;
            font-size: 10px;
            border: 1px solid #ddd;
        }
        th.text-right, td.text-right {
            text-align: right;
        }
        th.text-center, td.text-center {
            text-align: center;
        }
        td {
            padding: 6px 6px;
            border: 1px solid #ddd;
            font-size: 10px;
        }
        .signature-box {
            text-align: center;
            width: 45%;
        }
        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #333;
            padding-top: 5px;
        }
        .empty-row {
            text-align: center;
            color: #999;
            font-style: italic;
            padding: 10px;
        }
    </style>
</head>
<body>
    <div class="report-header text-center">
        <div class="store-name uppercase">{{ trim(\App\Models\Setting::get('store_name', 'APOTEK')) }}</div>
        <div class="report-title">LAPORAN UMUR HUTANG (AP AGING REPORT)</div>
        <div class="period-info">Per Tanggal: {{ now()->translatedFormat('d F Y') }}</div>
    </div>

    <table class="summary-table">
        <tr>
            @foreach(['0-30', '31-60', '61-90', '>90'] as $key)
            <td style="padding: 5px; border: none;">
                <div class="summary-box">
                    <div class="summary-title">{{ $key }} HARI</div>
                    <div class="summary-value">Rp {{ number_format($data['summary'][$key], 0, ',', '.') }}</div>
                </div>
            </td>
            @endforeach
        </tr>
    </table>

    {{-- Detail Tables per Bucket --}}
    @foreach(['0-30', '31-60', '61-90', '>90'] as $key)
        @if(count($data[$key]) > 0)
            <div class="section-title">KATEGORI UMUR: {{ $key }} HARI (Total: Rp {{ number_format($data['summary'][$key], 0, ',', '.') }})</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 25%;">Supplier</th>
                        <th style="width: 15%;">No. Surat Jalan</th>
                        <th style="width: 10%;">Tgl Terima</th>
                        <th style="width: 10%;">Jatuh Tempo</th>
                        <th class="text-center" style="width: 10%;">Umur</th>
                        <th class="text-right" style="width: 15%;">Total Tagihan</th>
                        <th class="text-right" style="width: 15%;">Sisa Hutang</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data[$key] as $item)
                    <tr>
                        <td>{{ $item['supplier'] }}</td>
                        <td>{{ $item['invoice_number'] }}</td>
                        <td>{{ \Carbon\Carbon::parse($item['date'])->format('d/m/Y') }}</td>
                        <td>{{ $item['due_date'] ? \Carbon\Carbon::parse($item['due_date'])->format('d/m/Y') : '-' }}</td>
                        <td class="text-center">{{ $item['age'] }} Hari</td>
                        <td class="text-right">{{ number_format($item['total_amount'], 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($item['outstanding'], 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @endforeach

    {{-- Total --}}
    <div class="total-highlight">
        TOTAL HUTANG OUTSTANDING: Rp {{ number_format($data['summary']['total'], 0, ',', '.') }}
    </div>

    <table class="signature-section">
        <tr>
            <td class="signature-box" style="border:none">
                <div class="font-bold">Disetujui Oleh</div>
                <div class="signature-line">(...........................)</div>
            </td>
            <td style="width: 10%; border:none"></td>
            <td class="signature-box" style="border:none">
                <div class="font-bold">Dibuat Oleh</div>
                <div class="signature-line">(...........................)</div>
            </td>
        </tr>
    </table>

    </body>
</html>
