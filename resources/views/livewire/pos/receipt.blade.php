<div x-data x-init="setTimeout(() => window.print(), 1000)">
    @php
        $ppnRate = \App\Models\Setting::get('pos_ppn_rate', 11);
        $isA4 = $paperSize === 'A4';
        $widthClass = $isA4 ? 'w-[210mm]' : ($paperSize === '80mm' ? 'w-[80mm]' : 'w-[58mm]');
        $baseFontSize = $isA4 ? 'text-sm' : 'text-[11px]';
    @endphp

    <style>
        body, html {
            background-color: #fff !important;
            color: #000 !important;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 14px;
            @if($paperSize === 'A4')
                font-family: sans-serif;
                width: 210mm;
                padding: 20mm;
            @elseif($paperSize === '80mm')
                width: 80mm;
                padding: 5mm;
            @else
                width: 58mm;
                padding: 3mm;
            @endif
        }
        .receipt-container {
            margin: 0 auto;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .border-t { border-top: 1px dashed #000; margin: 5px 0; }
        .border-b { border-bottom: 1px dashed #000; margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; }
        td { vertical-align: top; padding: 2px 0; }
        .header { margin-bottom: 15px; }
        .shop-name { font-size: {{ $isA4 ? '24px' : '18px' }}; font-weight: bold; text-transform: uppercase; }
        .meta { font-size: {{ $isA4 ? '16px' : '14px' }}; margin-bottom: 10px; }
        .items td { font-size: {{ $isA4 ? '15px' : '14px' }}; }
        .totals td { font-size: {{ $isA4 ? '15px' : '14px' }}; }
        @media print { 
            .no-print { display: none !important; } 
            body { padding: {{ $isA4 ? '10mm' : '0' }}; }
        }
    </style>

    <div class="receipt-container">
        <div class="header text-center">
            @if($logoPath = \App\Models\Setting::get('store_logo_path'))
                <img src="{{ asset('storage/' . $logoPath) }}" style="max-width: {{ $isA4 ? '60mm' : '40mm' }}; max-height: 25mm; margin-bottom: 10px; margin-left: auto; margin-right: auto; display: block;" alt="Logo">
            @endif
            <div class="shop-name">{{ \App\Models\Setting::get('store_name', config('app.name', 'Apotek')) }}</div>
            <div style="font-size: {{ $isA4 ? '12px' : '10px' }};">
                {{ \App\Models\Setting::get('store_address', 'Jl. Raya Apotek No. 123') }}
                <br>Telp: {{ \App\Models\Setting::get('store_phone', '0812-3456-7890') }}
            </div>
            @if($taxId = \App\Models\Setting::get('store_tax_id'))
                <div style="font-size: 9px; margin-top: 2px;">NPWP: {{ $taxId }}</div>
            @endif
        </div>

        <div class="meta border-b pb-2">
            <table style="font-size: {{ $isA4 ? '12px' : '10px' }};">
                <tr>
                    <td>No: {{ $sale->invoice_no }}</td>
                    <td class="text-right">Tgl: {{ $sale->date }}</td>
                </tr>
                <tr>
                    <td>Kasir: {{ $sale->user->name }}</td>
                    <td class="text-right">{{ $sale->created_at->format('H:i') }}</td>
                </tr>
            </table>
        </div>

        <div class="items">
            <table>
                @if($isA4)
                <thead class="border-b">
                    <tr>
                        <th class="text-left py-2">Item</th>
                        <th class="text-center py-2">Qty</th>
                        <th class="text-right py-2">Harga</th>
                        <th class="text-right py-2">Total</th>
                    </tr>
                </thead>
                @endif
                <tbody>
                    @foreach($sale->saleItems as $item)
                        @if(!$isA4)
                        <tr>
                            <td colspan="2" class="font-bold">
                                {{ $item->product->name }}
                            </td>
                        </tr>
                        <tr>
                            <td>{{ $item->quantity }} x {{ number_format($item->sell_price, 0, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @else
                        <tr class="border-b border-gray-100">
                            <td class="py-2">
                                {{ $item->product->name }}
                                @if($item->notes)
                                    <div style="font-size: 9px; color: #666;">(Note: {{ $item->notes }})</div>
                                @endif
                            </td>
                            <td class="text-center py-2">{{ $item->quantity }}</td>
                            <td class="text-right py-2">{{ number_format($item->sell_price, 0, ',', '.') }}</td>
                            <td class="text-right py-2">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endif

                        @if(!$isA4 && $item->notes)
                        <tr>
                            <td colspan="2" style="font-size: 9px; font-style: italic;">(Note: {{ $item->notes }})</td>
                        </tr>
                        @endif

                        @if($item->discount_amount > 0)
                        <tr>
                            <td style="font-size: 9px; color: #444;" colspan="{{ $isA4 ? 4 : 2 }}">
                                (Diskon Item: -{{ number_format($item->discount_amount * $item->quantity, 0, ',', '.') }})
                            </td>
                        </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="border-t"></div>

        <div class="totals pt-2">
            <table style="margin-left: auto; width: {{ $isA4 ? '40%' : '100%' }};">
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
                @if($sale->tax > 0)
                <tr>
                    <td>PPN ({{ $ppnRate }}%)</td>
                    <td class="text-right">{{ number_format($sale->tax, 0, ',', '.') }}</td>
                </tr>
                @endif
                @if($sale->rounding != 0)
                <tr>
                    <td>Pembulatan</td>
                    <td class="text-right">{{ number_format($sale->rounding, 0, ',', '.') }}</td>
                </tr>
                @endif
                <tr class="font-bold">
                    <td style="padding-top: 5px; font-size: 14px;">TOTAL</td>
                    <td class="text-right" style="padding-top: 5px; font-size: 14px;">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</td>
                </tr>
                <tr class="border-t">
                    <td style="padding-top: 5px;">{{ ucfirst($sale->payment_method) }}</td>
                    <td class="text-right" style="padding-top: 5px;">{{ number_format($sale->cash_amount, 0, ',', '.') }}</td>
                </tr>
                @if($sale->change_amount > 0)
                <tr>
                    <td>Kembali</td>
                    <td class="text-right">{{ number_format($sale->change_amount, 0, ',', '.') }}</td>
                </tr>
                @endif
            </table>
        </div>

        @if($bankAccount = \App\Models\Setting::get('store_bank_account'))
            <div class="border-t text-center" style="margin-top: 10px; font-size: 10px;">
                <div class="font-bold">Penerimaan via Bank</div>
                <div>{{ \App\Models\Setting::get('store_bank_name', '-') }} - {{ $bankAccount }}</div>
                <div>A/n: {{ \App\Models\Setting::get('store_bank_holder', '-') }}</div>
            </div>
        @endif

        <div class="border-t text-center" style="margin-top: 15px; font-size: {{ $isA4 ? '11px' : '9px' }}; white-space: pre-line; line-height: 1.4;">
            {{ \App\Models\Setting::get('store_footer_note', "Terima Kasih atas Kunjungan Anda\nSemoga Lekas Sembuh") }}
        </div>
    </div>

    <div class="no-print" style="margin-top: 40px; text-align: center; max-width: 58mm; margin-left: auto; margin-right: auto;">
        <button @click="window.print()" style="width: 100%; padding: 12px; margin-bottom: 10px; background: #2563eb; color: #fff; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);">CETAK ULANG</button>
        <button onclick="window.location.href='{{ route('pos.cashier') }}'" style="width: 100%; padding: 12px; background: #f3f4f6; color: #374151; border: 1px solid #d1d5db; border-radius: 8px; font-weight: bold; cursor: pointer;">KEMBALI KE KASIR</button>
    </div>
</div>
