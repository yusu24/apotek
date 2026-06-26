<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use Livewire\WithPagination;
use App\Services\AccountingService;

class AgingReport extends Component
{
    use WithPagination;

    public $activeTab = 'all'; // all, 0-7, 8-15, 16-30, 31-45, 45+
    public $type = 'ap'; // ap (Hutang), ar (Piutang)
    public $showPaid = false; // Toggle to show paid records
    public $search = '';
    public $perPage = 10;

    // Payment Modal Properties
    public $showPaymentModal = false;
    public $selectedItemId = null; // Mixed use for Receivable or GoodsReceipt
    public $paymentAmount = 0;
    public $paymentMethod = 'cash';
    public $bankAccountId = null;
    public $paymentDate = '';
    public $paymentNotes = '';
    public $maxPaymentAmount = 0;
    public $selectedEntityName = '';

    public $accounts = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'activeTab' => ['except' => 'all'],
        'type' => ['except' => 'ap'],
        'showPaid' => ['except' => false],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingActiveTab()
    {
        $this->resetPage();
    }

    public function updatingType()
    {
        $this->resetPage();
    }

    public function updatingShowPaid()
    {
        $this->resetPage();
    }

    public function mount()
    {
        if (!auth()->user()->can('view ap aging report')) {
            abort(403, 'Unauthorized');
        }

        $this->accounts = \App\Models\Account::whereIn('category', ['cash_bank', 'current_asset'])->active()->get();
    }

    public function setType($type)
    {
        $this->type = $type;
        $this->resetPage();
    }

    public function openPaymentModal($id, $amount, $entityName)
    {
        $this->selectedItemId = $id;
        $this->maxPaymentAmount = $amount;
        $this->paymentAmount = $amount;
        $this->selectedEntityName = $entityName;
        $this->paymentMethod = 'cash';
        $this->paymentDate = date('Y-m-d');
        $this->paymentNotes = '';
        $this->showPaymentModal = true;
    }

    public function closePaymentModal()
    {
        $this->showPaymentModal = false;
        $this->reset(['selectedItemId', 'paymentAmount', 'paymentNotes', 'maxPaymentAmount', 'selectedEntityName', 'paymentDate']);
    }

    public function paySettlement()
    {
        $this->validate([
            'paymentAmount' => 'required|numeric|min:1|max:' . ($this->maxPaymentAmount + 0.01),
            'paymentDate' => 'required|date',
        ]);

        try {
            $accountingService = new AccountingService();
            
            if ($this->type === 'ar') {
                $accountingService->processReceivablePayment($this->selectedItemId, [
                    'amount' => $this->paymentAmount,
                    'notes' => $this->paymentNotes,
                    'payment_method' => $this->paymentMethod,
                    'account_id' => $this->bankAccountId,
                    'date' => $this->paymentDate,
                ]);
            } else {
                $accountingService->processSupplierPayment($this->selectedItemId, [
                    'amount' => $this->paymentAmount,
                    'notes' => $this->paymentNotes,
                    'payment_method' => $this->paymentMethod,
                    'account_id' => $this->bankAccountId,
                    'date' => $this->paymentDate,
                ]);
            }

            $this->js("alert('Pembayaran berhasil disimpan!')");
            $this->closePaymentModal();

        } catch (\Exception $e) {
            $this->addError('paymentAmount', $e->getMessage());
        }
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function exportPdf()
    {
        return redirect()->route('pdf.aging-report', [
            'type' => $this->type,
            'showPaid' => $this->showPaid
        ]); 
    }

    public function render()
    {
        $accountingService = new AccountingService();
        if ($this->type === 'ar') {
            $rawReport = $accountingService->getArAgingReport($this->showPaid);
        } else {
            $rawReport = $accountingService->getApAgingReport($this->showPaid); 
        }

        // Apply search filter if present
        if ($this->search) {
            $search = strtolower($this->search);
            foreach (['0-7', '8-15', '16-30', '31-45', '45+'] as $bucket) {
                $rawReport[$bucket] = array_filter($rawReport[$bucket], function($item) use ($search) {
                    $entity = strtolower($item['supplier'] ?? $item['customer'] ?? '');
                    $inv = strtolower($item['invoice_number'] ?? '');
                    return str_contains($entity, $search) || str_contains($inv, $search);
                });
            }
            
            // Recalculate summary based on filtered buckets
            $rawReport['summary'] = [
                '0-7' => 0,
                '8-15' => 0,
                '16-30' => 0,
                '31-45' => 0,
                '45+' => 0,
                'total' => 0
            ];
            foreach (['0-7', '8-15', '16-30', '31-45', '45+'] as $bucket) {
                foreach ($rawReport[$bucket] as $item) {
                    $rawReport['summary'][$bucket] += $item['outstanding'];
                }
                $rawReport['summary']['total'] += $rawReport['summary'][$bucket];
            }
        }

        // Get the active tab's items
        $displayData = [];
        if ($this->activeTab === 'all') {
            $displayData = array_merge(
                $rawReport['45+'] ?? [], 
                $rawReport['31-45'] ?? [], 
                $rawReport['16-30'] ?? [], 
                $rawReport['8-15'] ?? [],
                $rawReport['0-7'] ?? []
            );
        } else {
            $displayData = $rawReport[$this->activeTab] ?? [];
        }

        // Paginate the displayData
        $page = \Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1;
        $items = collect($displayData);
        $paginatedItems = new \Illuminate\Pagination\LengthAwarePaginator(
            $items->forPage($page, $this->perPage)->values(),
            $items->count(),
            $this->perPage,
            $page,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );

        return view('livewire.reports.aging-report', [
            'reportData' => $rawReport,
            'paginatedItems' => $paginatedItems
        ])->layout('layouts.app');
    }
}

