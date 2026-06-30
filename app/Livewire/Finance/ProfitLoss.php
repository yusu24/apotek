<?php

namespace App\Livewire\Finance;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalesReturn;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

#[Layout('layouts.app')]
class ProfitLoss extends Component
{
    use WithPagination;

    public $startDate;
    public $endDate;
    public $period = 'this_month'; // this_month, last_month, this_year, custom
    public $perPage = 10;

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
        $this->resetPage('salesPage');
        $this->resetPage('cogsPage');
        $this->resetPage('expensePage');
        $this->resetPage('taxPage');
    }

    public function updatedStartDate()
    {
        $this->resetPage('salesPage');
        $this->resetPage('cogsPage');
        $this->resetPage('expensePage');
        $this->resetPage('taxPage');
    }

    public function updatedEndDate()
    {
        $this->resetPage('salesPage');
        $this->resetPage('cogsPage');
        $this->resetPage('expensePage');
        $this->resetPage('taxPage');
    }

    public function updatedPerPage()
    {
        $this->resetPage('salesPage');
        $this->resetPage('cogsPage');
        $this->resetPage('expensePage');
        $this->resetPage('taxPage');
    }

    private function paginateCollection($items, $perPage, $pageName)
    {
        $currentPage = LengthAwarePaginator::resolveCurrentPage($pageName);
        $currentItems = $items->slice(($currentPage - 1) * $perPage, $perPage)->all();
        
        return new LengthAwarePaginator(
            $currentItems,
            $items->count(),
            $perPage,
            $currentPage,
            [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'pageName' => $pageName,
            ]
        );
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
                'printedBy' => auth()->user()->name ?? 'System',
                'printedAt' => now()->format('d/m/Y H:i'),
            ]));
            echo $pdf->output();
        }, 'Laporan_Laba_Rugi_' . $this->startDate . '_to_' . $this->endDate . '.pdf');
    }

    public function exportExcel()
    {
        $data = $this->calculateMetrics();
        $filename = 'Laporan_Laba_Rugi_Detail_' . $this->startDate . '_to_' . $this->endDate . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\DetailedProfitLossExport($data, $this->startDate, $this->endDate),
            $filename
        );
    }

    private function calculateMetrics()
    {
        // 1. Sales Details (Gross, Discount, Tax) — pakai kolom 'date' agar sinkron dengan Laporan Penjualan
        $sales = Sale::whereDate('date', '>=', $this->startDate)
            ->whereDate('date', '<=', $this->endDate)
            ->with('saleItems')
            ->get();

        $grossRevenue = (float) $sales->sum('dpp');
        
        $totalTax = (float) $sales->sum('tax');
        $totalDiscount = (float) $sales->sum('discount');
        $grandTotal = (float) $sales->sum('grand_total');

        // 2. Sales Returns (Retur Penjualan) — sinkron dengan Laporan Penjualan
        $salesReturns = SalesReturn::whereBetween('created_at', [
            Carbon::parse($this->startDate)->startOfDay(),
            Carbon::parse($this->endDate)->endOfDay()
        ])->get();

        $totalReturns = (float) $salesReturns->sum('total_amount');

        // Revenue = DPP - Retur (Pendapatan Riil, sinkron dengan Laporan Penjualan)
        $revenue = $grossRevenue - $totalReturns;

        // 3. COGS (HPP) - Detailed records for table — pakai kolom 'date'
        $cogsDetails = SaleItem::select(
                'sale_items.*', 
                'products.name as product_name', 
                'batches.buy_price as cost_price',
                'sales.date as sale_date'
            )
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('batches', 'sale_items.batch_id', '=', 'batches.id') 
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->whereDate('sales.date', '>=', $this->startDate)
            ->whereDate('sales.date', '<=', $this->endDate)
            ->orderBy('sales.date', 'desc')
            ->get();

        // Also fetch sales with direct cogs (historical/imported)
        $historicalSales = Sale::whereDate('date', '>=', $this->startDate)
            ->whereDate('date', '<=', $this->endDate)
            ->whereNotNull('cogs')
            ->where('cogs', '>', 0)
            ->get();

        $historicalCogsItems = $historicalSales->map(function ($sale) {
            return (object) [
                'id' => null,
                'sale_id' => $sale->id,
                'sale_date' => $sale->date,
                'product_name' => 'HPP Omset Historis (' . $sale->invoice_no . ')',
                'quantity' => 1,
                'cost_price' => (float) $sale->cogs,
                'subtotal' => (float) $sale->cogs
            ];
        });

        $mergedCogsDetails = $cogsDetails->concat($historicalCogsItems)->sortByDesc('sale_date');

        $cogs = (float) $mergedCogsDetails->sum(function($item) {
            return (float) $item->quantity * (float) $item->cost_price;
        });

        // 4. Expenses (Beban Operasional & Pajak)
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

        // 5. Final Calculations
        $grossProfit = $revenue - $cogs;
        $netProfitBeforeTax = $grossProfit - $operatingExpenses;
        $netProfit = $netProfitBeforeTax - $taxExpenses;
        
        return [
            'grossRevenue' => $grossRevenue, // DPP sebelum retur
            'totalReturns' => $totalReturns, // Total retur penjualan
            'revenue' => $revenue, // DPP - Retur = Pendapatan Riil
            'totalTax' => $totalTax,
            'totalDiscount' => $totalDiscount,
            'grandTotal' => $grandTotal,
            'cogs' => $cogs,
            'operatingExpenses' => $operatingExpenses,
            'expenses' => $operatingExpenses,
            'taxExpenses' => $taxExpenses,
            'grossProfit' => $grossProfit,
            'netProfitBeforeTax' => $netProfitBeforeTax,
            'netProfit' => $netProfit,
            'salesDetails' => $sales,
            'salesReturns' => $salesReturns,
            'cogsDetails' => $mergedCogsDetails,
            'expenseDetails' => $operatingExpenseDetails,
            'taxDetails' => $taxExpenseDetails,
        ];
    }

    
    public function updatingPerPage()
    {
        $this->resetPage();
    }
public function render()
    {
        $data = $this->calculateMetrics();

        // Paginate collections for the view
        $data['salesDetails'] = $this->paginateCollection($data['salesDetails'], $this->perPage, 'salesPage');
        $data['cogsDetails'] = $this->paginateCollection($data['cogsDetails'], $this->perPage, 'cogsPage');
        $data['expenseDetails'] = $this->paginateCollection($data['expenseDetails'], $this->perPage, 'expensePage');
        $data['taxDetails'] = $this->paginateCollection($data['taxDetails'], $this->perPage, 'taxPage');

        return view('livewire.finance.profit-loss', $data);
    }
}
