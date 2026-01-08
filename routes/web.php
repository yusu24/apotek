<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')
        ->name('dashboard');

    Route::view('profile', 'profile')
        ->name('profile');

    // Master Data (Protected by role via middleware or component logic later)
    Route::get('/products', App\Livewire\Master\ProductIndex::class)->name('products.index');
    Route::get('/products/create', App\Livewire\Master\ProductForm::class)->name('products.create');
    Route::get('/products/{id}/edit', App\Livewire\Master\ProductForm::class)->name('products.edit');
    Route::get('/master/categories', App\Livewire\Master\CategoryManagement::class)->name('master.categories');
    Route::get('/master/product-units', App\Livewire\Master\ProductUnit::class)
        ->name('master.product-units')
        ->middleware('permission:manage product units');
    Route::get('/master/units', App\Livewire\Master\UnitManagement::class)
        ->name('master.units')
        ->middleware('permission:manage master data');
    Route::get('/master/suppliers', App\Livewire\Master\SupplierManagement::class)->name('master.suppliers');


    // Inventory
    Route::get('/stock', App\Livewire\Inventory\StockIndex::class)->name('inventory.index');
    Route::get('/stock/{productId}/history', App\Livewire\Inventory\StockHistory::class)->name('inventory.history');
    Route::get('/stock/adjust/{batchId}', App\Livewire\Inventory\StockAdjustment::class)->name('inventory.adjust');
    Route::get('/inventory/returns/sales', App\Livewire\Inventory\SalesReturnList::class)->name('inventory.returns.sales');
    Route::get('/inventory/returns/purchase', App\Livewire\Inventory\PurchaseReturnList::class)->name('inventory.returns.purchase');

    // POS
    Route::get('/cashier', App\Livewire\Pos\Cashier::class)->name('pos.cashier');
    Route::get('/receipt/{id}', App\Livewire\Pos\Receipt::class)->name('pos.receipt');

    // Reports
    Route::get('/reports/sales', App\Livewire\Reports\SalesChart::class)->name('reports.sales');
    Route::get('/reports/sales-detail', App\Livewire\Reports\SalesReport::class)->name('reports.sales-detail');
    Route::get('/reports/stock', App\Livewire\Reports\StockReport::class)->name('reports.stock');
    Route::get('/reports/transaction-history', App\Livewire\Reports\TransactionHistory::class)->name('reports.transaction-history');
    Route::get('/reports/product-margin', App\Livewire\Reports\ProductMarginReport::class)->name('reports.product-margin');

    // Finance (Super Admin & Admin)
    Route::get('/finance/summary', App\Livewire\Finance\FinancialSummary::class)->name('finance.summary');
    Route::get('/finance/expenses', App\Livewire\Finance\ExpenseManager::class)->name('finance.expenses');
    Route::get('/finance/profit-loss', App\Livewire\Finance\ProfitLoss::class)->name('finance.profit-loss');
    
    // Accounting Reports
    Route::get('/finance/balance-sheet', App\Livewire\Reports\BalanceSheet::class)->name('finance.balance-sheet');
    Route::get('/finance/income-statement', App\Livewire\Reports\IncomeStatement::class)->name('finance.income-statement');
    Route::get('/finance/trial-balance', App\Livewire\Reports\TrialBalance::class)->name('finance.trial-balance');
    Route::get('/finance/ppn-report', App\Livewire\Reports\PpnReport::class)->name('finance.ppn-report');
    Route::get('/finance/ap-aging-report', App\Livewire\Reports\ApAgingReport::class)->name('finance.ap-aging-report');
    Route::get('/finance/opening-balance', App\Livewire\Finance\OpeningBalanceManager::class)
        ->name('finance.opening-balance')
        ->middleware('permission:manage opening balances');
    // Accounting
    Route::get('/accounting/accounts', App\Livewire\Accounting\AccountIndex::class)->name('accounting.accounts.index');
    Route::get('/accounting/journals', App\Livewire\Accounting\JournalIndex::class)->name('accounting.journals.index');
    Route::get('/accounting/journals/create', App\Livewire\Accounting\JournalEntryForm::class)->name('accounting.journals.create');
    Route::get('/accounting/ledger', App\Livewire\Accounting\GeneralLedger::class)->name('accounting.ledger');

    Route::get('/finance/expense-categories', App\Livewire\Finance\ExpenseCategoryIndex::class)
        ->name('finance.expense-categories')
        ->middleware('permission:manage expense categories');
    
    // Procurement
    Route::get('/procurement/purchase-orders', App\Livewire\Procurement\PurchaseOrderIndex::class)->name('procurement.purchase-orders.index');
    Route::get('/procurement/purchase-orders/create', App\Livewire\Procurement\PurchaseOrderForm::class)->name('procurement.purchase-orders.create');
    Route::get('/procurement/purchase-orders/{id}/view', App\Livewire\Procurement\PurchaseOrderForm::class)->name('procurement.purchase-orders.view');
    Route::get('/procurement/purchase-orders/{id}/edit', App\Livewire\Procurement\PurchaseOrderForm::class)->name('procurement.purchase-orders.edit');
    Route::get('/procurement/purchase-orders/{id}/print', App\Livewire\Procurement\PurchaseOrderPrint::class)->name('procurement.purchase-orders.print');
    
    Route::get('/procurement/goods-receipts', App\Livewire\Procurement\GoodsReceiptIndex::class)->name('procurement.goods-receipts.index');
    Route::get('/procurement/goods-receipts/create', App\Livewire\Procurement\GoodsReceiptForm::class)->name('procurement.goods-receipts.create');

    // PDF Exports
    Route::get('/pdf/goods-receipt/{id}', [App\Http\Controllers\PdfController::class, 'exportGoodsReceipt'])->name('pdf.goods-receipt');
    Route::get('/pdf/stock-history/{productId}', [App\Http\Controllers\PdfController::class, 'exportStockHistory'])->name('pdf.stock-history');
    Route::get('/pdf/ppn-report', [App\Http\Controllers\PdfController::class, 'exportPpnReport'])->name('pdf.ppn-report');
    Route::get('/pdf/ap-aging-report', [App\Http\Controllers\PdfController::class, 'exportApAgingReport'])->name('pdf.ap-aging-report');
    Route::get('/pdf/user-manual', [App\Http\Controllers\PdfController::class, 'exportUserManual'])->name('pdf.user-manual');


    // Settings (Super Admin only)
    Route::get('/settings/store', App\Livewire\Settings\StoreSettings::class)->name('settings.store');
    Route::get('/settings/pos', App\Livewire\Settings\PosSettings::class)->name('settings.pos');

    Route::get('/admin/users', App\Livewire\Admin\UserManagement::class)->name('admin.users.index');
    Route::get('/admin/users/create', App\Livewire\Admin\UserForm::class)->name('admin.users.create');
    Route::get('/admin/users/{id}/edit', App\Livewire\Admin\UserForm::class)->name('admin.users.edit');
    
    // Activity Log (Super Admin only)
    Route::get('/admin/activity-log', App\Livewire\ActivityLog\ActivityLogIndex::class)->name('admin.activity-log');
    
    Route::get('/admin/leave-impersonation', [App\Http\Controllers\ImpersonationController::class, 'leave'])->name('admin.leave-impersonation');

    // User Guide
    Route::get('/guide', App\Livewire\Settings\UserGuide::class)->name('guide.index');
    Route::get('/guide/detail/{slug}', App\Livewire\Settings\GuideDetail::class)->name('guide.detail');
    Route::view('/guide/handbook', 'guide.index')->name('guide.handbook');
});

require __DIR__.'/auth.php';
