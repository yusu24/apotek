<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Aging {{ $type === 'ar' ? 'Piutang' : 'Hutang' }} (Standar)</title>
    <style>
        @page { size: A4 landscape; margin: 1cm 1.2cm; }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12pt;
            line-height: 1.4;
            color: #1a1a1a;
            margin: 0;
            padding: 0;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        .italic { font-style: italic; }

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

        .column-headers th {
            padding: 6px;
            background-color: #1e40af;
            color: #ffffff;
            font-weight: bold;
            text-align: left;
        }

        td {
            padding: 4px 6px;
            vertical-align: top;
        }

        .entity-row td {
            font-weight: bold;
            background-color: #eef2f9;
            color: #1e40af;
            padding-top: 6px;
            padding-bottom: 6px;
        }

        .transaction-row td {
            border-bottom: 0.5pt solid #eee;
            padding: 4px 6px;
        }

        .total-row td {
            font-weight: bold;
            background-color: #1e40af;
            color: #ffffff;
            padding: 8px 6px;
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
        <div class="report-title">LAPORAN AGING {{ $type === 'ar' ? 'PIUTANG' : 'HUTANG' }}</div>
        <div class="period-info">Per Tanggal: {{ \Carbon\Carbon::now()->format('d/m/Y') }}</div>
        <div class="period-info">(dalam Mata Uang Rupiah IDR)</div>
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
