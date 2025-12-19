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
        if (!auth()->user()->can('view finance') && !auth()->user()->can('manage finance')) {
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
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf.profit-loss', array_merge($data, [
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ]));
        
        return $pdf->download('Laporan_Laba_Rugi_' . $this->startDate . '_to_' . $this->endDate . '.pdf');
    }

    private function calculateMetrics()
    {
        // 1. Revenue (Pendapatan Bersih - Tanpa Pajak)
        $revenue = Sale::whereDate('created_at', '>=', $this->startDate)
            ->whereDate('created_at', '<=', $this->endDate)
            ->sum(DB::raw('grand_total - tax'));

        // 2. COGS (HPP) - Sum of (SaleItem Qty * Batch Buy Price)
        $cogs = SaleItem::select(DB::raw('SUM(sale_items.quantity * batches.buy_price) as total_cogs'))
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('batches', 'sale_items.batch_id', '=', 'batches.id') 
            ->whereDate('sales.created_at', '>=', $this->startDate)
            ->whereDate('sales.created_at', '<=', $this->endDate)
            ->value('total_cogs') ?? 0;

        // 3. Expenses (Beban Operasional)
        $expenses = Expense::whereDate('date', '>=', $this->startDate)
            ->whereDate('date', '<=', $this->endDate)
            ->sum('amount');

        // 4. Calculations
        $grossProfit = $revenue - $cogs;
        $netProfit = $grossProfit - $expenses;
        
        return [
            'revenue' => $revenue,
            'cogs' => $cogs,
            'expenses' => $expenses,
            'grossProfit' => $grossProfit,
            'netProfit' => $netProfit,
        ];
    }

    public function render()
    {
        $data = $this->calculateMetrics();

        return view('livewire.finance.profit-loss', $data);
    }
}
