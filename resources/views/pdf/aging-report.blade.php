<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Aging {{ $type === 'ar' ? 'Piutang' : 'Hutang' }}</title>
    <style>
        @page { margin: 20px 30px; }
        body { font-family: sans-serif; font-size: 9pt; color: #333; }
        
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; text-align: center; }
        .header-table td { text-align: center; }
        .store-name { font-size: 16pt; font-weight: bold; text-transform: uppercase; margin-bottom: 5px; text-align: center; width: 100%; }
        .report-title { font-size: 12pt; font-weight: bold; text-transform: uppercase; color: #555; text-align: center; width: 100%; }
        .period { font-size: 10pt; margin-top: 5px; color: #666; text-align: center; width: 100%; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 6px 8px; vertical-align: top; }
        
        thead th { 
            background-color: #00BFFF; 
            color: white; 
            text-align: left; 
            font-weight: bold; 
            border-bottom: 2px solid #009ACD;
            font-size: 9pt;
        }
        
        .entity-row td {
            font-weight: bold;
            background-color: #f9fafb;
            border-bottom: 1px solid #ddd;
            padding-top: 8px;
            padding-bottom: 8px;
        }
        
        .invoice-row td {
            font-size: 8pt;
            border-bottom: 1px solid #eee;
            color: #555;
            padding-top: 4px;
            padding-bottom: 4px;
        }
        
        .total-row td { 
            font-weight: bold; 
            background-color: #333; 
            color: white;
            padding: 8px;
            font-size: 10pt;
        }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        .invoice-header-label { font-weight: bold; font-style: italic; color: #777; }
        .footer {
            position: fixed;
            bottom: -30px;
            left: 0;
            right: 0;
            font-size: 8pt;
            color: #999;
            text-align: left;
            border-top: 1px solid #eee;
            padding-top: 5px;
        }
        .footer .right {
            float: right;
        }
    </style>
</head>
<body>
    <center style="margin-bottom: 20px; border-bottom: 2px solid #eee; padding-bottom: 10px;">
        <div class="store-name">{{ $store['name'] }}</div>
        <div class="report-title">LAPORAN AGING {{ $type === 'ar' ? 'PIUTANG' : 'HUTANG' }}</div>
        <div class="period">Per Tanggal: {{ \Carbon\Carbon::now()->format('d F Y') }}</div>
        <div style="font-size: 9pt; margin-top: 5px; font-style: italic; color: #666;">(dalam Mata Uang Rupiah IDR)</div>
    </center>

    <table>
        <thead>
            <tr>
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

                {{-- Invoice Label Row --}}
                @if(count($entity['invoices']) > 0)
                <tr class="invoice-row">
                    <td></td>
                    <td colspan="{{ 3 + count($reportData['buckets']) }}" class="invoice-header-label">Detail Invoice:</td>
                </tr>
                @endif

                {{-- Invoice Detail Rows --}}
                @foreach($entity['invoices'] as $inv)
                <tr class="invoice-row">
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

    <div class="footer">
        Dicetak oleh: {{ $printedBy }}
        <span class="right">Waktu Cetak: {{ $printedAt }}</span>
    </div>
</body>
</html>
