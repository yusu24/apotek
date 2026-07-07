<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Arus Kas (Standar)</title>
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

        .full-width { width: 100%; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }

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
        .period-info {
            font-size: 10pt;
            margin-top: 3px;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 16px;
            font-size: 12pt;
        }

        /* Table Header Style */
        .column-headers td {
            padding: 6px;
            background-color: #1e40af;
            color: #ffffff;
            font-weight: bold;
        }

        td {
            padding: 4px 6px;
            vertical-align: bottom;
        }

        /* Hierarchy Levels */
        .level-0 { font-weight: bold; padding-top: 10px; text-transform: uppercase; }
        .level-1 { font-weight: normal; padding-left: 15px; padding-top: 5px; }
        .level-2 { font-weight: normal; padding-left: 30px; }
        .level-3 { font-weight: normal; padding-left: 45px; }

        /* Summary Lines */
        .summary-label { font-weight: bold; background-color: #dbeafe; }
        .summary-value {
            font-weight: bold;
            background-color: #dbeafe;
            text-align: right;
            width: 35%;
        }

        .grand-total-label {
            font-weight: bold;
            text-transform: uppercase;
            background-color: #1e40af;
            color: #ffffff;
            padding: 8px 6px;
        }
        .grand-total-value {
            font-weight: bold;
            text-align: right;
            background-color: #1e40af;
            color: #ffffff;
            padding: 8px 6px;
        }
    </style>
</head>
<body>
    <div class="report-header">
        <div class="store-name">{{ trim($store['name']) }}</div>
        <div class="report-title">LAPORAN ARUS KAS (STANDAR)</div>
        <div class="period-info">
            Untuk Periode {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
        </div>
    </div>

    <table>
        <thead>
            <tr class="column-headers">
                <td style="width: 65%">Deskripsi</td>
                <td style="width: 35%; text-align: right;">Nilai</td>
            </tr>
        </thead>
        <tbody>
            {{-- AKTIVITAS OPERASIONAL --}}
            <tr class="level-0"><td colspan="2">ARUS KAS DARI AKTIVITAS OPERASIONAL</td></tr>
            <tr class="level-1"><td>Penerimaan dari pelanggan</td><td class="text-right">{{ format_accounting_standard($data['receipts_from_customers']) }}</td></tr>
            <tr class="level-1"><td>Pembayaran ke pemasok</td><td class="text-right">{{ format_accounting_standard($data['payments_to_suppliers']) }}</td></tr>
            <tr class="level-1"><td>Pengeluaran operasional</td><td class="text-right">{{ format_accounting_standard($data['payments_for_expenses']) }}</td></tr>
            <tr class="level-1"><td>Pendapatan lainnya</td><td class="text-right">{{ format_accounting_standard($data['other_operating']) }}</td></tr>
            
            <tr class="level-0">
                <td class="summary-label">Kas Bersih dari Aktivitas Operasional</td>
                <td class="summary-value">{{ format_accounting_standard($data['net_cash_operating']) }}</td>
            </tr>

            {{-- AKTIVITAS INVESTASI --}}
            <tr class="level-0"><td colspan="2">ARUS KAS DARI AKTIVITAS INVESTASI</td></tr>
            <tr class="level-1"><td>Perolehan/Penjualan Aset</td><td class="text-right">{{ format_accounting_standard($data['sale_assets'] + $data['purchase_assets']) }}</td></tr>
            <tr class="level-1"><td>Investasi Lainnya</td><td class="text-right">{{ format_accounting_standard($data['other_investing']) }}</td></tr>
            
            <tr class="level-0">
                <td class="summary-label">Kas Bersih dari Aktivitas Investasi</td>
                <td class="summary-value">{{ format_accounting_standard($data['net_cash_investing']) }}</td>
            </tr>

            {{-- AKTIVITAS PENDANAAN --}}
            <tr class="level-0"><td colspan="2">ARUS KAS DARI AKTIVITAS PENDANAAN</td></tr>
            <tr class="level-1"><td>Pinjaman</td><td class="text-right">{{ format_accounting_standard($data['loans']) }}</td></tr>
            <tr class="level-1"><td>Ekuitas/Modal</td><td class="text-right">{{ format_accounting_standard($data['equity']) }}</td></tr>
            
            <tr class="level-0">
                <td class="summary-label">Kas Bersih dari Aktivitas Pendanaan</td>
                <td class="summary-value">{{ format_accounting_standard($data['net_cash_financing']) }}</td>
            </tr>

            {{-- SUMMARY --}}
            <tr><td colspan="2" style="height: 30px;"></td></tr>
            
            <tr class="level-0">
                <td>Kenaikan (Penurunan) Bersih Kas</td>
                <td class="text-right">{{ format_accounting_standard($data['net_increase']) }}</td>
            </tr>
            <tr class="level-0">
                <td>Saldo Kas Awal</td>
                <td class="text-right">{{ format_accounting_standard($data['beginning_balance']) }}</td>
            </tr>
            
            <tr class="grand-total-label">
                <td>SALDO KAS AKHIR</td>
                <td class="grand-total-value">{{ format_accounting_standard($data['ending_balance']) }}</td>
            </tr>
        </tbody>
    </table>

    @php
    function format_accounting_standard($number) {
        $formatted = number_format(abs($number), 2, ',', '.');
        if ($formatted == '0,00') return '0';
        return ($number < 0 ? '-' : '') . $formatted;
    }
    @endphp
</body>
</html>
