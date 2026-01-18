<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Besar (General Ledger)</title>
    <style>
        @page { margin: 20px 40px 60px 40px; }
        body { font-family: sans-serif; font-size: 10pt; color: #333; }
        
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; text-align: center; }
        .header-table td { text-align: center; }
        .store-name { font-size: 16pt; font-weight: bold; text-transform: uppercase; margin-bottom: 5px; text-align: center; width: 100%; }
        .report-title { font-size: 12pt; font-weight: bold; text-transform: uppercase; color: #555; text-align: center; width: 100%; }
        .period { font-size: 10pt; margin-top: 5px; color: #666; text-align: center; width: 100%; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 6px 8px; vertical-align: top; border-bottom: 1px solid #eee; word-wrap: break-word; }
        
        thead th { 
            background-color: #00BFFF; 
            color: white; 
            text-align: left; 
            font-weight: bold; 
            padding: 8px 10px;
            border-bottom: 2px solid #009ACD;
        }
        
        /* Account grouping row */
        .account-header-row td { 
            background-color: #f3f4f6; 
            font-weight: bold; 
            color: #111;
            padding: 8px 10px;
            border-bottom: 1px solid #ddd;
        }

        .transaction-row td {
            font-size: 9pt;
        }
        
        .subtotal-row td { 
            font-weight: bold; 
            background-color: #f0f9ff; 
            color: #000;
            border-top: 1px solid #cbd5e1;
            padding: 8px 10px;
        }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        
        .footer {
            position: fixed;
            bottom: -30px;
            width: 100%;
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

    @php
        $isSummaryView = empty($accountId) && empty($search);
        $hasSearch = !empty($search);
    @endphp

    <div style="width: 100%; text-align: center; margin-bottom: 30px;">
        <table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin: 0 auto;">
            <tr>
                <td align="center" style="text-align: center;">
                    <div style="font-size: 16pt; font-weight: bold; text-transform: uppercase;">{{ $store['name'] }}</div>
                    <div style="font-size: 12pt; font-weight: bold; text-transform: uppercase; color: #555; margin-top: 5px;">BUKU BESAR{{ $isSummaryView ? ' (Ringkasan)' : '' }}</div>
                    <div style="font-size: 10pt; color: #666; margin-top: 5px;">
                        Periode: {{ \Carbon\Carbon::parse($startDate)->format('d F Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}
                    </div>
                    @if($hasSearch)
                        <div style="font-size: 10pt; margin-top: 5px; font-weight: bold; color: #000;">
                            Pencarian: "{{ $search }}"
                        </div>
                    @endif
                    <div style="font-size: 9pt; margin-top: 5px; font-style: italic; color: #666;">(dalam Mata Uang Rupiah IDR)</div>
                </td>
            </tr>
        </table>
    </div>
    
    @if($isSummaryView)
        {{-- Summary View Table --}}
        <table>
            <thead>
                <tr>
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
                        <td class="text-right">{{ format_accounting($accountData['opening_balance']) }}</td>
                        <td class="text-right">{{ format_accounting($linesDebit) }}</td>
                        <td class="text-right">{{ format_accounting($linesCredit) }}</td>
                        <td class="text-right font-bold">{{ format_accounting($accountData['ending_balance']) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="subtotal-row">
                    <td colspan="2" class="text-right uppercase">Total Seluruh Akun</td>
                    <td class="text-right">{{ format_accounting($totalOpening) }}</td>
                    <td class="text-right">{{ format_accounting($totalDebit) }}</td>
                    <td class="text-right">{{ format_accounting($totalCredit) }}</td>
                    <td class="text-right">{{ format_accounting($totalEnding) }}</td>
                </tr>
            </tfoot>
        </table>
    @else
        {{-- Detailed View Table --}}
        <table>
            <thead>
            <tr>
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
                    {{-- Account Header Row --}}
                    <tr class="account-header-row">
                        <td colspan="7">({{ $accountData['account']->code }}) {{ $accountData['account']->name }}</td>
                    </tr>

                    @if(!$hasSearch)
                        {{-- Opening Balance Row --}}
                        <tr class="transaction-row italic">
                            <td colspan="4">Saldo Awal Periode</td>
                            <td class="text-right">-</td>
                            <td class="text-right">-</td>
                            <td class="text-right font-bold">{{ format_accounting($accountData['opening_balance']) }}</td>
                        </tr>
                    @endif

                    @foreach($accountData['lines'] as $line)
                        <tr class="transaction-row">
                            <td>{{ $line->journalEntry->date->format('d/m/Y') }}</td>
                            <td>{{ $line->journalEntry->source ?? 'General Journal' }}</td>
                            <td class="font-bold">{{ $line->journalEntry->entry_number }}</td>
                            <td>{{ $line->journalEntry->description }} {{ $line->notes ? '- ' . $line->notes : '' }}</td>
                            <td class="text-right">{{ format_accounting($line->debit) }}</td>
                            <td class="text-right">{{ format_accounting($line->credit) }}</td>
                            <td class="text-right font-bold">{{ format_accounting($line->running_balance) }}</td>
                        </tr>
                    @endforeach

                    {{-- Account Subtotal/Saldo Akhir --}}
                    <tr class="subtotal-row">
                        @if($hasSearch)
                            <td colspan="4" class="text-right uppercase">Total Pencarian untuk Akun Ini</td>
                            <td class="text-right">{{ format_accounting($sumDebit) }}</td>
                            <td class="text-right">{{ format_accounting($sumCredit) }}</td>
                            <td class="text-right">{{ format_accounting($sumDebit - $sumCredit) }}</td>
                        @else
                            <td colspan="4" class="text-right uppercase">Saldo Akhir ({{ $accountData['account']->code }})</td>
                            <td class="text-right">{{ format_accounting($sumDebit) }}</td>
                            <td class="text-right">{{ format_accounting($sumCredit) }}</td>
                            <td class="text-right">{{ format_accounting($accountData['ending_balance']) }}</td>
                        @endif
                    </tr>
                    
                    {{-- Spacing --}}
                    <tr><td colspan="7" style="height: 10px; border: none;"></td></tr>
                @endif
            @endforeach
            @if(empty(collect($reportData['data'])->filter(function($accountData) use ($hasSearch) {
                $hasLines = count($accountData['lines']) > 0;
                $hasActivity = $hasLines || abs($accountData['opening_balance']) > 0.01;
                return ($hasSearch && $hasLines) || (!$hasSearch && $hasActivity);
            })->count()))
                <tr>
                    <td colspan="7" class="text-center italic" style="padding: 40px;">Data Tidak Ditemukan</td>
                </tr>
            @endif
        </tbody>
        </table>
    @endif

    @php
    function format_accounting($number) {
        if ($number < 0) {
            return '( ' . number_format(abs($number), 0, ',', '.') . ' )';
        }
        return number_format($number, 0, ',', '.');
    }
    @endphp
    <div class="footer">
        Dicetak oleh: {{ $printedBy }}
        <span class="right">Waktu Cetak: {{ $printedAt }}</span>
    </div>
</body>
</html>
