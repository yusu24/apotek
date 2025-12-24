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


    // Inventory
    Route::get('/stock', App\Livewire\Inventory\StockIndex::class)->name('inventory.index');
    Route::get('/stock/{productId}/history', App\Livewire\Inventory\StockHistory::class)->name('inventory.history');
    Route::get('/stock/adjust/{batchId}', App\Livewire\Inventory\StockAdjustment::class)->name('inventory.adjust');

    // POS
    Route::get('/cashier', App\Livewire\Pos\Cashier::class)->name('pos.cashier');
    Route::get('/receipt/{id}', App\Livewire\Pos\Receipt::class)->name('pos.receipt');

    // Reports
    Route::get('/reports/sales', App\Livewire\Reports\SalesChart::class)->name('reports.sales');

    // Finance (Super Admin & Admin)
    Route::get('/finance/expenses', App\Livewire\Finance\ExpenseManager::class)->name('finance.expenses');
    Route::get('/finance/profit-loss', App\Livewire\Finance\ProfitLoss::class)->name('finance.profit-loss');
    
    // Procurement
    Route::get('/procurement/purchase-orders', App\Livewire\Procurement\PurchaseOrderIndex::class)->name('procurement.purchase-orders.index');
    Route::get('/procurement/purchase-orders/create', App\Livewire\Procurement\PurchaseOrderForm::class)->name('procurement.purchase-orders.create');
    Route::get('/procurement/purchase-orders/{id}/edit', App\Livewire\Procurement\PurchaseOrderForm::class)->name('procurement.purchase-orders.edit');
    Route::get('/procurement/purchase-orders/{id}/print', App\Livewire\Procurement\PurchaseOrderPrint::class)->name('procurement.purchase-orders.print');
    
    Route::get('/procurement/goods-receipts', App\Livewire\Procurement\GoodsReceiptIndex::class)->name('procurement.goods-receipts.index');
    Route::get('/procurement/goods-receipts/create', App\Livewire\Procurement\GoodsReceiptForm::class)->name('procurement.goods-receipts.create');

    // Settings (Super Admin only)
    Route::get('/settings/store', App\Livewire\Settings\StoreSettings::class)->name('settings.store');

    // User Management (Super Admin only)
    Route::get('/admin/users', App\Livewire\Admin\UserManagement::class)->name('admin.users.index');
    Route::get('/admin/users/create', App\Livewire\Admin\UserForm::class)->name('admin.users.create');
    Route::get('/admin/users/{id}/edit', App\Livewire\Admin\UserForm::class)->name('admin.users.edit');
    
    Route::get('/admin/leave-impersonation', [App\Http\Controllers\ImpersonationController::class, 'leave'])->name('admin.leave-impersonation');

    // User Guide
    Route::get('/guide', App\Livewire\Settings\UserGuide::class)->name('guide.index');
    Route::get('/guide/detail/{slug}', App\Livewire\Settings\GuideDetail::class)->name('guide.detail');
    Route::view('/guide/handbook', 'guide.index')->name('guide.handbook');
});

require __DIR__.'/auth.php';
