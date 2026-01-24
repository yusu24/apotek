<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Aging {{ $type === 'ar' ? 'Piutang' : 'Hutang' }} (Standar)</title>
    <style>
        @page { 
            size: A4 landscape; 
            margin:  15mm 1cm 10mm 1cm; 
        }
        
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: 9pt; 
            color: #000; 
            margin: 0; 
            padding: 0; 
            line-height: 1.2;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        .italic { font-style: italic; }

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
        
        .timestamp {
            position: fixed;
            top: -10mm;
            right: 0;
            font-size: 7pt;
            color: #666;
        }

        table { 
            width: 100%; 
            border-collapse: collapse; 
            table-layout: fixed; 
            margin-top: 10px;
        }
        
        /* Table Header Style - Unified Border */
        .column-headers th {
            padding: 5px 0;
            border-bottom: 1.5pt solid #4a7ebb; /* Blue-ish line */
            font-weight: bold;
            color: #4a7ebb;
            text-align: left;
        }

        td { 
            padding: 4px 0; 
            vertical-align: top; 
        }
        
        .entity-row td { 
            font-weight: bold; 
            padding-top: 10px;
            padding-bottom: 5px;
            color: #000;
        }
        
        .transaction-row td {
            border-bottom: 0.5pt solid #eee;
            padding: 4px 0;
        }

        .total-row td { 
            font-weight: bold; 
            border-top: 1pt solid #000;
            padding-top: 8px;
            padding-bottom: 8px;
        }

        .grand-total-label { 
            font-weight: bold; 
            text-transform: uppercase;
            padding-top: 10px;
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
        <div class="report-title">LAPORAN AGING {{ $type === 'ar' ? 'PIUTANG' : 'HUTANG' }}</div>
        <div class="period-info">Per Tanggal: {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</div>
        <div class="currency-info">(dalam Mata Uang Rupiah IDR)</div>
    </div>

    <table>
        <thead>
            <tr class="column-headers">
                <th style="width: 8%">Kode</th>
                <th style="width: 20%">Keterangan</th>
                <th style="width: 10%; text-align: right;">Limit</th>
                <th style="width: 10%; text-align: right;">Total</th>
                @foreach($reportData['buckets'] as $key => $bucket)
                    <th style="width: 10%; text-align: right;">{{ $bucket['label'] }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($reportData['groupedData'] as $entity)
                {{-- Entity Summary Row --}}
                <tr class="entity-row">
                    <td>{{ $entity['code'] }}</td>
                    <td>{{ $entity['name'] }}</td>
                    <td class="text-right">{{ number_format($entity['limit'], 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($entity['total'], 2, ',', '.') }}</td>
                    @foreach($reportData['buckets'] as $key => $bucket)
                        <td class="text-right">{{ number_format($entity['buckets'][$key], 2, ',', '.') }}</td>
                    @endforeach
                </tr>

                {{-- Invoice Labels and Details --}}
                @foreach($entity['invoices'] as $inv)
                <tr class="transaction-row">
                    <td></td>
                    <td style="padding-left: 20px;">
                        {{ $inv['number'] }} <span style="font-size: 7pt; color: #999;">({{ $inv['dueDate'] }})</span>
                    </td>
                    <td></td>
                    <td></td>
                    @foreach($reportData['buckets'] as $key => $bucket)
                        <td class="text-right">
                            @if($inv['bucket'] === $key)
                                {{ number_format($inv['amount'], 2, ',', '.') }}
                            @else
                                <span style="color: #eee;">-</span>
                            @endif
                        </td>
                    @endforeach
                </tr>
                @endforeach
            @endforeach
            
            {{-- Grand Total --}}
            <tr class="total-row">
                <td colspan="3" class="text-right">GRAND TOTAL</td>
                <td class="text-right">{{ number_format($reportData['totalSummary']['total'], 2, ',', '.') }}</td>
                @foreach($reportData['buckets'] as $key => $bucket)
                    <td class="text-right">{{ number_format($reportData['totalSummary'][$key], 2, ',', '.') }}</td>
                @endforeach
            </tr>
        </tbody>
    </table>

    </body>
</html>
