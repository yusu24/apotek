<div x-data x-init="setTimeout(() => window.print(), 1000)">
    <style>
        body, html {
            background-color: #fff !important;
            color: #000 !important;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            width: 58mm;
            margin: 0;
            padding: 5px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .border-t { border-top: 1px dashed #000; margin: 5px 0; }
        .border-b { border-bottom: 1px dashed #000; margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; }
        td { vertical-align: top; }
        .header { margin-bottom: 10px; }
        .shop-name { font-size: 14px; font-weight: bold; text-transform: uppercase; }
        .meta { font-size: 10px; margin-bottom: 5px; }
        @media print { .no-print { display: none; } }
    </style>

    <div class="header text-center">
        <div class="shop-name">{{ config('app.name', 'Apotek') }}</div>
        <div>Jl. Raya Apotek No. 123</div>
        <div>Telp: 0812-3456-7890</div>
    </div>

    <div class="meta border-b">
        <div>No: {{ $sale->invoice_no }}</div>
        <div>Tgl: {{ $sale->date }}</div>
        <div>Kasir: {{ $sale->user->name }}</div>
    </div>

    <div class="items">
        <table>
            @foreach($sale->saleItems as $item)
                <tr>
                    <td colspan="2">{{ $item->product->name }}</td>
                </tr>
                <tr>
                    <td>{{ $item->quantity }} x {{ number_format($item->sell_price, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @if($item->discount_amount > 0)
                <tr>
                    <td class="text-xs" colspan="2">(Disc: -{{ number_format($item->discount_amount * $item->quantity, 0, ',', '.') }})</td>
                </tr>
                @endif
            @endforeach
        </table>
    </div>

    <div class="border-t"></div>

    <div class="totals">
        <table>
            <tr>
                <td>Subtotal</td>
                <td class="text-right">{{ number_format($sale->total_amount, 0, ',', '.') }}</td>
            </tr>
            @if($sale->discount > 0)
            <tr>
                <td>Diskon Global</td>
                <td class="text-right">-{{ number_format($sale->discount, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr>
                <td>PPN (12%)</td>
                <td class="text-right">{{ number_format($sale->tax, 0, ',', '.') }}</td>
            </tr>
            @if($sale->rounding != 0)
            <tr>
                <td>Pembulatan</td>
                <td class="text-right">{{ number_format($sale->rounding, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr class="font-bold border-t">
                <td style="padding-top: 5px;">TOTAL</td>
                <td class="text-right" style="padding-top: 5px;">{{ number_format($sale->grand_total, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Bayar ({{ ucfirst($sale->payment_method) }})</td>
                <td class="text-right">{{ number_format($sale->cash_amount, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Kembali</td>
                <td class="text-right">{{ number_format($sale->change_amount, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div class="border-t text-center" style="margin-top: 10px; font-size: 10px;">
        Terima Kasih atas Kunjungan Anda<br>
        Semoga Lekas Sembuh
    </div>

    <div class="no-print" style="margin-top: 20px; text-center">
        <button @click="window.print()" style="width: 100%; padding: 12px; margin-bottom: 10px; background: #000; color: #fff; border: none; border-radius: 5px; font-weight: bold; cursor: pointer;">CETAK STRUK</button>
        <button onclick="window.location.href='{{ route('pos.cashier') }}'" style="width: 100%; padding: 12px; background: #ddd; color: #000; border: none; border-radius: 5px; font-weight: bold; cursor: pointer;">KEMBALI KE KASIR</button>
    </div>
</div>
