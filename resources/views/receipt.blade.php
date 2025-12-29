<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Transaksi</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 11px;
            width: 58mm;
            margin: 0 auto;
            padding: 10px;
            color: #000;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .border-t { border-top: 1px dashed #000; margin: 5px 0; padding-top: 5px; }
        .border-b { border-bottom: 1px dashed #000; margin: 5px 0; padding-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; }
        td { vertical-align: top; padding: 2px 0; }
        .header { margin-bottom: 10px; }
        .shop-name { font-size: 14px; font-weight: bold; text-transform: uppercase; }
        .meta { font-size: 10px; margin: 5px 0; }
        @media print { 
            .no-print { display: none; }
            body { width: 58mm; }
        }
    </style>
</head>
<body>
    <div class="header text-center">
        <div class="shop-name">{{ config('app.name', 'APOTEK') }}</div>
        <div>Jl. Raya Apotek No. 123</div>
        <div>Telp: 0812-3456-7890</div>
    </div>

    <div class="meta border-b">
        <div>No: {{ $sale->invoice_no }}</div>
        <div>Tgl: {{ $sale->date->format('d/m/Y H:i') }}</div>
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
                    <td colspan="2" style="font-size: 10px; color: #666;">Disc: -{{ number_format($item->discount_amount * $item->quantity, 0, ',', '.') }}</td>
                </tr>
                @endif
                @if($item->notes)
                <tr>
                    <td colspan="2" style="font-size: 10px; font-style: italic; color: #666;">{{ $item->notes }}</td>
                </tr>
                @endif
            @endforeach
        </table>
    </div>

    <div class="border-t">
        <table>
            <tr>
                <td>Subtotal:</td>
                <td class="text-right font-bold">Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</td>
            </tr>
            @if($sale->discount > 0)
            <tr>
                <td>Diskon:</td>
                <td class="text-right">- Rp {{ number_format($sale->discount, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($sale->service_charge_amount > 0)
            <tr>
                <td>Service ({{ $sale->service_charge_percentage }}%):</td>
                <td class="text-right">Rp {{ number_format($sale->service_charge_amount, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($sale->tax > 0)
            <tr>
                <td>PPN ({{ strtoupper($sale->ppn_mode) }}):</td>
                <td class="text-right">Rp {{ number_format($sale->tax, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($sale->rounding != 0)
            <tr>
                <td>Pembulatan:</td>
                <td class="text-right">{{ $sale->rounding > 0 ? '+' : '' }} Rp {{ number_format($sale->rounding, 0, ',', '.') }}</td>
            </tr>
            @endif
        </table>
    </div>

    <div class="border-t">
        <table>
            <tr>
                <td class="font-bold">TOTAL:</td>
                <td class="text-right font-bold" style="font-size: 14px;">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Bayar:</td>
                <td class="text-right">Rp {{ number_format($sale->cash_amount, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Kembali:</td>
                <td class="text-right">Rp {{ number_format($sale->change_amount, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div class="border-t text-center" style="margin-top: 10px; font-size: 10px;">
        Terima Kasih atas Kunjungan Anda<br>
        Semoga Lekas Sembuh
    </div>

    <div class="no-print" style="margin-top: 20px; text-align: center;">
        <button onclick="window.print()" style="width: 100%; padding: 12px; margin-bottom: 10px; background: #000; color: #fff; border: none; border-radius: 5px; font-weight: bold; cursor: pointer;">📄 CETAK STRUK</button>
        <button onclick="window.location.href='{{ route('pos.cashier') }}'" style="width: 100%; padding: 12px; background: #ddd; color: #000; border: none; border-radius: 5px; font-weight: bold; cursor: pointer;">← KEMBALI KE KASIR</button>
    </div>

    <script>
        // Auto-print dengan delay
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 1000);
        });
    </script>
</body>
</html>
