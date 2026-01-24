<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Receivable;
use App\Models\GoodsReceipt;
use App\Models\PurchaseOrder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinancialOverview extends Component
{
    public $totalReceivables = 0;
    public $totalPayables = 0;
    
    public $topReceivables = [];
    public $topPayables = [];

    public function mount()
    {
        // 1. Permission Check
        if (!auth()->user()->can('view financial overview')) {
            return;
        }

        $this->calculateTotals();
        $this->fetchTopDueItems();
    }

    public function calculateTotals()
    {
        // Total Receivables (Unpaid Sales)
        $this->totalReceivables = Receivable::whereIn('status', ['unpaid', 'partial'])
            ->sum('remaining_balance');

        // Total Payables (Unpaid Purchase Orders / Goods Receipts)
        // We look at Goods Receipts that are not fully paid
        $this->totalPayables = GoodsReceipt::whereIn('payment_status', ['pending', 'partial'])
            ->sum(DB::raw('total_amount - paid_amount'));
    }

    public function fetchTopDueItems()
    {
        // Top 5 Receivables Due Soon (Oldest Due Date First)
        $this->topReceivables = Receivable::with(['customer', 'sale'])
            ->whereIn('status', ['unpaid', 'partial'])
            ->whereNotNull('due_date')
            ->orderBy('due_date', 'asc') // Oldest due date (most urgent) first
            ->take(5)
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->customer->name ?? 'Unknown',
                    'ref' => $item->sale->invoice_no ?? '-',
                    'due_date' => $item->due_date ? Carbon::parse($item->due_date)->format('d M Y') : '-',
                    'amount' => $item->remaining_balance,
                    'is_overdue' => $item->due_date ? Carbon::now()->gt($item->due_date) : false,
                ];
            });

        // Top 5 Payables Due Soon
        $this->topPayables = GoodsReceipt::with(['purchaseOrder.supplier'])
            ->whereIn('payment_status', ['pending', 'partial'])
            ->whereNotNull('due_date')
            ->orderBy('due_date', 'asc')
            ->take(5)
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->purchaseOrder->supplier->name ?? 'Unknown',
                    'ref' => $item->delivery_note_number ?? '-',
                    'due_date' => $item->due_date ? Carbon::parse($item->due_date)->format('d M Y') : '-',
                    'amount' => $item->total_amount - $item->paid_amount,
                    'is_overdue' => $item->due_date ? Carbon::now()->gt($item->due_date) : false,
                ];
            });
    }

    public function render()
    {
        // If user doesn't have permission, we can render an empty view or check in blade
        if (!auth()->user()->can('view financial overview')) {
             return view('livewire.dashboard.empty-state'); 
             // Or simply return a div that says nothing, but since it's used inside dashboard
             // we'll handle the UI hiding in the parent or verify permission in blade
        }

        return view('livewire.dashboard.financial-overview');
    }
}
