<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$values = Illuminate\Support\Facades\DB::table('journal_entries')->select('source')->distinct()->pluck('source');
echo "Distinct Source Values:\n";
foreach ($values as $val) {
    echo "- '$val'\n";
}
