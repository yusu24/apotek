<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\AccountingService;

$service = new AccountingService();

echo "\n=== QUICK AUDIT CHECK ===\n\n";

// Trial Balance
$tb = $service->getTrialBalance();
echo "1. TRIAL BALANCE\n";
echo "   Total Debit  : Rp " . number_format($tb['grand_total_debit'], 0, ',', '.') . "\n";
echo "   Total Credit : Rp " . number_format($tb['grand_total_credit'], 0, ',', '.') . "\n";
echo "   Status       : " . ($tb['is_balanced'] ? "✓ BALANCED" : "✗ NOT BALANCED") . "\n";
echo "   Difference   : Rp " . number_format(abs($tb['difference']), 0, ',', '.') . "\n\n";

// Income Statement
$is = $service->getIncomeStatement();
echo "2. INCOME STATEMENT\n";
echo "   Revenue      : Rp " . number_format($is['total_revenue'], 0, ',', '.') . "\n";
echo "   COGS         : Rp " . number_format($is['total_cogs'], 0, ',', '.') . "\n";
echo "   Gross Profit : Rp " . number_format($is['gross_profit'], 0, ',', '.') . "\n";
echo "   Expenses     : Rp " . number_format($is['total_expenses'], 0, ',', '.') . "\n";
echo "   Net Income   : Rp " . number_format($is['net_income'], 0, ',', '.') . "\n\n";

// Balance Sheet
$bs = $service->getBalanceSheet();
echo "3. BALANCE SHEET\n";
echo "   Total Assets     : Rp " . number_format($bs['total_assets'], 0, ',', '.') . "\n";
echo "   Total Liabilities: Rp " . number_format($bs['total_liabilities'], 0, ',', '.') . "\n";
echo "   Total Equity     : Rp " . number_format($bs['total_equity'], 0, ',', '.') . "\n";
echo "   Net Income       : Rp " . number_format($bs['net_income'], 0, ',', '.') . "\n";
$totalLE = $bs['total_liabilities'] + $bs['total_equity'] + $bs['net_income'];
echo "   Total L+E        : Rp " . number_format($totalLE, 0, ',', '.') . "\n";
echo "   Status           : " . ($bs['balance_check'] ? "✓ BALANCED" : "✗ NOT BALANCED") . "\n\n";

// Validations
echo "=== VALIDATION RESULTS ===\n";
echo "✓ Trial Balance: " . ($tb['is_balanced'] ? "PASS" : "FAIL") . "\n";
echo "✓ Balance Sheet: " . ($bs['balance_check'] ? "PASS" : "FAIL") . "\n";
echo "✓ Net Income in BS: " . ($bs['net_income'] == $is['net_income'] ? "PASS" : "FAIL") . "\n";

echo "\n";
