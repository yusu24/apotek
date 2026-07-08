<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan PPN (Standar)</title>
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
        }

        .section-header {
            font-weight: bold;
            background-color: #eef2f9;
            color: #1e40af;
            padding: 6px;
        }

        .transaction-row td {
            border-bottom: 0.5pt solid #eee;
            padding: 4px 6px;
        }

        .total-row td {
            font-weight: bold;
            background-color: #f3f4f6;
            border-top: 1pt solid #999;
            padding-top: 6px;
            padding-bottom: 6px;
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
        <div class="report-title">LAPORAN PPN (STANDAR)</div>
        <div class="period-info">Untuk Periode {{ $monthName }}</div>
        <div class="period-info">(dalam Mata Uang Rupiah IDR)</div>
    </div>

    {{-- Summary Section --}}
    <div class="section-header">RINGKASAN PAJAK</div>
    <table>
        <thead>
            <tr class="column-headers">
                <th style="width: 70%">Deskripsi</th>
                <th style="width: 30%; text-align: right;">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <tr class="transaction-row">
                <td>Total PPN Keluaran (Output Tax)</td>
                <td class="text-right">Rp. {{ number_format($data['total_ppn_keluaran'], 0, ',', '.') }},-</td>
            </tr>
            <tr class="transaction-row">
                <td>Total PPN Masukan (Input Tax)</td>
                <td class="text-right">Rp. {{ number_format($data['total_ppn_masukan'], 0, ',', '.') }},-</td>
            </tr>
            <tr class="total-row">
                <td>
                    STATUS: 
                    @if($data['status'] === 'kurang_bayar')
                        <span style="color: #ef4444;">KURANG BAYAR</span>
                    @elseif($data['status'] === 'lebih_bayar')
                        <span style="color: #f59e0b;">LEBIH BAYAR</span>
                    @else
                        <span style="color: #10b981;">NIHIL</span>
                    @endif
                </td>
                <td class="text-right" style="font-size: 11pt;">Rp. {{ number_format(abs($data['kurang_lebih']), 0, ',', '.') }},-</td>
            </tr>
        </tbody>
    </table>

    {{-- A. PPN KELUARAN --}}
    <div class="section-header" style="margin-top: 20px;">A. PPN KELUARAN (OUTPUT TAX) - PENJUALAN</div>
    <table>
        <thead>
            <tr class="column-headers">
                <th style="width: 5%">No</th>
                <th style="width: 15%">Tanggal</th>
                <th style="width: 20%">No. Invoice</th>
                <th class="text-right" style="width: 20%">DPP</th>
                <th class="text-right" style="width: 20%">PPN 11%</th>
                <th class="text-right" style="width: 20%">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data['ppn_keluaran_details'] as $index => $sale)
            <tr class="transaction-row">
                <td>{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($sale->date)->format('d/m/Y') }}</td>
                <td>{{ $sale->invoice_no }}</td>
                <td class="text-right">{{ number_format($sale->dpp, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($sale->ppn_amount, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($sale->grand_total, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center italic" style="padding: 15px; color: #777;">Tidak ada transaksi penjualan dengan PPN pada periode ini.</td>
            </tr>
            @endforelse
            
            @if($data['ppn_keluaran_details']->count() > 0)
            <tr class="total-row">
                <td colspan="3" class="text-right">TOTAL PPN KELUARAN</td>
                <td class="text-right">{{ number_format($data['total_dpp_keluaran'], 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($data['total_ppn_keluaran'], 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($data['total_dpp_keluaran'] + $data['total_ppn_keluaran'], 0, ',', '.') }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    {{-- B. PPN MASUKAN --}}
    <div class="section-header" style="margin-top: 20px;">B. PPN MASUKAN (INPUT TAX) - PEMBELIAN</div>
    <table>
        <thead>
            <tr class="column-headers">
                <th style="width: 5%">No</th>
                <th style="width: 15%">Tanggal</th>
                <th style="width: 20%">No. Surat Jalan</th>
                <th class="text-right" style="width: 20%">DPP</th>
                <th class="text-right" style="width: 20%">PPN 11%</th>
                <th class="text-right" style="width: 20%">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data['ppn_masukan_details'] as $index => $purchase)
            <tr class="transaction-row">
                <td>{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($purchase->date)->format('d/m/Y') }}</td>
                <td>{{ $purchase->delivery_note_number }}</td>
                <td class="text-right">{{ number_format($purchase->dpp, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($purchase->ppn_amount, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($purchase->dpp + $purchase->ppn_amount, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center italic" style="padding: 15px; color: #777;">Tidak ada transaksi pembelian dengan PPN pada periode ini.</td>
            </tr>
            @endforelse
            
            @if($data['ppn_masukan_details']->count() > 0)
            <tr class="total-row">
                <td colspan="3" class="text-right">TOTAL PPN MASUKAN</td>
                <td class="text-right">{{ number_format($data['total_dpp_masukan'], 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($data['total_ppn_masukan'], 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($data['total_dpp_masukan'] + $data['total_ppn_masukan'], 0, ',', '.') }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    </body>
</html>
