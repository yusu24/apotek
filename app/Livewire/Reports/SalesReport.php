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
    public $search = '';

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

        $sales = (clone $salesQuery)->latest('date')->paginate(15);

        $stats = [
            'total_sales' => (clone $salesQuery)->sum('grand_total'),
            'transaction_count' => (clone $salesQuery)->count(),
        ];

        return view('livewire.reports.sales-report', [
            'sales' => $sales,
            'stats' => $stats,
        ]);
    }
}
