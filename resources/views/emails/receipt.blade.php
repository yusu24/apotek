<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembelian</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 0;
            background-color: #f5f5f5;
        }
        .container {
            background-color: #ffffff;
            margin: 20px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0 0 10px 0;
            font-size: 24px;
            font-weight: 600;
        }
        .header p {
            margin: 5px 0;
            opacity: 0.95;
            font-size: 14px;
        }
        .content {
            padding: 30px;
        }
        .store-info {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 2px solid #e5e7eb;
            margin-bottom: 20px;
        }
        .store-name {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            color: #111827;
            margin-bottom: 5px;
        }
        .store-details {
            font-size: 13px;
            color: #6b7280;
            line-height: 1.5;
        }
        .info-section {
            background: #f9fafb;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .info-section h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            text-transform: uppercase;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            font-size: 13px;
        }
        .label {
            color: #6b7280;
            font-weight: 500;
        }
        .value {
            color: #111827;
            font-weight: 600;
        }
        .items-table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }
        .items-table th {
            background-color: #f3f4f6;
            padding: 10px;
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            color: #374151;
            text-transform: uppercase;
            border-bottom: 2px solid #e5e7eb;
        }
        .items-table td {
            padding: 10px;
            font-size: 13px;
            border-bottom: 1px solid #f3f4f6;
        }
        .item-name {
            font-weight: 600;
            color: #111827;
        }
        .item-qty {
            color: #6b7280;
            font-size: 12px;
        }
        .item-price {
            text-align: right;
            font-weight: 600;
            color: #111827;
        }
        .totals-section {
            background: #f9fafb;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 14px;
        }
        .total-row.grand {
            border-top: 2px solid #667eea;
            padding-top: 12px;
            margin-top: 8px;
            font-size: 18px;
            font-weight: bold;
            color: #667eea;
        }
        .payment-section {
            background: #ecfdf5;
            border: 1px solid #10b981;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
        }
        .payment-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            font-size: 14px;
        }
        .attachment-note {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin-top: 20px;
            border-radius: 4px;
            font-size: 13px;
        }
        .footer {
            text-align: center;
            padding: 20px;
            background-color: #f9fafb;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 12px;
        }
        .thank-you {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 14px;
            margin-top: 20px;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ Transaksi Berhasil</h1>
            <p>Terima kasih atas pembelian Anda</p>
        </div>
        
        <div class="content">
            <!-- Store Info -->
            <div class="store-info">
                <div class="store-name">{{ \App\Models\Setting::get('store_name') ?? config('app.name', 'APOTEK') }}</div>
                <div class="store-details">
                    {{ \App\Models\Setting::get('store_address') ?? 'Jl. Raya Apotek No. 123' }}<br>
                    Telp: {{ \App\Models\Setting::get('store_phone') ?? '0812-3456-7890' }}
                </div>
            </div>

            <!-- Transaction Info -->
            <div class="info-section">
                <h3>Informasi Transaksi</h3>
                <div class="info-row">
                    <span class="label">No. Invoice</span>
                    <span class="value">{{ $sale->invoice_no }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Tanggal</span>
                    <span class="value">{{ $sale->date->format('d/m/Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Waktu</span>
                    <span class="value">{{ $sale->date->format('H:i') }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Kasir</span>
                    <span class="value">{{ $sale->user->name }}</span>
                </div>
            </div>

            <!-- Patient Info (if exists) -->
            @if($sale->patient_name)
            <div class="info-section">
                <h3>Data Pasien</h3>
                <div class="info-row">
                    <span class="label">Nama</span>
                    <span class="value">{{ $sale->patient_name }}</span>
                </div>
                @if($sale->patient_doctor_name)
                <div class="info-row">
                    <span class="label">Dokter</span>
                    <span class="value">{{ $sale->patient_doctor_name }}</span>
                </div>
                @endif
                @if($sale->patient_birth_date)
                <div class="info-row">
                    <span class="label">Tgl Lahir</span>
                    <span class="value">{{ \Carbon\Carbon::parse($sale->patient_birth_date)->format('d/m/Y') }}</span>
                </div>
                @endif
                @if($sale->patient_phone)
                <div class="info-row">
                    <span class="label">Telepon</span>
                    <span class="value">{{ $sale->patient_phone }}</span>
                </div>
                @endif
            </div>
            @endif

            <!-- Items Table -->
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th style="text-align: center; width: 80px;">Qty</th>
                        <th style="text-align: right; width: 120px;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sale->saleItems as $item)
                    <tr>
                        <td>
                            <div class="item-name">{{ $item->product->name }}</div>
                            <div class="item-qty">{{ number_format($item->quantity, 0) }} x Rp {{ number_format($item->sell_price, 0, ',', '.') }}</div>
                            @if($item->notes)
                            <div style="font-size: 11px; font-style: italic; color: #6b7280; margin-top: 3px;">{{ $item->notes }}</div>
                            @endif
                        </td>
                        <td style="text-align: center; color: #6b7280;">{{ $item->quantity }}</td>
                        <td class="item-price">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Totals -->
            <div class="totals-section">
                <div class="total-row">
                    <span>Subtotal</span>
                    <span style="font-weight: 600;">Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</span>
                </div>
                @if($sale->discount > 0)
                <div class="total-row">
                    <span>Diskon</span>
                    <span style="color: #ef4444;">- Rp {{ number_format($sale->discount, 0, ',', '.') }}</span>
                </div>
                @endif
                @if($sale->service_charge_amount > 0)
                <div class="total-row">
                    <span>Service ({{ $sale->service_charge_percentage }}%)</span>
                    <span>Rp {{ number_format($sale->service_charge_amount, 0, ',', '.') }}</span>
                </div>
                @endif
                @if($sale->tax > 0)
                <div class="total-row">
                    <span>PPN ({{ strtoupper($sale->ppn_mode) }})</span>
                    <span>Rp {{ number_format($sale->tax, 0, ',', '.') }}</span>
                </div>
                @endif
                @if($sale->rounding != 0)
                <div class="total-row">
                    <span>Pembulatan</span>
                    <span>{{ $sale->rounding > 0 ? '+' : '' }} Rp {{ number_format($sale->rounding, 0, ',', '.') }}</span>
                </div>
                @endif
                <div class="total-row grand">
                    <span>TOTAL</span>
                    <span>Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Payment Info -->
            <div class="payment-section">
                <div class="payment-row">
                    <span style="font-weight: 600;">Bayar</span>
                    <span style="font-weight: 600;">Rp {{ number_format($sale->cash_amount, 0, ',', '.') }}</span>
                </div>
                <div class="payment-row">
                    <span style="font-weight: 600;">Kembali</span>
                    <span style="font-weight: 600; color: #10b981;">Rp {{ number_format($sale->change_amount, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Attachment Note -->
            <div class="attachment-note">
                <strong>📎 Struk Terlampir</strong><br>
                Struk pembelian lengkap Anda tersedia dalam file PDF yang terlampir pada email ini.
            </div>

            <!-- Thank You Message -->
            <div class="thank-you">
                <strong>Terima Kasih atas Kunjungan Anda</strong><br>
                Semoga Lekas Sembuh
            </div>
        </div>

        <div class="footer">
            <p>Email ini dikirim secara otomatis dari sistem POS kami.</p>
            <p style="margin-top: 10px;">Jika ada pertanyaan, silakan hubungi kami.</p>
        </div>
    </div>
</body>
</html>
