<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan PPN - {{ $monthName }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #333;
        }
        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 12px;
            color: #666;
        }
        .summary {
            margin-bottom: 20px;
            background: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 12px;
        }
        .summary-row strong {
            font-weight: bold;
        }
        .summary-total {
            border-top: 2px solid #333;
            padding-top: 8px;
            margin-top: 8px;
            font-size: 13px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .section-title {
            background: #333;
            color: white;
            padding: 8px 10px;
            font-weight: bold;
            font-size: 12px;
            margin-top: 15px;
            margin-bottom: 5px;
        }
        th {
            background: #f0f0f0;
            padding: 8px 6px;
            text-align: left;
            font-weight: bold;
            font-size: 10px;
            border: 1px solid #ddd;
        }
        th.text-right, td.text-right {
            text-align: right;
        }
        td {
            padding: 6px 6px;
            border: 1px solid #ddd;
            font-size: 10px;
        }
        tfoot td {
            background: #f9f9f9;
            font-weight: bold;
            font-size: 11px;
        }
        .footer {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            text-align: center;
            width: 45%;
        }
        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #333;
            padding-top: 5px;
        }
        .empty-row {
            text-align: center;
            color: #999;
            font-style: italic;
            padding: 20px;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 10px;
        }
        .status-kurang {
            background: #fee;
            color: #c00;
        }
        .status-lebih {
            background: #ffc;
            color: #c60;
        }
        .status-nihil {
            background: #efe;
            color: #060;
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <h1>LAPORAN PAJAK PERTAMBAHAN NILAI (PPN)</h1>
        <p>Periode: {{ $monthName }}</p>
        <p style="font-size: 10px; color: #999;">Dicetak: {{ now()->format('d/m/Y H:i') }} oleh {{ auth()->user()->name }}</p>
    </div>

    {{-- Summary --}}
    <div class="summary">
        <div class="summary-row">
            <span>Total PPN Keluaran (Output Tax):</span>
            <strong>Rp {{ number_format($data['total_ppn_keluaran'], 0, ',', '.') }}</strong>
        </div>
        <div class="summary-row">
            <span>Total PPN Masukan (Input Tax):</span>
            <strong>Rp {{ number_format($data['total_ppn_masukan'], 0, ',', '.') }}</strong>
        </div>
        <div class="summary-total summary-row">
            <span>
                @if($data['status'] === 'kurang_bayar')
                    <span class="status-badge status-kurang">KURANG BAYAR</span>
                @elseif($data['status'] === 'lebih_bayar')
                    <span class="status-badge status-lebih">LEBIH BAYAR</span>
                @else
                    <span class="status-badge status-nihil">NIHIL</span>
                @endif
            </span>
            <strong style="font-size: 14px;">Rp {{ number_format(abs($data['kurang_lebih']), 0, ',', '.') }}</strong>
        </div>
    </div>

    {{-- PPN Keluaran --}}
    <div class="section-title">A. PPN KELUARAN (OUTPUT TAX) - PENJUALAN</div>
    <table>
        <thead>
            <tr>
                <th style="width: 8%;">No</th>
                <th style="width: 15%;">Tanggal</th>
                <th style="width: 22%;">No. Invoice</th>
                <th class="text-right" style="width: 20%;">DPP</th>
                <th class="text-right" style="width: 20%;">PPN 11%</th>
                <th class="text-right" style="width: 15%;">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data['ppn_keluaran_details'] as $index => $sale)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($sale->date)->format('d/m/Y') }}</td>
                <td>{{ $sale->invoice_no }}</td>
                <td class="text-right">{{ number_format($sale->dpp, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($sale->ppn_amount, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($sale->grand_total, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="empty-row">Tidak ada transaksi penjualan dengan PPN pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
        @if($data['ppn_keluaran_details']->count() > 0)
        <tfoot>
            <tr>
                <td colspan="3" style="text-align: right;">TOTAL PPN KELUARAN:</td>
                <td class="text-right">{{ number_format($data['total_dpp_keluaran'], 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($data['total_ppn_keluaran'], 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($data['total_dpp_keluaran'] + $data['total_ppn_keluaran'], 0, ',', '.') }}</td>
            </tr>
        </tfoot>
        @endif
    </table>

    {{-- PPN Masukan --}}
    <div class="section-title">B. PPN MASUKAN (INPUT TAX) - PEMBELIAN</div>
    <table>
        <thead>
            <tr>
                <th style="width: 8%;">No</th>
                <th style="width: 15%;">Tanggal</th>
                <th style="width: 22%;">No. Surat Jalan</th>
                <th class="text-right" style="width: 20%;">DPP</th>
                <th class="text-right" style="width: 20%;">PPN 11%</th>
                <th class="text-right" style="width: 15%;">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data['ppn_masukan_details'] as $index => $purchase)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($purchase->date)->format('d/m/Y') }}</td>
                <td>{{ $purchase->delivery_note_number }}</td>
                <td class="text-right">{{ number_format($purchase->dpp, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($purchase->ppn_amount, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($purchase->dpp + $purchase->ppn_amount, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="empty-row">Tidak ada transaksi pembelian dengan PPN pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
        @if($data['ppn_masukan_details']->count() > 0)
        <tfoot>
            <tr>
                <td colspan="3" style="text-align: right;">TOTAL PPN MASUKAN:</td>
                <td class="text-right">{{ number_format($data['total_dpp_masukan'], 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($data['total_ppn_masukan'], 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($data['total_dpp_masukan'] + $data['total_ppn_masukan'], 0, ',', '.') }}</td>
            </tr>
        </tfoot>
        @endif
    </table>

    {{-- Footer / Signature --}}
    <div class="footer">
        <div class="signature-box">
            <div>Manajer</div>
            <div class="signature-line">(...........................)</div>
        </div>
        <div class="signature-box">
            <div>Petugas Keuangan</div>
            <div class="signature-line">(...........................)</div>
        </div>
    </div>
</body>
</html>
