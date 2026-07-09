<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Besar (Standar)</title>
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
            border-bottom: 0.5pt solid #eee;
        }

        /* Hierarchy Levels / Rows */
        .account-header-row td {
            font-weight: bold;
            background-color: #eef2f9;
            color: #1e40af;
            padding-top: 6px;
            padding-bottom: 6px;
        }

        .transaction-row td {
            padding: 4px 6px;
        }

        .summary-label { font-weight: bold; }

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
    @php
        $isSummaryView = empty($accountId) && empty($search);
        $hasSearch = !empty($search);
    @endphp

    <div class="report-header">
        <div class="store-name">{{ trim($store['name']) }}</div>
        <div class="report-title">BUKU BESAR{{ $isSummaryView ? ' (STANDAR)' : '' }}</div>
        <div class="period-info">
            Untuk Periode {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
        </div>
        @if($hasSearch)
            <div class="period-info font-bold">Pencarian: "{{ $search }}"</div>
        @endif
    </div>

    @if($isSummaryView)
        <table>
            <thead>
                <tr class="column-headers">
                    <th style="width: 15%">Kode Akun</th>
                    <th style="width: 35%">Nama Akun</th>
                    <th style="width: 12.5%; text-align: right;">Saldo Awal</th>
                    <th style="width: 12.5%; text-align: right;">Debit</th>
                    <th style="width: 12.5%; text-align: right;">Kredit</th>
                    <th style="width: 12.5%; text-align: right;">Saldo Akhir</th>
                </tr>
            </thead>
            <tbody>
                @php 
                    $totalOpening = 0; 
                    $totalDebit = 0; 
                    $totalCredit = 0; 
                    $totalEnding = 0; 
                @endphp
                @foreach($reportData['data'] as $accountData)
                    @php
                        $linesDebit = $accountData['lines']->sum('debit');
                        $linesCredit = $accountData['lines']->sum('credit');
                        $totalOpening += $accountData['opening_balance'];
                        $totalDebit += $linesDebit;
                        $totalCredit += $linesCredit;
                        $totalEnding += $accountData['ending_balance'];
                    @endphp
                    <tr class="transaction-row">
                        <td>{{ $accountData['account']->code }}</td>
                        <td>{{ $accountData['account']->name }}</td>
                        <td class="text-right">{{ format_accounting_standard($accountData['opening_balance']) }}</td>
                        <td class="text-right">{{ format_accounting_standard($linesDebit) }}</td>
                        <td class="text-right">{{ format_accounting_standard($linesCredit) }}</td>
                        <td class="text-right font-bold">{{ format_accounting_standard($accountData['ending_balance']) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="grand-total-label">
                    <td colspan="2" class="text-right">TOTAL</td>
                    <td class="grand-total-value">{{ format_accounting_standard($totalOpening) }}</td>
                    <td class="grand-total-value">{{ format_accounting_standard($totalDebit) }}</td>
                    <td class="grand-total-value">{{ format_accounting_standard($totalCredit) }}</td>
                    <td class="grand-total-value">{{ format_accounting_standard($totalEnding) }}</td>
                </tr>
            </tfoot>
        </table>
    @else
        <table>
            <thead>
                <tr class="column-headers">
                    <th style="width: 10%">Tanggal</th>
                    <th style="width: 10%">Transaksi</th>
                    <th style="width: 12%">Nomor</th>
                    <th style="width: 38%">Keterangan</th>
                    <th style="width: 10%; text-align: right;">Debit</th>
                    <th style="width: 10%; text-align: right;">Kredit</th>
                    <th style="width: 10%; text-align: right;">Saldo</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reportData['data'] as $accountData)
                    @php
                        $hasLines = count($accountData['lines']) > 0;
                        $hasActivity = $hasLines || abs($accountData['opening_balance']) > 0.01;
                        $sumDebit = $accountData['lines']->sum('debit');
                        $sumCredit = $accountData['lines']->sum('credit');
                    @endphp

                    @if(($hasSearch && $hasLines) || (!$hasSearch && $hasActivity))
                        <tr class="account-header-row">
                            <td colspan="7">({{ $accountData['account']->code }}) {{ $accountData['account']->name }}</td>
                        </tr>

                        @if(!$hasSearch)
                            <tr class="transaction-row italic">
                                <td colspan="4">Saldo Awal Periode</td>
                                <td class="text-right">-</td>
                                <td class="text-right">-</td>
                                <td class="text-right font-bold">{{ format_accounting_standard($accountData['opening_balance']) }}</td>
                            </tr>
                        @endif

                        @foreach($accountData['lines'] as $line)
                            <tr class="transaction-row">
                                <td>{{ $line->journalEntry->date->format('d/m/Y') }}</td>
                                <td>{{ $line->journalEntry->source ?? 'Jurnal Umum' }}</td>
                                <td class="font-bold">{{ $line->journalEntry->entry_number }}</td>
                                <td>{{ $line->journalEntry->description }} {{ $line->notes ? '- ' . $line->notes : '' }}</td>
                                <td class="text-right">{{ format_accounting_standard($line->debit) }}</td>
                                <td class="text-right">{{ format_accounting_standard($line->credit) }}</td>
                                <td class="text-right font-bold">{{ format_accounting_standard($line->running_balance) }}</td>
                            </tr>
                        @endforeach

                        <tr class="grand-total-label">
                            <td colspan="4" class="text-right">SALDO AKHIR</td>
                            <td class="grand-total-value">{{ format_accounting_standard($sumDebit) }}</td>
                            <td class="grand-total-value">{{ format_accounting_standard($sumCredit) }}</td>
                            <td class="grand-total-value">{{ format_accounting_standard($accountData['ending_balance']) }}</td>
                        </tr>
                        
                        <tr><td colspan="7" style="height: 10px; border: none;"></td></tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    @endif

</body>
</html>
