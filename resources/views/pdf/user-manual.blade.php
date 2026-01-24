<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Panduan Apotek</title>
    <style>
        @page { 
            size: A4; 
            margin:    10mm 1cm 10mm 1cm; 
        }
        
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: 10pt; 
            color: #1a1a1a; 
            margin: 0; 
            padding: 0; 
            line-height: 1.6;
        }

        .full-width { width: 100%; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }

        .report-header { 
            margin-bottom: 40px; 
            display: block;
            width: 100%;
            border-bottom: 3pt solid #1e40af;
            padding-bottom: 20px;
        }
        .store-name { 
            font-size: 20pt; 
            font-weight: bold; 
            color: #1e40af;
            margin: 0; 
        }
        .report-title { 
            font-size: 14pt; 
            font-weight: bold; 
            color: #64748b; 
            margin-top: 8px;
        }
        
        .content { padding: 0; }
        
        h1 {
            font-size: 18pt;
            color: #1e40af;
            margin-top: 30px;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2pt solid #e2e8f0;
            page-break-after: avoid;
        }
        
        h2 {
            font-size: 14pt;
            color: #1e3a8a;
            margin-top: 25px;
            margin-bottom: 12px;
            page-break-after: avoid;
        }
        
        h3 {
            font-size: 12pt;
            color: #2563eb;
            margin-top: 20px;
            margin-bottom: 10px;
            page-break-after: avoid;
        }
        
        p { margin-bottom: 12px; text-align: justify; }
        
        ul, ol { margin-left: 25px; margin-bottom: 15px; }
        li { margin-bottom: 6px; }
        
        code {
            background-color: #f1f5f9;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 10pt;
        }
        
        strong { color: #1e40af; font-weight: bold; }
        
        .alert {
            padding: 15px;
            margin: 20px 0;
            border-left: 5px solid;
            background-color: #f8fafc;
            border-radius: 0 6px 6px 0;
        }
        .alert-important { border-color: #dc2626; background-color: #fef2f2; }
        .alert-warning { border-color: #f59e0b; background-color: #fffbeb; }
        .alert-note { border-color: #3b82f6; background-color: #eff6ff; }
        .alert-tip { border-color: #10b981; background-color: #f0fdf4; }
        
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        table th {
            background-color: #1e40af;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }
        table td { padding: 8px 10px; border-bottom: 1px solid #e2e8f0; }
        
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <div class="report-header text-center">
        <div class="store-name uppercase">BUKU PANDUAN APLIKASI APOTEK</div>
        <div class="report-title">Panduan Lengkap Penggunaan Sistem Manajemen Apotek</div>
    </div>
    
    <div class="content">
        {!! $content !!}
    </div>
    
    </body>
</html>
