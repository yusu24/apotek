<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Arus Kas</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; color: #333; }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .header-table td { text-align: center; }
        .store-name { font-size: 16pt; font-weight: bold; text-transform: uppercase; margin-bottom: 5px; text-align: center; }
        .report-title { font-size: 12pt; font-weight: bold; text-transform: uppercase; color: #555; text-align: center; }
        .period { font-size: 10pt; margin-top: 5px; color: #666; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 8px 10px; }
        thead th { background-color: #00BFFF; color: white; text-align: left; font-weight: bold; border-bottom: 2px solid #009ACD; }
        
        .section-header { 
            background-color: #f3f4f6; 
            font-weight: bold; 
            padding-top: 15px; 
            padding-bottom: 5px;
            color: #111;
            border-bottom: 1px solid #ddd;
        }
        
        .sub-row td { padding-left: 25px; border-bottom: 1px solid #eee; }
        
        .total-row { 
            font-weight: bold; 
            background-color: #f0f9ff; 
            color: #000;
            border-top: 1px solid #cbd5e1;
            border-bottom: 2px solid #white; 
        }
        
        .grand-total-row {
            font-weight: bold;
            font-size: 11pt;
            background-color: #333;
            color: white;
        }
        
        .text-right { text-align: right; }
        
        .footer { 
            margin-top: 50px; 
            border-top: 1px solid #eee; 
            padding-top: 10px; 
            font-size: 8pt; 
            color: #999; 
            display: flex; 
            justify-content: space-between;
        }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td>
                <div class="store-name">{{ $store['name'] }}</div>
                <div class="report-title">LAPORAN ARUS KAS</div>
                <div class="period">
                    Periode: {{ \Carbon\Carbon::parse($startDate)->format('d F Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}
                </div>
                <div style="font-size: 9pt; margin-top: 5px; font-style: italic;">(dalam Mata Uang Rupiah IDR)</div>
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th style="width: 65%">Akun & Kategori</th>
                <th style="width: 35%; text-align: right;">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            {{-- Operating --}}
            <tr class="section-header"><td colspan="2">ARUS KAS DARI AKTIVITAS OPERASIONAL</td></tr>
            <tr class="sub-row"><td>Penerimaan dari pelanggan</td><td class="text-right">{{ format_accounting($data['receipts_from_customers']) }}</td></tr>
            <tr class="sub-row"><td>Pembayaran ke pemasok</td><td class="text-right">{{ format_accounting($data['payments_to_suppliers']) }}</td></tr>
            <tr class="sub-row"><td>Pengeluaran operasional</td><td class="text-right">{{ format_accounting($data['payments_for_expenses']) }}</td></tr>
            <tr class="sub-row"><td>Pendapatan lainnya</td><td class="text-right">{{ format_accounting($data['other_operating']) }}</td></tr>
            <tr class="total-row"><td>Kas Bersih dari Aktivitas Operasional</td><td class="text-right">{{ format_accounting($data['net_cash_operating']) }}</td></tr>

            {{-- Investing --}}
            <tr class="section-header"><td colspan="2">ARUS KAS DARI AKTIVITAS INVESTASI</td></tr>
            <tr class="sub-row"><td>Perolehan/Penjualan Aset</td><td class="text-right">{{ format_accounting($data['sale_assets'] + $data['purchase_assets']) }}</td></tr>
            <tr class="sub-row"><td>Investasi Lainnya</td><td class="text-right">{{ format_accounting($data['other_investing']) }}</td></tr>
            <tr class="total-row"><td>Kas Bersih dari Aktivitas Investasi</td><td class="text-right">{{ format_accounting($data['net_cash_investing']) }}</td></tr>

            {{-- Financing --}}
            <tr class="section-header"><td colspan="2">ARUS KAS DARI AKTIVITAS PENDANAAN</td></tr>
            <tr class="sub-row"><td>Pinjaman</td><td class="text-right">{{ format_accounting($data['loans']) }}</td></tr>
            <tr class="sub-row"><td>Ekuitas/Modal</td><td class="text-right">{{ format_accounting($data['equity']) }}</td></tr>
            <tr class="total-row"><td>Kas Bersih dari Aktivitas Pendanaan</td><td class="text-right">{{ format_accounting($data['net_cash_financing']) }}</td></tr>

            {{-- Summary --}}
            <tr><td colspan="2" style="height: 30px;"></td></tr>
            
            <tr style="border-bottom: 1px solid #eee;">
                <td style="padding-top: 10px; font-weight: bold;">Kenaikan (Penurunan) Bersih Kas</td>
                <td class="text-right" style="padding-top: 10px; font-weight: bold;">{{ format_accounting($data['net_increase']) }}</td>
            </tr>
            <tr style="border-bottom: 2px solid #333;">
                <td>Saldo Kas Awal</td>
                <td class="text-right">{{ format_accounting($data['beginning_balance']) }}</td>
            </tr>
            <tr class="grand-total-row">
                <td style="padding: 10px;">SALDO KAS AKHIR</td>
                <td class="text-right" style="padding: 10px;">{{ format_accounting($data['ending_balance']) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <div>Dicetak oleh: {{ $printedBy }}</div>
        <div>Waktu cetak: {{ $printedAt }}</div>
    </div>

    @php
    function format_accounting($number) {
        if ($number < 0) {
            return '( ' . number_format(abs($number), 0, ',', '.') . ' )';
        }
        return number_format($number, 0, ',', '.');
    }
    @endphp
</body>
</html>
