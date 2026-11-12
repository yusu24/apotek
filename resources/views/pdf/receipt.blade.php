<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Struk Transaksi</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            line-height: 1.35;
            width: 100%;
            margin: 0;
            padding: 0;
            color: #000;
        }
        .text-left { text-align: left; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .dashed-line {
            border-bottom: 1px dashed #000;
            margin: 5px 0;
        }
        table { width: 100%; border-collapse: collapse; }
        td, th { vertical-align: top; padding: 1.5px 0; }
        .header { text-align: left; margin-bottom: 4px; }
        .shop-name { font-size: 12px; font-weight: normal; }
        .store-address { font-size: 10.5px; line-height: 1.3; }
        .meta-table { font-size: 10.5px; margin: 5px 0 8px 0; }
        .items-table { font-size: 10.5px; width: 100%; }
        .items-table thead th {
            font-weight: normal;
            text-transform: uppercase;
            padding-bottom: 3px;
            border-bottom: 1px dashed #000;
        }
        .items-table tbody td {
            padding-top: 3px;
            padding-bottom: 2px;
        }
        .totals-table { width: 100%; margin-top: 8px; font-size: 10.5px; }
        .totals-label { text-align: right; width: 55%; padding-right: 8px; }
        .totals-value { text-align: right; width: 45%; }
        .footer {
            margin-top: 10px;
            padding-top: 6px;
            border-top: 1px dashed #000;
            text-align: center;
            font-size: 10px;
            line-height: 1.35;
        }
    </style>
</head>
<body>
    @php
        $ppnRate = \App\Models\Setting::get('pos_ppn_rate', 11);
        $storeName = \App\Models\Setting::get('store_name') ?? config('app.name', 'Apotek');
        $storeAddress = \App\Models\Setting::get('store_address') ?? 'Jl. Raya Apotek No. 123';
        $storePhone = \App\Models\Setting::get('store_phone') ?? '0812-3456-7890';
        $taxId = \App\Models\Setting::get('store_tax_id');
        $companyName = \App\Models\Setting::get('store_bank_holder');
        $footerNote = \App\Models\Setting::get('store_footer_note', "Terima Kasih - Semoga Cepat Sembuh\nBarang yang sudah dibeli\ntidak dapat ditukar/dikembalikan");

        $patientName = $sale->patient_name ?: ($sale->customer->name ?? '-');
        $jenisObat = $sale->saleItems
            ->map(fn($item) => $item->product?->category?->name)
            ->filter()
            ->unique()
            ->implode(', ') ?: 'Obat Bebas';

        $saleDateFormatted = \Carbon\Carbon::parse($sale->date)->format('j M Y') . '  ' . ($sale->created_at ? $sale->created_at->format('H:i') : \Carbon\Carbon::parse($sale->date)->format('H:i'));
        $totalAfterDiscount = max(0, $sale->total_amount - $sale->discount + ($sale->ppn_mode === 'exclusive' ? $sale->tax : 0));
    @endphp

    <div class="header">
        <div class="shop-name">{{ $storeName }}</div>
        <div class="store-address">
            {{ $storeAddress }}<br>
            {{ $storePhone }}
        </div>
    </div>

    <div class="dashed-line"></div>

    <table class="meta-table">
        <tr>
            <td style="width: 34%;">No Faktur</td>
            <td style="width: 3%;">:</td>
            <td>{{ $sale->invoice_no }}</td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td>:</td>
            <td>{{ $saleDateFormatted }}</td>
        </tr>
        <tr>
            <td>Nama Pasien</td>
            <td>:</td>
            <td style="text-transform: uppercase;">{{ $patientName }}</td>
        </tr>
        @if($sale->patient_doctor_name)
        <tr>
            <td>Dokter</td>
            <td>:</td>
            <td>{{ $sale->patient_doctor_name }}</td>
        </tr>
        @endif
        <tr>
            <td>Jns Obat</td>
            <td>:</td>
            <td>{{ $jenisObat }}</td>
        </tr>
        <tr>
            <td>Petugas</td>
            <td>:</td>
            <td style="text-transform: uppercase;">{{ $sale->user->name ?? '-' }}</td>
        </tr>
        @if($sale->payment_method === 'tempo' && $sale->receivables)
        <tr>
            <td>Jatuh Tempo</td>
            <td>:</td>
            <td>{{ $sale->receivables->due_date ? $sale->receivables->due_date->format('d/m/Y') : '-' }}</td>
        </tr>
        @endif
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th class="text-left" style="width: 10%;">NO</th>
                <th class="text-left" style="width: 46%;">NAMA BARANG</th>
                <th class="text-center" style="width: 20%;">JUMLAH</th>
                <th class="text-right" style="width: 24%;">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->saleItems as $index => $item)
                @php
                    $qtyFormatted = number_format($item->quantity, $item->quantity == floor($item->quantity) ? 0 : 2, ',', '.');
                    $unitShort = $item->unit->name ?? '';
                @endphp
                <tr>
                    <td class="text-left">{{ $index + 1 }}.</td>
                    <td class="text-left" style="padding-right: 4px;">
                        {{ $item->product->name }}
                        @if($item->notes)
                            <div style="font-size: 9px; color: #333;">({{ $item->notes }})</div>
                        @endif
                        @if($item->discount_amount > 0)
                            <div style="font-size: 9px; color: #333;">(Disc: -{{ number_format($item->discount_amount * $item->quantity, 0, ',', '.') }})</div>
                        @endif
                    </td>
                    <td class="text-center">{{ $qtyFormatted }} {{ $unitShort }}</td>
                    <td class="text-right">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals-table">
        <tr>
            <td class="totals-label">Sub Total:</td>
            <td class="totals-value">{{ number_format($sale->total_amount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="totals-label">Diskon:</td>
            <td class="totals-value">{{ number_format($sale->discount, 0, ',', '.') }}</td>
        </tr>
        @if($sale->ppn_mode === 'exclusive' && $sale->tax > 0)
        <tr>
            <td class="totals-label">PPN ({{ $ppnRate }}%):</td>
            <td class="totals-value">{{ number_format($sale->tax, 0, ',', '.') }}</td>
        </tr>
        @endif
        <tr>
            <td class="totals-label">Total:</td>
            <td class="totals-value">{{ number_format($totalAfterDiscount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="totals-label">Pembulatan:</td>
            <td class="totals-value">{{ number_format($sale->rounding, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="totals-label">Grand Total:</td>
            <td class="totals-value">{{ number_format($sale->grand_total, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="totals-label">Donasi:</td>
            <td class="totals-value"></td>
        </tr>
        <tr>
            <td class="totals-label">Bayar:</td>
            <td class="totals-value">{{ number_format($sale->cash_amount, 0, ',', '.') }}</td>
        </tr>
        @if($sale->payment_method === 'cash')
            @if($sale->change_amount > 0)
            <tr>
                <td class="totals-label">Kembali:</td>
                <td class="totals-value">{{ number_format($sale->change_amount, 0, ',', '.') }}</td>
            </tr>
            @else
            <tr>
                <td class="totals-label">Nomor Kartu:</td>
                <td class="totals-value">TUNAI</td>
            </tr>
            @endif
        @elseif($sale->payment_method === 'tempo')
            <tr>
                <td class="totals-label">Nomor Kartu:</td>
                <td class="totals-value">TEMPO</td>
            </tr>
            @if($sale->receivables)
            <tr>
                <td class="totals-label">Sisa Tagihan:</td>
                <td class="totals-value">{{ number_format($sale->receivables->remaining_balance, 0, ',', '.') }}</td>
            </tr>
            @endif
        @else
            <tr>
                <td class="totals-label">Nomor Kartu:</td>
                <td class="totals-value">{{ strtoupper($sale->payment_method) }}</td>
            </tr>
        @endif
    </table>

    <div class="footer">
        @if($sale->ppn_mode !== 'exclusive')
            <div>Harga Sudah Termasuk PPN</div>
        @endif
        @if($companyName)
            <div style="text-transform: uppercase;">{{ $companyName }}</div>
        @endif
        @if($taxId)
            <div>NPWP : {{ $taxId }}</div>
        @endif
        <div>{{ $storeAddress }}</div>
        <div>{!! nl2br(e($footerNote)) !!}</div>
    </div>
</body>
</html>
