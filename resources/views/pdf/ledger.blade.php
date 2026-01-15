<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Besar (General Ledger)</title>
    <style>
        @page { margin: 20px 30px; }
        body { font-family: sans-serif; font-size: 8pt; color: #333; margin: 0; padding: 0; }
        
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .header-table td { text-align: center; }
        .store-name { font-size: 16pt; font-weight: bold; text-transform: uppercase; margin-bottom: 5px; text-align: center; }
        .report-title { font-size: 12pt; font-weight: bold; text-transform: uppercase; color: #555; text-align: center; }
        .period { font-size: 10pt; margin-top: 5px; color: #666; text-align: center; }
        .currency { font-size: 9pt; margin-top: 5px; font-style: italic; color: #666; text-align: center; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; table-layout: fixed; }
        th, td { padding: 4px 6px; vertical-align: top; border-bottom: 1px solid #eee; overflow: hidden; word-wrap: break-word; }
        
        thead th { 
            background-color: #00BFFF; /* Cyan color matching image */
            color: white; 
            text-align: left; 
            font-weight: normal; 
            padding: 6px 8px;
            font-size: 8pt;
        }
        
        /* Account grouping row */
        .account-header-row td { 
            background-color: #f0f9ff; 
            font-weight: bold; 
            color: #000;
            padding: 6px 8px;
            border-bottom: 1px solid #ddd;
        }

        .transaction-row td {
            font-size: 8pt;
        }
        
        .subtotal-row td { 
            font-weight: bold; 
            border-top: 1px solid #333;
            border-bottom: 1px solid #333;
            background-color: #fff;
            padding: 6px 8px;
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
            border-top: 1px solid #000;
            padding-top: 2px;
        }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td>
                <div class="store-name">{{ $store['name'] }}</div>
                <div class="report-title">BUKU BESAR</div>
                <div class="period">
                    Periode: {{ \Carbon\Carbon::parse($startDate)->format('d F Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}
                </div>
                <div class="currency">(dalam IDR)</div>
            </td>
        </tr>
    </table>
    
    <table>
        <thead>
            <tr>
                <th style="width: 12%">Nama Akun / Tanggal</th>
                <th style="width: 10%">Transaksi</th>
                <th style="width: 10%">Nomor</th>
                <th style="width: 28%">Keterangan</th>
                <th style="width: 10%; text-align: right;">Debit</th>
                <th style="width: 10%; text-align: right;">Kredit</th>
                <th style="width: 10%; text-align: right;">Saldo</th>
                <th style="width: 10%">Tags</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData['data'] as $accountData)
                {{-- Account Header Row --}}
                <tr class="account-header-row">
                    <td colspan="8">({{ $accountData['account']->code }}) {{ $accountData['account']->name }}</td>
                </tr>

                {{-- Opening Balance Row (Optional, based on image check) --}}
                {{-- In user image, it seems they show transactions. If no transactions, they might just show Saldo Akhir. --}}
                {{-- Let's show the lines --}}
                @foreach($accountData['lines'] as $line)
                    <tr class="transaction-row">
                        <td>{{ $line->journalEntry->date->format('d/m/Y') }}</td>
                        <td>{{ $line->journalEntry->source ?? 'General Journal' }}</td>
                        <td class="font-bold">{{ $line->journalEntry->entry_number }}</td>
                        <td>{{ $line->journalEntry->description }} {{ $line->notes ? '- ' . $line->notes : '' }}</td>
                        <td class="text-right">{{ number_format($line->debit, 2, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($line->credit, 2, ',', '.') }}</td>
                        <td class="text-right font-bold">{{ number_format($line->running_balance, 2, ',', '.') }}</td>
                        <td></td>
                    </tr>
                @endforeach

                {{-- Account Subtotal/Saldo Akhir --}}
                <tr class="subtotal-row">
                    <td colspan="4" class="text-right">({{ $accountData['account']->code }}) {{ $accountData['account']->name }} | Saldo Akhir</td>
                    <td class="text-right">{{ number_format($accountData['lines']->sum('debit'), 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($accountData['lines']->sum('credit'), 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($accountData['ending_balance'], 2, ',', '.') }}</td>
                    <td></td>
                </tr>
                
                {{-- Spacing --}}
                <tr><td colspan="8" style="height: 10px; border: none;"></td></tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <div style="float: left;">Buku Besar : {{ $store['name'] }} | Dicetak: {{ $printedBy }}</div>
        <div style="float: right;">{{ $printedAt }}</div>
    </div>
</body>
</html>
