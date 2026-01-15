<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Aging {{ $type === 'ar' ? 'Piutang' : 'Hutang' }}</title>
    <style>
        @page { margin: 20px 30px; }
        body { font-family: sans-serif; font-size: 8pt; color: #333; margin: 0; padding: 0; }
        
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .header-table td { text-align: center; }
        .store-name { font-size: 16pt; font-weight: bold; text-transform: uppercase; margin-bottom: 5px; text-align: center; }
        .report-title { font-size: 12pt; font-weight: bold; text-transform: uppercase; color: #555; text-align: center; }
        .period { font-size: 10pt; margin-top: 5px; color: #666; text-align: center; }
        .currency-info { text-align: right; font-size: 8pt; margin-bottom: 5px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 5px; table-layout: fixed; }
        th { 
            padding: 4px 6px; 
            vertical-align: middle; 
            border-top: 2px solid #333; 
            border-bottom: 2px solid #333;
            text-align: left;
            font-weight: bold;
        }
        td { padding: 4px 6px; vertical-align: top; border-bottom: 1px solid #eee; }
        
        .entity-row td { 
            font-weight: normal; 
            border-bottom: none;
            padding-top: 8px;
        }
        
        .invoice-header-label {
            font-weight: bold;
            padding-left: 20px;
            font-size: 8pt;
        }

        .invoice-row td {
            font-size: 7.5pt;
            border-bottom: none;
            padding-top: 2px;
            padding-bottom: 2px;
        }
        
        .invoice-number-cell {
            padding-left: 20px;
        }

        .total-row td { 
            font-weight: bold; 
            border-top: 1px solid #333;
            border-bottom: 2px solid #333;
            padding-top: 6px;
            padding-bottom: 6px;
        }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        
        .footer { 
            position: fixed; 
            bottom: 0px; 
            left: 0px; 
            right: 0px; 
            height: 20px; 
            font-size: 7pt;
            border-top: 1px solid #eee;
            padding-top: 2px;
        }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td>
                <div class="store-name">{{ $store['name'] }}</div>
                <div class="report-title">Aging {{ $type === 'ar' ? 'Piutang' : 'Hutang' }}</div>
                <div class="period">Per Tanggal: {{ \Carbon\Carbon::now()->format('d F Y') }}</div>
            </td>
        </tr>
    </table>
    
    <div class="currency-info">
        Mata Uang : Rupiah
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 8%">Kode {{ $type === 'ar' ? 'Cust' : 'Supp' }}</th>
                <th style="width: 22%">Keterangan</th>
                <th style="width: 10%; text-align: right;">Limit {{ $type === 'ar' ? 'Piutang' : 'Hutang' }}</th>
                <th style="width: 12%; text-align: right;">Total {{ $type === 'ar' ? 'Piutang' : 'Hutang' }}</th>
                @foreach($reportData['buckets'] as $key => $bucket)
                    <th style="width: 9.6%; text-align: right;">{{ $bucket['label'] }}</th>
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
                    <td class="text-right font-bold">{{ number_format($entity['total'], 2, ',', '.') }}</td>
                    @foreach($reportData['buckets'] as $key => $bucket)
                        <td class="text-right">{{ number_format($entity['buckets'][$key], 2, ',', '.') }}</td>
                    @endforeach
                </tr>

                {{-- Invoice Label Row --}}
                @if(count($entity['invoices']) > 0)
                <tr class="invoice-row">
                    <td></td>
                    <td colspan="7" class="invoice-header-label">No. Invoice</td>
                </tr>
                @endif

                {{-- Invoice Detail Rows --}}
                @foreach($entity['invoices'] as $inv)
                <tr class="invoice-row">
                    <td></td>
                    <td class="invoice-number-cell">{{ $inv['number'] }}</td>
                    <td></td>
                    <td></td>
                    @foreach($reportData['buckets'] as $key => $bucket)
                        <td class="text-right">
                            @if($inv['bucket'] === $key)
                                {{ number_format($inv['amount'], 2, ',', '.') }}
                            @else
                                0.000
                            @endif
                        </td>
                    @endforeach
                </tr>
                @endforeach
                
                {{-- Border/Spacing between entities --}}
                <tr><td colspan="9" style="height: 5px; border-bottom: 1px solid #eee;"></td></tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="3" class="text-right">Total Rp.</td>
                <td class="text-right">{{ number_format($reportData['totalSummary']['total'], 2, ',', '.') }}</td>
                @foreach($reportData['buckets'] as $key => $bucket)
                    <td class="text-right">{{ number_format($reportData['totalSummary'][$key], 2, ',', '.') }}</td>
                @endforeach
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <div style="float: left;">Laporan Aging {{ $type === 'ar' ? 'Piutang' : 'Hutang' }} | Dicetak: {{ $printedBy }}</div>
        <div style="float: right;">{{ $printedAt }}</div>
    </div>
</body>
</html>
