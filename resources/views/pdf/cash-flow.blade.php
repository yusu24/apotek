<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Arus Kas (Standar)</title>
    <style>
        @page { 
            size: A4; 
            margin: 15mm 1cm 10mm 1cm; 
        }
        
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: 10pt; 
            color: #000; 
            margin: 0; 
            padding: 0; 
            line-height: 1.2;
        }

        .full-width { width: 100%; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }

        .report-header { 
            margin-bottom: 25px; 
            text-align: center;
        }
        .store-name { 
            font-size: 11pt; 
            margin-bottom: 5px; 
        }
        .report-title { 
            font-size: 16pt; 
            font-weight: bold; 
            color: #800000; /* Maroon */
            margin: 0;
        }
        .period-info { 
            font-size: 10pt; 
            margin-top: 5px; 
        }
        
        table { 
            width: 100%; 
            border-collapse: collapse; 
            table-layout: fixed; 
        }
        
        /* Table Header Style */
        .column-headers td {
            padding: 5px 0;
            border-bottom: 1.5pt solid #4a7ebb; /* Blue-ish line */
            font-weight: bold;
            color: #4a7ebb;
        }

        td { 
            padding: 3px 0; 
            vertical-align: bottom; 
        }
        
        /* Hierarchy Levels */
        .level-0 { font-weight: bold; padding-top: 10px; text-transform: uppercase; }
        .level-1 { font-weight: normal; padding-left: 15px; padding-top: 5px; }
        .level-2 { font-weight: normal; padding-left: 30px; }
        .level-3 { font-weight: normal; padding-left: 45px; }

        .timestamp {
            position: fixed;
            top: -10mm;
            right: 0;
            font-size: 7pt;
            color: #666;
        }
        
        /* Summary Lines */
        .summary-label { font-weight: bold; }
        .summary-value { 
            font-weight: bold; 
            border-top: 0.5pt solid #000; 
            text-align: right;
            width: 35%;
        }

        .grand-total-label { 
            font-weight: bold; 
            text-transform: uppercase;
            padding-top: 15px;
        }
        .grand-total-value { 
            font-weight: bold; 
            border-top: 0.5pt solid #000; 
            border-bottom: 3pt double #000;
            text-align: right;
            padding-top: 2px;
        }
    </style>
</head>
<body>
    <div class="timestamp">
        Waktu Cetak: {{ $printedAt }}
    </div>
    <div class="report-header">
        <div class="store-name">{{ trim($store['name']) }}</div>
        <div class="report-title">Arus Kas (Standar)</div>
        <div class="period-info">
            Periode: {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}
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
                <td style="color: #800000">SALDO KAS AKHIR</td>
                <td class="grand-total-value" style="color: #800000">{{ format_accounting_standard($data['ending_balance']) }}</td>
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
