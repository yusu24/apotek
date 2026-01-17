<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan PPN - {{ $monthName }}</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; color: #333; }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .header-table td { text-align: center; }
        .store-name { font-size: 16pt; font-weight: bold; text-transform: uppercase; margin-bottom: 5px; text-align: center; }
        .report-title { font-size: 12pt; font-weight: bold; text-transform: uppercase; color: #555; text-align: center; }
        .period { font-size: 10pt; margin-top: 5px; color: #666; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 8px 10px; }
        thead th { background-color: #00BFFF; color: white; text-align: left; font-weight: bold; border-bottom: 2px solid #009ACD; }
        
        .section-header { 
            background-color: #f3f4f6; 
            font-weight: bold; 
            padding-top: 15px; 
            padding-bottom: 5px;
            color: #111;
            border-bottom: 1px solid #ddd;
        }
        
        .sub-row td { padding-left: 10px; border-bottom: 1px solid #eee; font-size: 9pt; }
        
        .total-row td { 
            font-weight: bold; 
            background-color: #f0f9ff; 
            color: #000;
            border-top: 1px solid #cbd5e1;
            padding-top: 8px;
            padding-bottom: 8px;
        }
        
        /* Specific Status Badges adapted for PDF */
        .status-badge { padding: 2px 6px; border-radius: 3px; font-size: 9pt; font-weight: bold; color: white; display: inline-block; }
        .status-kurang { background-color: #ef4444; } /* Red */
        .status-lebih { background-color: #f59e0b; } /* Amber */
        .status-nihil { background-color: #10b981; } /* Green */

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        .footer { 
            margin-top: 50px; 
            border-top: 1px solid #eee; 
            padding-top: 10px; 
            font-size: 8pt; 
            color: #999; 
            display: flex; 
            justify-content: space-between;
        }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td>
                <div class="store-name">{{ $store['name'] }}</div>
                <div class="report-title">LAPORAN PAJAK PERTAMBAHAN NILAI (PPN)</div>
                <div class="period">
                    Periode: {{ $monthName }}
                </div>
                <div style="font-size: 9pt; margin-top: 5px; font-style: italic;">(dalam Mata Uang Rupiah IDR)</div>
            </td>
        </tr>
    </table>

    {{-- Summary Section --}}
    <table style="width: 100%; margin-bottom: 20px;">
        <thead>
            <tr>
                <th colspan="2">RINGKASAN PAJAK</th>
            </tr>
        </thead>
        <tbody>
            <tr class="sub-row">
                <td>Total PPN Keluaran (Output Tax)</td>
                <td class="text-right" style="font-weight: bold;">Rp {{ number_format($data['total_ppn_keluaran'], 0, ',', '.') }}</td>
            </tr>
            <tr class="sub-row">
                <td>Total PPN Masukan (Input Tax)</td>
                <td class="text-right" style="font-weight: bold;">Rp {{ number_format($data['total_ppn_masukan'], 0, ',', '.') }}</td>
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
                <td class="text-right" style="font-size: 11pt;">Rp {{ number_format(abs($data['kurang_lebih']), 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    {{-- A. PPN KELUARAN --}}
    <div class="section-header" style="margin-top: 20px; padding: 10px; border-bottom: 2px solid #ccc;">A. PPN KELUARAN (OUTPUT TAX) - PENJUALAN</div>
    <table>
        <thead>
            <tr>
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
            <tr class="sub-row">
                <td>{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($sale->date)->format('d/m/Y') }}</td>
                <td>{{ $sale->invoice_no }}</td>
                <td class="text-right">{{ number_format($sale->dpp, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($sale->ppn_amount, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($sale->grand_total, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center" style="padding: 15px; font-style: italic; color: #777;">Tidak ada transaksi penjualan dengan PPN pada periode ini.</td>
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
    <div class="section-header" style="margin-top: 20px; padding: 10px; border-bottom: 2px solid #ccc;">B. PPN MASUKAN (INPUT TAX) - PEMBELIAN</div>
    <table>
        <thead>
            <tr>
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
            <tr class="sub-row">
                <td>{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($purchase->date)->format('d/m/Y') }}</td>
                <td>{{ $purchase->delivery_note_number }}</td>
                <td class="text-right">{{ number_format($purchase->dpp, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($purchase->ppn_amount, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($purchase->dpp + $purchase->ppn_amount, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center" style="padding: 15px; font-style: italic; color: #777;">Tidak ada transaksi pembelian dengan PPN pada periode ini.</td>
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

    <div class="footer">
        <div>Dicetak oleh: {{ $printedBy }}</div>
        <div>Waktu cetak: {{ $printedAt }}</div>
    </div>
</body>
</html>
