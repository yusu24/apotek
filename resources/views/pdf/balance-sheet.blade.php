<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Neraca</title>
    <style>
        @page { 
            size: A4; 
            margin:  15mm 1cm 10mm 1cm; 
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
        .level-0 { font-weight: bold; padding-top: 10px; }
        .level-1 { font-weight: bold; padding-left: 15px; padding-top: 5px; }
        .level-2 { font-weight: normal; padding-left: 30px; }
        .level-3 { font-weight: regular; padding-left: 45px; }
        
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
            width: 30%;
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
        <div class="report-title">Neraca (Standar)</div>
        <div class="period-info">Per Tgl. {{ \Carbon\Carbon::parse($asOfDate)->format('d/m/Y') }}</div>
    </div>

    @if(!$reportData['balance_check'])
    <div style="background-color: #fee2e2; color: #b91c1c; padding: 10px; margin-bottom: 15px; text-align: center; border: 1px solid #f87171; font-weight: bold;">
        PERHATIAN: NERACA TIDAK BALANCE! Selisih: {{ format_accounting($reportData['total_assets'] - ($reportData['total_liabilities'] + $reportData['total_equity'] + $reportData['net_income'])) }}
    </div>
    @endif

    <table>
        <thead>
            <tr class="column-headers">
                <td style="width: 70%">Deskripsi</td>
                <td style="width: 30%; text-align: right;">Nilai</td>
            </tr>
        </thead>
        <tbody>
            {{-- ASET --}}
            <tr class="level-0"><td colspan="2">ASET</td></tr>
            
            {{-- ASET LANCAR --}}
            <tr class="level-1"><td colspan="2">ASET LANCAR</td></tr>
            @foreach($reportData['current_assets'] as $account)
            <tr class="level-2">
                <td>{{ $account->name }}</td>
                <td class="text-right">{{ format_accounting_standard($account->balance) }}</td>
            </tr>
            @endforeach
            
            <tr class="level-1">
                <td class="summary-label">Jumlah Aset Lancar</td>
                <td class="summary-value">{{ format_accounting_standard($reportData['total_current_assets']) }}</td>
            </tr>

            {{-- ASET TIDAK LANCAR (Fixed Assets) --}}
            <tr class="level-1"><td colspan="2">ASET TIDAK LANCAR</td></tr>
            @foreach($reportData['fixed_assets'] as $account)
            <tr class="level-2">
                <td>{{ $account->name }}</td>
                <td class="text-right">{{ format_accounting_standard($account->balance) }}</td>
            </tr>
            @endforeach
            
            <tr class="level-1">
                <td class="summary-label">Jumlah Aset Tidak Lancar</td>
                <td class="summary-value">{{ format_accounting_standard($reportData['total_fixed_assets']) }}</td>
            </tr>

            <tr class="grand-total-label">
                <td>JUMLAH ASET</td>
                <td class="grand-total-value">{{ format_accounting_standard($reportData['total_assets']) }}</td>
            </tr>

            {{-- SPACING --}}
            <tr><td colspan="2" style="height: 30px;"></td></tr>

            {{-- KEWAJIBAN DAN EKUITAS --}}
            <tr class="level-0"><td colspan="2">KEWAJIBAN DAN EKUITAS</td></tr>
            
            {{-- KEWAJIBAN (Liabilities) --}}
            <tr class="level-1"><td colspan="2">KEWAJIBAN</td></tr>
            
            {{-- KEWAJIBAN JANGKA PENDEK --}}
            @if($reportData['current_liabilities']->count() > 0)
            <tr class="level-2"><td colspan="2">KEWAJIBAN JANGKA PENDEK</td></tr>
            @foreach($reportData['current_liabilities'] as $account)
            <tr class="level-3">
                <td>{{ $account->name }}</td>
                <td class="text-right">{{ format_accounting_standard($account->balance) }}</td>
            </tr>
            @endforeach
            <tr class="level-2">
                <td class="summary-label">Jumlah Kewajiban Jangka Pendek</td>
                <td class="summary-value">{{ format_accounting_standard($reportData['total_current_liabilities']) }}</td>
            </tr>
            @endif

            {{-- KEWAJIBAN JANGKA PANJANG --}}
            @if($reportData['long_term_liabilities']->count() > 0)
            <tr class="level-2"><td colspan="2">KEWAJIBAN JANGKA PANJANG</td></tr>
            @foreach($reportData['long_term_liabilities'] as $account)
            <tr class="level-3">
                <td>{{ $account->name }}</td>
                <td class="text-right">{{ format_accounting_standard($account->balance) }}</td>
            </tr>
            @endforeach
            <tr class="level-2">
                <td class="summary-label">Jumlah Kewajiban Jangka Panjang</td>
                <td class="summary-value">{{ format_accounting_standard($reportData['total_long_term_liabilities']) }}</td>
            </tr>
            @endif
            
            <tr class="level-1">
                <td class="summary-label">Jumlah Kewajiban</td>
                <td class="summary-value">{{ format_accounting_standard($reportData['total_liabilities']) }}</td>
            </tr>

            {{-- EKUITAS --}}
            <tr class="level-1"><td colspan="2">EKUITAS</td></tr>
            @foreach($reportData['equity'] as $account)
            <tr class="level-2"><td>{{ $account->name }}</td><td class="text-right">{{ format_accounting_standard($account->balance) }}</td></tr>
            @endforeach
            <tr class="level-2"><td>Laba Bersih Periode Berjalan</td><td class="text-right">{{ format_accounting_standard($reportData['net_income']) }}</td></tr>
            
            <tr class="level-1">
                <td class="summary-label">Jumlah Ekuitas</td>
                <td class="summary-value">{{ format_accounting_standard($reportData['total_equity'] + $reportData['net_income']) }}</td>
            </tr>

            <tr class="grand-total-label">
                <td>JUMLAH KEWAJIBAN DAN EKUITAS</td>
                <td class="grand-total-value">{{ format_accounting_standard($reportData['total_liabilities'] + $reportData['total_equity'] + $reportData['net_income']) }}</td>
            </tr>
        </tbody>
    </table>

    @php
    function format_accounting_standard($number) {
        $formatted = number_format(abs($number), 2, ',', '.');
        if ($formatted == '0,00') return '0';
        
        // Remove .00 if requested or keep it as per reference. Reference shows .00 for some.
        // Let's keep 2 decimal places as in the reference.
        
        return ($number < 0 ? '-' : '') . $formatted;
    }
    
    // Existing format_accounting for backward compat if needed in partials
    function format_accounting($number) {
        return format_accounting_standard($number);
    }
    @endphp
    </body>
</html>
