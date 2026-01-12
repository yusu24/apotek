<?php

use App\Models\Receivable;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Checking Recent Receivables...\n";

$receivables = Receivable::with('sale')
    ->orderBy('created_at', 'desc')
    ->limit(5)
    ->get();

foreach ($receivables as $r) {
    echo "ID: {$r->id} | Invoice: " . ($r->sale->invoice_no ?? 'N/A') . "\n";
    echo "   Created At: " . $r->created_at->format('Y-m-d H:i:s') . "\n";
    echo "   Due Date:   " . ($r->due_date ? $r->due_date->format('Y-m-d') : 'NULL') . "\n";
    
    $daysDiff = $r->created_at->diffInDays($r->due_date, false);
    echo "   Diff (Days): " . round($daysDiff) . "\n";
    echo "   Age (Since Created): " . $r->created_at->diffInDays(now()) . " Days\n";
    echo "--------------------------\n";
}
