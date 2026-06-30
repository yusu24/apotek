<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.app')]
class SalesReport extends Component
{
    use WithPagination;

    public $startDate;
    public $endDate;
    public $paymentMethod = 'all';
    public $perPage = 10;
    public $search = '';

    // Modal state
    public $showDetailModal = false;
    public $selectedSale = null;
    public $showReceiptModal = false;
    public $completedSaleId = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'startDate' => ['except' => ''],
        'endDate' => ['except' => ''],
        'paymentMethod' => ['except' => 'all'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function mount()
    {
        if (!auth()->user()->can('view sales reports')) {
            abort(403, 'Unauthorized');
        }
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->paymentMethod = 'all';
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
        $this->resetPage();
    }

    public function viewSale($saleId)
    {
        $sale = Sale::with(['saleItems.product', 'saleItems.unit', 'saleItems.batch', 'user', 'customer'])->find($saleId);
        
        if ($sale) {
            $this->selectedSale = $sale->toArray();
            $this->selectedSale['sale_items'] = $sale->saleItems->map(function($item) {
                return [
                    'product_name' => $item->product->name ?? '-',
                    'unit_name' => $item->unit->name ?? '-',
                    'batch_number' => $item->batch->batch_number ?? '-',
                    'quantity' => $item->quantity,
                    'sell_price' => $item->sell_price,
                    'discount_amount' => $item->discount_amount ?? 0,
                    'subtotal' => $item->subtotal,
                    'notes' => $item->notes,
                ];
            })->toArray();
            $this->selectedSale['user_name'] = $sale->user->name ?? '-';
            $this->selectedSale['customer_name'] = $sale->customer->name ?? null;
            $this->showDetailModal = true;
        }
    }

    public function closeDetail()
    {
        $this->showDetailModal = false;
        $this->selectedSale = null;
    }

    public function openReceiptModal($saleId)
    {
        $this->completedSaleId = $saleId;
        $this->showReceiptModal = true;
    }

    public function closeReceiptModal()
    {
        $this->showReceiptModal = false;
        $this->completedSaleId = null;
    }


    public function updatedPerPage()
    {
        $this->resetPage();
    }

    
    public function updatingPerPage()
    {
        $this->resetPage();
    }
public function render()
    {
        $salesQuery = Sale::query()
            ->with(['user'])
            ->whereDate('date', '>=', $this->startDate)
            ->whereDate('date', '<=', $this->endDate)
            ->when($this->paymentMethod !== 'all', function($q) {
                $q->where('payment_method', $this->paymentMethod);
            })
            ->when($this->search, function($q) {
                $q->where(function($query) {
                    $query->where('invoice_no', 'like', '%' . $this->search . '%')
                        ->orWhereHas('user', function($u) {
                            $u->where('name', 'like', '%' . $this->search . '%');
                        });
                });
            });

        /** @var \Illuminate\Pagination\LengthAwarePaginator $sales */
        $sales = (clone $salesQuery)->latest('date')->paginate($this->perPage);
        $sales->onEachSide(1);

        $totalReturns = \App\Models\SalesReturn::whereBetween('created_at', [
            Carbon::parse($this->startDate)->startOfDay(),
            Carbon::parse($this->endDate)->endOfDay()
        ])->sum('total_amount');

        $stats = [
            'total_sales' => (clone $salesQuery)->sum('grand_total'),
            'total_returns' => $totalReturns,
            'net_sales' => (clone $salesQuery)->sum('dpp') - $totalReturns,
            'total_dpp' => (clone $salesQuery)->sum('dpp'),
            'total_tax' => (clone $salesQuery)->sum('tax'),
            'total_rounding' => (clone $salesQuery)->sum('rounding'),
            'transaction_count' => (clone $salesQuery)->count(),
        ];

        return view('livewire.reports.sales-report', [
            'sales' => $sales,
            'stats' => $stats,
        ]);
    }
}
