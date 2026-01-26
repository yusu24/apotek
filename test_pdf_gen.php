<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;

try {
    echo "Fetching latest sale...\n";
    $sale = Sale::latest()->first();
    
    if (!$sale) {
        die("No sales found to test PDF generation.\n");
    }

    echo "Generating PDF for INV: " . $sale->invoice_no . "...\n";
    
    // Simulate ReceiptMail logic
    $pdf = Pdf::loadView('pdf.receipt', ['sale' => $sale]);
    $output = $pdf->output();
    
    echo "PDF Generated successfully (" . strlen($output) . " bytes).\n";
    
    // Try saving it to test storage permission
    file_put_contents('test_receipt.pdf', $output);
    echo "PDF Saved to test_receipt.pdf\n";

} catch (\Exception $e) {
    echo "ERROR Generating PDF: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
