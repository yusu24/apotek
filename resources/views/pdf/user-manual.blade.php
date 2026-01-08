<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Panduan Apotek</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.6;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #2563eb;
        }
        
        .header h1 {
            font-size: 24pt;
            color: #1e40af;
            margin-bottom: 10px;
        }
        
        .header .subtitle {
            font-size: 12pt;
            color: #64748b;
        }
        
        .content {
            padding: 20px;
        }
        
        h1 {
            font-size: 18pt;
            color: #1e40af;
            margin-top: 30px;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e2e8f0;
            page-break-after: avoid;
        }
        
        h2 {
            font-size: 14pt;
            color: #1e3a8a;
            margin-top: 20px;
            margin-bottom: 10px;
            page-break-after: avoid;
        }
        
        h3 {
            font-size: 12pt;
            color: #3b82f6;
            margin-top: 15px;
            margin-bottom: 8px;
            page-break-after: avoid;
        }
        
        p {
            margin-bottom: 10px;
            text-align: justify;
        }
        
        ul, ol {
            margin-left: 20px;
            margin-bottom: 10px;
        }
        
        li {
            margin-bottom: 5px;
        }
        
        code {
            background-color: #f1f5f9;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
            font-size: 9pt;
        }
        
        strong {
            color: #1e40af;
            font-weight: bold;
        }
        
        .alert {
            padding: 12px;
            margin: 15px 0;
            border-left: 4px solid;
            background-color: #f8fafc;
        }
        
        .alert-important {
            border-color: #dc2626;
            background-color: #fef2f2;
        }
        
        .alert-warning {
            border-color: #f59e0b;
            background-color: #fffbeb;
        }
        
        .alert-note {
            border-color: #3b82f6;
            background-color: #eff6ff;
        }
        
        .alert-tip {
            border-color: #10b981;
            background-color: #f0fdf4;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        
        table th {
            background-color: #1e40af;
            color: white;
            padding: 8px;
            text-align: left;
            font-weight: bold;
        }
        
        table td {
            padding: 6px 8px;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 8pt;
            color: #64748b;
            padding: 10px 0;
            border-top: 1px solid #e2e8f0;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        @page {
            margin: 2cm;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📚 BUKU PANDUAN APLIKASI APOTEK</h1>
        <div class="subtitle">Panduan Lengkap Penggunaan Sistem Manajemen Apotek</div>
        <div class="subtitle" style="margin-top: 10px;">Dicetak oleh: {{ $printedBy }} | {{ $printedAt }}</div>
    </div>
    
    <div class="content">
        {!! $content !!}
    </div>
    
    <div class="footer">
        <p>Buku Panduan Aplikasi Apotek | Versi 1.0 | {{ date('Y') }}</p>
    </div>
</body>
</html>
