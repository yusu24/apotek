<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembelian</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 10px 10px 0 0;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            background: #f9fafb;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .info-box {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: 600;
            color: #6b7280;
        }
        .value {
            color: #111827;
            font-weight: 500;
        }
        .total {
            background: #667eea;
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            margin-top: 20px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
            color: #6b7280;
            font-size: 14px;
        }
        .attachment-note {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin-top: 20px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>✅ Transaksi Berhasil</h1>
        <p style="margin: 10px 0 0 0; opacity: 0.9;">Terima kasih atas pembelian Anda</p>
    </div>
    
    <div class="content">
        <div class="info-box">
            <div class="info-row">
                <span class="label">No. Invoice:</span>
                <span class="value">{{ $invoiceNo }}</span>
            </div>
            <div class="info-row">
                <span class="label">Tanggal:</span>
                <span class="value">{{ $date }}</span>
            </div>
        </div>

        <div class="total">
            Total: Rp {{ $total }}
        </div>

        <div class="attachment-note">
            <strong>📎 Struk Terlampir</strong><br>
            Struk pembelian lengkap Anda tersedia dalam file PDF yang terlampir pada email ini.
        </div>

        <div class="footer">
            <p>Email ini dikirim secara otomatis dari sistem POS kami.</p>
            <p style="margin-top: 10px;">Jika ada pertanyaan, silakan hubungi kami.</p>
        </div>
    </div>
</body>
</html>
