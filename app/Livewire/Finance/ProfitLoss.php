<?php

namespace App\Livewire\Finance;

use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.app')]
class ProfitLoss extends Component
{
    public $startDate;
    public $endDate;
    public $period = 'this_month'; // this_month, last_month, this_year, custom

    public function mount()
    {
        // Permission check
        // Permission check
        if (!auth()->user()->can('view profit loss')) {
             abort(403, 'Unauthorized');
        }

        $this->updateDates();
    }

    public function updatedPeriod()
    {
        $this->updateDates();
    }

    public function updateDates()
    {
        $now = Carbon::now();
        if ($this->period === 'this_month') {
            $this->startDate = $now->copy()->startOfMonth()->format('Y-m-d');
            $this->endDate = $now->copy()->endOfMonth()->format('Y-m-d');
        } elseif ($this->period === 'last_month') {
            $this->startDate = $now->copy()->subMonth()->startOfMonth()->format('Y-m-d');
            $this->endDate = $now->copy()->subMonth()->endOfMonth()->format('Y-m-d');
        } elseif ($this->period === 'this_year') {
            $this->startDate = $now->copy()->startOfYear()->format('Y-m-d');
            $this->endDate = $now->copy()->endOfYear()->format('Y-m-d');
        }
    }

    public function export()
    {
        $data = $this->calculateMetrics();
        
        return response()->streamDownload(function () use ($data) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf.profit-loss', array_merge($data, [
                'startDate' => $this->startDate,
                'endDate' => $this->endDate,
                'storeName' => \App\Models\Setting::get('store_name', 'Apotek'),
                'storeAddress' => \App\Models\Setting::get('store_address', ''),
            ]));
            echo $pdf->output();
        }, 'Laporan_Laba_Rugi_' . $this->startDate . '_to_' . $this->endDate . '.pdf');
    }

    private function calculateMetrics()
    {
        // 1. Sales Details (Gross, Discount, Tax)
        $sales = Sale::whereDate('created_at', '>=', $this->startDate)
            ->whereDate('created_at', '<=', $this->endDate)
            ->with('saleItems')
            ->get();

        // Revenue (Net Sales) = Sum of Item Subtotals - Global Discount
        // Note: SaleItem.subtotal is already (price - item_discount) * qty
        $itemSubtotalSum = (float) $sales->sum(function($sale) {
            return $sale->saleItems->sum('subtotal');
        });
        
        $totalDiscount = (float) $sales->sum('discount'); // Global discount
        $revenue = $itemSubtotalSum - $totalDiscount; 
        
        $totalTax = (float) $sales->sum('tax');
        $grandTotal = (float) $sales->sum('grand_total');

        // 2. COGS (HPP) - Detailed records for table
        $cogsDetails = SaleItem::select(
                'sale_items.*', 
                'products.name as product_name', 
                'batches.buy_price as cost_price',
                'sales.created_at as sale_date'
            )
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('batches', 'sale_items.batch_id', '=', 'batches.id') 
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->whereDate('sales.created_at', '>=', $this->startDate)
            ->whereDate('sales.created_at', '<=', $this->endDate)
            ->orderBy('sales.created_at', 'desc')
            ->get();

        $cogs = (float) $cogsDetails->sum(function($item) {
            return (float) $item->quantity * (float) $item->cost_price;
        });

        // 3. Expenses (Beban Operasional & Pajak)
        $allExpenses = Expense::whereDate('date', '>=', $this->startDate)
            ->whereDate('date', '<=', $this->endDate)
            ->orderBy('date', 'desc')
            ->get();

        $taxKeywords = ['pajak', 'tax', 'beban pajak', 'income tax', 'pph', 'pajak penghasilan'];
        
        $taxExpenseDetails = $allExpenses->filter(function($expense) use ($taxKeywords) {
            return in_array(strtolower($expense->category), $taxKeywords) || 
                   \Illuminate\Support\Str::contains(strtolower($expense->description), 'pajak penghasilan');
        });
        
        $operatingExpenseDetails = $allExpenses->diff($taxExpenseDetails);

        $operatingExpenses = (float) $operatingExpenseDetails->sum('amount');
        $taxExpenses = (float) $taxExpenseDetails->sum('amount');

        // 4. Final Calculations
        $grossProfit = $revenue - $cogs;
        $netProfitBeforeTax = $grossProfit - $operatingExpenses;
        $netProfit = $netProfitBeforeTax - $taxExpenses;
        
        return [
            'revenue' => $revenue, // Net Sales
            'totalTax' => $totalTax,
            'totalDiscount' => $totalDiscount,
            'grandTotal' => $grandTotal,
            'cogs' => $cogs,
            'operatingExpenses' => $operatingExpenses, // Replaces 'expenses' for clarity
            'expenses' => $operatingExpenses, // Keep backward compatibility just in case, but view uses this for Operating
            'taxExpenses' => $taxExpenses,
            'grossProfit' => $grossProfit,
            'netProfitBeforeTax' => $netProfitBeforeTax,
            'netProfit' => $netProfit,
            'salesDetails' => $sales,
            'cogsDetails' => $cogsDetails,
            'expenseDetails' => $operatingExpenseDetails,
            'taxDetails' => $taxExpenseDetails,
        ];
    }

    public function render()
    {
        $data = $this->calculateMetrics();

        return view('livewire.finance.profit-loss', $data);
    }
}
