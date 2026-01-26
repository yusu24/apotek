<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

use App\Models\Sale;
use App\Mail\ReceiptMail;
use Illuminate\Support\Facades\Mail;

try {
    echo "Fetching latest sale...\n";
    $sale = Sale::with('saleItems.product')->latest()->first();
    
    if (!$sale) {
        die("No sales found.\n");
    }

    echo "Sending ReceiptMail for INV: " . $sale->invoice_no . " to yusuf24ef@gmail.com...\n";
    
    Mail::to('yusuf24ef@gmail.com')->send(new ReceiptMail($sale));
    
    echo "SUCCESS: Full Mailable sent.\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
