<div x-data x-init="@if($autoprint) setTimeout(() => window.print(), 1000) @endif">
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
            font-size: 15px;
            padding: 15px;
            @if($paperSize === 'A4')
                font-family: sans-serif;
                padding: 25px;
            @endif
        }
        .receipt-container {
            margin: 0 auto;
            width: 100%;
            max-width: 600px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .border-t { border-top: 1px dashed #000; margin: 5px 0; }
        .border-b { border-bottom: 1px dashed #000; margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; }
        td { vertical-align: top; padding: 3px 0; }
        .header { margin-bottom: 15px; }
        .shop-name { font-size: {{ $isA4 ? '26px' : '20px' }}; font-weight: bold; text-transform: uppercase; }
        .store-address { font-size: {{ $isA4 ? '14px' : '13px' }}; line-height: 1.4; }
        .meta-table { font-size: {{ $isA4 ? '14px' : '13px' }}; }
        .section-title { font-size: {{ $isA4 ? '14px' : '13px' }}; font-weight: bold; margin-bottom: 3px; }
        .items td { font-size: {{ $isA4 ? '15px' : '14px' }}; }
        .item-note, .item-discount-note { font-size: 11px; }
        .totals td { font-size: {{ $isA4 ? '15px' : '14px' }}; }
        .bank-details { margin-top: 10px; font-size: 13px; }
        .footer-note { margin-top: 15px; font-size: {{ $isA4 ? '14px' : '13px' }}; white-space: pre-line; line-height: 1.4; }
        
        @media print { 
            .no-print { display: none !important; } 
            body { 
                padding: 0 !important;
                margin: 0 !important;
                font-size: 11px !important;
                @if($paperSize === 'A4')
                    font-family: sans-serif !important;
                    width: 210mm !important;
                    padding: 10mm !important;
                    font-size: 13px !important;
                @elseif($paperSize === '80mm')
                    width: 80mm !important;
                    padding: 3mm !important;
                @else
                    width: 58mm !important;
                    padding: 2mm !important;
                @endif
            }
            .receipt-container {
                width: 100% !important;
                max-width: 100% !important;
            }
            .shop-name { font-size: {{ $isA4 ? '22px' : '15px' }} !important; }
            .store-address { font-size: {{ $isA4 ? '11px' : '9px' }} !important; }
            .meta-table { font-size: {{ $isA4 ? '11px' : '9px' }} !important; }
            .section-title { font-size: {{ $isA4 ? '11px' : '9px' }} !important; }
            .items td { font-size: {{ $isA4 ? '12px' : '10px' }} !important; }
            .item-note, .item-discount-note { font-size: 8px !important; }
            .totals td { font-size: {{ $isA4 ? '12px' : '10px' }} !important; }
            .bank-details { font-size: 9px !important; }
            .footer-note { font-size: {{ $isA4 ? '11px' : '9px' }} !important; }
        }
    </style>

    <div class="receipt-container">
        <div class="header text-center">
            @if($logoPath = \App\Models\Setting::get('store_logo_path'))
                <img src="{{ asset('storage/' . $logoPath) }}" style="max-width: {{ $isA4 ? '60mm' : '40mm' }}; max-height: 25mm; margin-bottom: 10px; margin-left: auto; margin-right: auto; display: block;" alt="Logo">
            @endif
            <div class="shop-name">{{ \App\Models\Setting::get('store_name', config('app.name', 'Apotek')) }}</div>
            <div class="store-address">
                {{ \App\Models\Setting::get('store_address', 'Jl. Raya Apotek No. 123') }}
                <br>Telp: {{ \App\Models\Setting::get('store_phone', '0812-3456-7890') }}
            </div>
            @if($taxId = \App\Models\Setting::get('store_tax_id'))
                <div style="font-size: 9px; margin-top: 2px;">NPWP: {{ $taxId }}</div>
            @endif
        </div>


        <div class="meta border-b pb-2">
            <table class="meta-table">
                <tr>
                    <td style="width: {{ $isA4 ? '30%' : '35%' }};">No</td>
                    <td>: {{ $sale->invoice_no }}</td>
                </tr>
                <tr>
                    <td>Tanggal</td>
                    <td>: {{ $sale->date }}</td>
                </tr>
                <tr>
                    <td>Waktu</td>
                    <td>: {{ $sale->created_at->format('H:i') }}</td>
                </tr>
                <tr>
                    <td>Kasir</td>
                    <td>: {{ $sale->user->name }}</td>
                </tr>
                @if($sale->payment_method === 'tempo' && $sale->receivables)
                <tr>
                    <td class="font-bold">Jatuh Tempo</td>
                    <td class="font-bold">: {{ $sale->receivables->due_date->format('d/m/Y') }}</td>
                </tr>
                @endif
            </table>
        </div>

        @if($sale->patient_name)
        <div class="meta border-b pb-2 mt-2">
            <div class="section-title">DATA PASIEN</div>
            <table class="meta-table">
                <tr>
                    <td style="width: {{ $isA4 ? '30%' : '35%' }};">Nama</td>
                    <td>: {{ $sale->patient_name }}</td>
                </tr>
                @if($sale->patient_doctor_name)
                <tr>
                    <td>Dokter</td>
                    <td>: {{ $sale->patient_doctor_name }}</td>
                </tr>
                @endif
                @if($sale->patient_birth_date)
                <tr>
                    <td>Tgl Lahir</td>
                    <td>: {{ formatDate($sale->patient_birth_date) }}</td>
                </tr>
                @endif
                @if($sale->patient_phone)
                <tr>
                    <td>Telepon</td>
                    <td>: {{ $sale->patient_phone }}</td>
                </tr>
                @endif
                @if($sale->patient_address)
                <tr>
                    <td>Alamat</td>
                    <td>: {{ $sale->patient_address }}</td>
                </tr>
                @endif
                @if($sale->patient_email)
                <tr>
                    <td>Email</td>
                    <td>: {{ $sale->patient_email }}</td>
                </tr>
                @endif
            </table>
        </div>
        @endif

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
                            <td>{{ number_format($item->quantity, $item->quantity == floor($item->quantity) ? 0 : 2, ',', '.') }} {{ $item->unit->name ?? '' }} x {{ number_format($item->sell_price, 0, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @else
                        <tr class="border-b border-gray-100">
                            <td class="py-2">
                                {{ $item->product->name }}
                                @if($item->notes)
                                    <div class="item-note" style="color: #666;">(Note: {{ $item->notes }})</div>
                                @endif
                            </td>
                            <td class="text-center py-2">{{ number_format($item->quantity, $item->quantity == floor($item->quantity) ? 0 : 2, ',', '.') }} {{ $item->unit->name ?? '' }}</td>
                            <td class="text-right py-2">{{ number_format($item->sell_price, 0, ',', '.') }}</td>
                            <td class="text-right py-2">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endif

                        @if(!$isA4 && $item->notes)
                        <tr>
                            <td colspan="2" class="item-note" style="font-style: italic;">(Note: {{ $item->notes }})</td>
                        </tr>
                        @endif

                        @if($item->discount_amount > 0)
                        <tr>
                            <td class="item-discount-note" style="color: #444;" colspan="{{ $isA4 ? 4 : 2 }}">
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
            <div class="border-t text-center bank-details">
                <div class="font-bold">Penerimaan via Bank</div>
                <div>{{ \App\Models\Setting::get('store_bank_name', '-') }} - {{ $bankAccount }}</div>
                <div>A/n: {{ \App\Models\Setting::get('store_bank_holder', '-') }}</div>
            </div>
        @endif

        <div class="border-t text-center footer-note">
            {{ \App\Models\Setting::get('store_footer_note', "Terima Kasih atas Kunjungan Anda\nSemoga Lekas Sembuh") }}
        </div>
    </div>
</div>
