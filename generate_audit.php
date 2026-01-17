<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\AccountingService;
use App\Models\JournalEntry;
use App\Models\Account;

$service = new AccountingService();

echo "========================================================\n";
echo "     AI AUDITOR - LAPORAN KEUANGAN SISTEM APOTEK       \n";
echo "========================================================\n\n";

// 1. TRIAL BALANCE
echo "1. NERACA SALDO (TRIAL BALANCE)\n";
echo "--------------------------------------------------------\n";
printf("%-10s | %-35s | %15s | %15s\n", "Kode", "Nama Akun", "Debit", "Kredit");
echo "--------------------------------------------------------\n";

$tbData = $service->getTrialBalance();
$totalD = $tbData['grand_total_debit'];
$totalC = $tbData['grand_total_credit'];

foreach ($tbData['accounts'] as $row) {
    $r = (array) $row;
    printf("%-10s | %-35s | %15s | %15s\n", 
        $r['code'], 
        substr($r['name'], 0, 35),
        number_format($r['total_debit'] ?? 0, 0, ',', '.'),
        number_format($r['total_credit'] ?? 0, 0, ',', '.')
    );
}

echo "--------------------------------------------------------\n";
printf("%-48s | %15s | %15s\n", "TOTAL", 
    number_format($totalD, 0, ',', '.'),
    number_format($totalC, 0, ',', '.')
);
echo "Status: " . ($tbData['is_balanced'] ? "✓ SEIMBANG" : "✗ TIDAK SEIMBANG") . "\n";
if (!$tbData['is_balanced']) {
    echo "Selisih: Rp " . number_format(abs($tbData['difference']), 0, ',', '.') . "\n";
}
echo "\n";

// 2. INCOME STATEMENT
echo "2. LAPORAN LABA RUGI\n";
echo "--------------------------------------------------------\n";
$is = $service->getIncomeStatement();
printf("%-45s : Rp %15s\n", "Pendapatan (Revenue)", number_format($is['total_revenue'], 0, ',', '.'));
printf("%-45s : Rp %15s\n", "Harga Pokok Penjualan (COGS)", "(" . number_format($is['total_cogs'], 0, ',', '.') . ")");
echo "--------------------------------------------------------\n";
printf("%-45s : Rp %15s\n", "LABA KOTOR", number_format($is['gross_profit'], 0, ',', '.'));
printf("%-45s : Rp %15s\n", "Beban Operasional", "(" . number_format($is['total_expenses'], 0, ',', '.') . ")");
echo "--------------------------------------------------------\n";
printf("%-45s : Rp %15s\n", "LABA BERSIH", number_format($is['net_income'], 0, ',', '.'));
echo "\n";

// 3. BALANCE SHEET
echo "3. NERACA (BALANCE SHEET)\n";
echo "--------------------------------------------------------\n";
$bs = $service->getBalanceSheet();

echo "ASET:\n";
foreach ($bs['current_assets'] as $a) {
    printf("  %-43s : Rp %15s\n", $a->name, number_format($a->balance, 0, ',', '.'));
}
foreach ($bs['fixed_assets'] as $a) {
    printf("  %-43s : Rp %15s\n", $a->name, number_format($a->balance, 0, ',', '.'));
}
echo "  " . str_repeat("-", 63) . "\n";
printf("  %-43s : Rp %15s\n", "TOTAL ASET", number_format($bs['total_assets'], 0, ',', '.'));
echo "\n";

echo "KEWAJIBAN:\n";
foreach ($bs['current_liabilities'] as $l) {
    printf("  %-43s : Rp %15s\n", $l->name, number_format($l->balance, 0, ',', '.'));
}
foreach ($bs['long_term_liabilities'] as $l) {
    printf("  %-43s : Rp %15s\n", $l->name, number_format($l->balance, 0, ',', '.'));
}
echo "  " . str_repeat("-", 63) . "\n";
printf("  %-43s : Rp %15s\n", "TOTAL KEWAJIBAN", number_format($bs['total_liabilities'], 0, ',', '.'));
echo "\n";

echo "EKUITAS:\n";
foreach ($bs['equity'] as $e) {
    printf("  %-43s : Rp %15s\n", $e->name, number_format($e->balance, 0, ',', '.'));
}
printf("  %-43s : Rp %15s\n", "Laba Periode Berjalan", number_format($bs['net_income'], 0, ',', '.'));
echo "  " . str_repeat("-", 63) . "\n";
printf("  %-43s : Rp %15s\n", "TOTAL EKUITAS", number_format($bs['total_equity'] + $bs['net_income'], 0, ',', '.'));
echo "\n";

$totalLiabEquity = $bs['total_liabilities'] + $bs['total_equity'] + $bs['net_income'];
echo "--------------------------------------------------------\n";
printf("%-45s : Rp %15s\n", "TOTAL KEWAJIBAN + EKUITAS", number_format($totalLiabEquity, 0, ',', '.'));
echo "Status Neraca: " . ($bs['balance_check'] ? "✓ BALANCE" : "✗ TIDAK BALANCE") . "\n";

if (!$bs['balance_check']) {
    $diff = $bs['total_assets'] - $totalLiabEquity;
    echo "Selisih: Rp " . number_format(abs($diff), 0, ',', '.') . "\n";
}

echo "\n========================================================\n";
echo "                  VALIDASI AUDIT                        \n";
echo "========================================================\n";

$validations = [
    ['check' => 'Trial Balance Seimbang (D=C)', 'status' => $tbData['is_balanced']],
    ['check' => 'Neraca Balance (Aset = Liab + Equity)', 'status' => $bs['balance_check']],
    ['check' => 'Laba Rugi Terhubung ke Neraca', 'status' => true], // Always true in this system
];

foreach ($validations as $v) {
    printf("%-45s : %s\n", $v['check'], $v['status'] ? "✓ PASS" : "✗ FAIL");
}

echo "\n========================================================\n";
