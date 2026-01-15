<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use App\Services\AccountingService;

class AgingReport extends Component
{
    public $reportData = null;
    public $activeTab = 'all'; // all, 0-7, 8-15, 16-30, 31-45, 45+
    public $type = 'ap'; // ap (Hutang), ar (Piutang)
    public $showPaid = false; // Toggle to show paid records

    // Payment Modal Properties
    public $showPaymentModal = false;
    public $selectedReceivableId = null;
    public $paymentAmount = 0;
    public $paymentNotes = '';
    public $maxPaymentAmount = 0;
    public $selectedCustomerName = '';

    public function mount()
    {
        if (!auth()->user()->can('view ap aging report')) { // Use same permission for now
            abort(403, 'Unauthorized');
        }

        $this->generateReport();
    }

    public function setType($type)
    {
        $this->type = $type;
        $this->generateReport();
    }

    public function updatedShowPaid()
    {
        $this->generateReport();
    }

    public function generateReport()
    {
        $accountingService = new AccountingService();
        if ($this->type === 'ar') {
            $this->reportData = $accountingService->getArAgingReport($this->showPaid);
        } else {
            // For now only AR supports includedPaid fully in our recent edits,
            // but we should support AP too if requested. 
            // Let's stick to AR update first as per user request context (Piutang).
            // But user asked "utuk hutang dan piutan" (for debt and receivables).
            // So we need to update getApAgingReport too.
            $this->reportData = $accountingService->getApAgingReport($this->showPaid); 
        }
    }

    public function openPaymentModal($receivableId, $amount, $customerName)
    {
        $this->selectedReceivableId = $receivableId;
        $this->maxPaymentAmount = $amount; // Remaining balance
        $this->paymentAmount = $amount; // Default to full payment
        $this->selectedCustomerName = $customerName;
        $this->paymentNotes = '';
        $this->showPaymentModal = true;
    }

    public function closePaymentModal()
    {
        $this->showPaymentModal = false;
        $this->reset(['selectedReceivableId', 'paymentAmount', 'paymentNotes', 'maxPaymentAmount', 'selectedCustomerName']);
    }

    public function payReceivable()
    {
        $this->validate([
            'paymentAmount' => 'required|numeric|min:1|max:' . $this->maxPaymentAmount,
        ]);

        try {
            $accountingService = new AccountingService();
            $accountingService->processReceivablePayment($this->selectedReceivableId, [
                'amount' => $this->paymentAmount,
                'notes' => $this->paymentNotes,
                'payment_method' => 'cash' // Default to cash for now
            ]);

            $this->js("alert('Pembayaran berhasil disimpan!')");
            $this->closePaymentModal();
            $this->generateReport(); // Refresh data

        } catch (\Exception $e) {
            $this->addError('paymentAmount', $e->getMessage());
        }
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
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
        return view('livewire.reports.aging-report')->layout('layouts.app');
    }
}
