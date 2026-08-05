<?php

namespace App\Livewire\Procurement;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class GoodsReceiptIndex extends Component
{
    use \Livewire\WithPagination;

    public $search = '';
    public $perPage = 10;
    public $dateFrom = '';
    public $dateTo = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
        'sortBy' => ['except' => 'received_date'],
        'sortDirection' => ['except' => 'desc'],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
    ];

    public $sortBy = 'received_date';
    public $sortDirection = 'desc';

    public function sortByColumn($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function updatedDateFrom()
    {
        $this->resetPage();
    }

    public function updatedDateTo()
    {
        $this->resetPage();
    }

    public $showModal = false;
    public $showDetailModal = false;
    public $showPaymentModal = false;
    public $showDeleteModal = false;
    public $selectedId = null;
    public $deleteId = null;
    public $deleteReceipt = null;

    // Payment Form
    public $payment_amount = 0;
    public $payment_date = '';
    public $payment_method = 'cash';
    public $bank_account_id = null; // New Property
    public $payment_notes = '';
    public $remaining_debt = 0;

    public $accounts = []; // New Property

    public function mount()
    {
        if (!auth()->user()->can('view goods receipts')) {
            abort(403, 'Unauthorized');
        }
        
        $this->accounts = \App\Models\Account::where('category', 'cash_bank')
            ->orWhere('sub_category', 'cash')
            ->active()
            ->get();
    }

    public function showDetail($id)
    {
        $this->selectedId = $id;
        $this->showDetailModal = true;
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->selectedId = null;
    }

    public function confirmDelete($id)
    {
        if (!auth()->user()->can('delete goods receipts')) {
            abort(403, 'Anda tidak memiliki hak akses untuk menghapus penerimaan barang.');
        }

        $this->deleteId = $id;
        $this->deleteReceipt = \App\Models\GoodsReceipt::with(['items.product', 'items.unit', 'purchaseOrder.supplier'])->find($id);
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->deleteId = null;
        $this->deleteReceipt = null;
        $this->resetErrorBag();
    }

    public function deleteGoodsReceipt()
    {
        if (!auth()->user()->can('delete goods receipts')) {
            abort(403, 'Anda tidak memiliki hak akses untuk menghapus penerimaan barang.');
        }

        if (!$this->deleteId) {
            return;
        }

        $gr = \App\Models\GoodsReceipt::with(['items.product', 'payments', 'journalEntries', 'purchaseOrder.items', 'purchaseOrder.goodsReceipts.items'])->findOrFail($this->deleteId);

        // 1. Safety check: Check if current batch stock is sufficient to deduct
        foreach ($gr->items as $item) {
            $baseQty = (float)$item->qty_received * (float)($item->conversion_factor ?? 1);
            $batch = \App\Models\Batch::where('product_id', $item->product_id)
                ->where('batch_no', $item->batch_no)
                ->first();

            if ($batch && (float)$batch->stock_current < ($baseQty - 0.001)) {
                $this->addError('delete_error', "Gagal menghapus: Stok fisik produk '" . ($item->product->name ?? 'Produk') . "' (Batch: {$item->batch_no}) tersisa " . (float)$batch->stock_current . ", kurang dari " . (float)$baseQty . " yang pernah diterima dari transaksi ini. Kemungkinan obat sudah terpakai/terjual.");
                return;
            }
        }

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($gr) {
                // 2. Revert Supplier Payments & Payment Journals
                foreach ($gr->payments as $payment) {
                    $paymentJournals = \App\Models\JournalEntry::where('source', 'supplier_payment')
                        ->where('source_id', $payment->id)
                        ->get();
                    foreach ($paymentJournals as $pj) {
                        $pj->lines()->delete();
                        $pj->delete();
                    }
                    $payment->delete();
                }

                // 3. Revert Purchase Journal Entries
                foreach ($gr->journalEntries as $je) {
                    $je->lines()->delete();
                    $je->delete();
                }

                $extraJournals = \App\Models\JournalEntry::where('doc_ref', 'like', 'GR-' . $gr->id . '%')->get();
                foreach ($extraJournals as $ej) {
                    $ej->lines()->delete();
                    $ej->delete();
                }

                // 4. Revert Batch Stock & Movements
                foreach ($gr->items as $item) {
                    $baseQty = (float)$item->qty_received * (float)($item->conversion_factor ?? 1);
                    $batch = \App\Models\Batch::where('product_id', $item->product_id)
                        ->where('batch_no', $item->batch_no)
                        ->first();

                    if ($batch) {
                        $batch->decrement('stock_in', $baseQty);
                        $batch->decrement('stock_current', $baseQty);
                    }
                }

                \App\Models\StockMovement::where('doc_ref', 'like', 'GR-' . $gr->id . '%')->delete();

                // 5. Update Purchase Order status (if attached)
                $poId = $gr->purchase_order_id;

                $grItemsData = $gr->items->toArray();
                $grData = $gr->toArray();

                $gr->items()->delete();
                $gr->delete();

                if ($poId) {
                    $po = \App\Models\PurchaseOrder::with(['items', 'goodsReceipts.items'])->find($poId);
                    if ($po) {
                        $totalReceivedAny = 0;
                        $allFullyReceived = true;

                        foreach ($po->items as $poItem) {
                            $totalReceivedBase = 0;
                            foreach ($po->goodsReceipts as $otherGr) {
                                foreach ($otherGr->items as $grItem) {
                                    if ($grItem->product_id == $poItem->product_id) {
                                        $totalReceivedBase += ((float)$grItem->qty_received * (float)($grItem->conversion_factor ?? 1));
                                    }
                                }
                            }

                            $totalOrderedBase = (float)$poItem->qty_ordered * (float)($poItem->conversion_factor ?? 1);

                            if ($totalReceivedBase > 0) {
                                $totalReceivedAny += $totalReceivedBase;
                            }

                            if (($totalOrderedBase - $totalReceivedBase) > 0.001) {
                                $allFullyReceived = false;
                            }
                        }

                        if ($totalReceivedAny <= 0) {
                            $po->update(['status' => 'ordered']);
                        } elseif ($allFullyReceived) {
                            $po->update(['status' => 'received']);
                        } else {
                            $po->update(['status' => 'partial']);
                        }
                    }
                }

                // 6. Log Activity
                \App\Models\ActivityLog::log([
                    'action' => 'deleted',
                    'module' => 'goods_receipts',
                    'description' => "Penghapusan Penerimaan Barang (SJ: {$grData['delivery_note_number']}, ID: GR-{$grData['id']})",
                    'old_values' => [
                        'goods_receipt' => $grData,
                        'items' => $grItemsData,
                    ],
                    'new_values' => null,
                    'subject_id' => $grData['id'],
                    'subject_type' => \App\Models\GoodsReceipt::class,
                ]);
            });

            $this->closeDeleteModal();
            session()->flash('message', 'Penerimaan barang berhasil dihapus. Stok dan jurnal terkait telah dibatalkan.');
        } catch (\Exception $e) {
            \Log::error('Error deleting Goods Receipt GR-' . $this->deleteId . ': ' . $e->getMessage());
            $this->addError('delete_error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }

    public function openPaymentModal($id)
    {
        $gr = \App\Models\GoodsReceipt::findOrFail($id);
        $this->selectedId = $id;
        $this->remaining_debt = $gr->total_amount - $gr->paid_amount;
        $this->payment_amount = $this->remaining_debt;
        $this->payment_date = date('Y-m-d');
        $this->payment_method = 'cash';
        $this->bank_account_id = null;
        $this->payment_notes = '';
        $this->showPaymentModal = true;
    }

    public function closePaymentModal()
    {
        $this->showPaymentModal = false;
        $this->selectedId = null;
        $this->bank_account_id = null;
    }

    public function savePayment()
    {
        $this->validate([
            'payment_amount' => 'required|numeric|min:1|max:' . $this->remaining_debt,
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,transfer',
            'bank_account_id' => 'required_if:payment_method,transfer',
        ], [
            'payment_amount.max' => 'Jumlah bayar tidak boleh melebihi sisa hutang (Rp. ' . number_format($this->remaining_debt, 0, ',', '.') . ',-)',
            'bank_account_id.required_if' => 'Harap pilih akun bank untuk metode Transfer.',
        ]);

        try {
            $accountingService = new \App\Services\AccountingService();
            $accountingService->processSupplierPayment($this->selectedId, [
                'amount' => $this->payment_amount,
                'payment_method' => $this->payment_method,
                'account_id' => $this->bank_account_id,
                'date' => $this->payment_date,
                'notes' => $this->payment_notes,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to process supplier payment: ' . $e->getMessage());
            $this->addError('payment_amount', 'Gagal memproses pembayaran: ' . $e->getMessage());
            return;
        }

        $this->showPaymentModal = false;
        $this->selectedId = null;
        session()->flash('message', 'Pembayaran berhasil dicatat.');
    }

    
    public function updatingPerPage()
    {
        $this->resetPage();
    }
public function render()
    {
        $sortableColumns = [
            'received_date' => 'received_date',
            'delivery_note_number' => 'delivery_note_number',
            'payment_status' => 'payment_status',
            'total_amount' => 'total_amount',
        ];
        $orderColumn = $sortableColumns[$this->sortBy] ?? 'received_date';

        /** @var \Illuminate\Pagination\LengthAwarePaginator $receipts */
        $receipts = \App\Models\GoodsReceipt::with('purchaseOrder.supplier', 'user', 'items')
            ->where(function ($q) {
                $q->where('delivery_note_number', 'like', '%' . $this->search . '%')
                  ->orWhereHas('purchaseOrder.supplier', function ($q2) {
                      $q2->where('name', 'like', '%' . $this->search . '%');
                  });
            })
            ->when($this->dateFrom, function ($q) {
                $q->whereDate('received_date', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($q) {
                $q->whereDate('received_date', '<=', $this->dateTo);
            })
            ->orderBy($orderColumn, $this->sortDirection)
            ->paginate($this->perPage);
        $receipts->onEachSide(1);

        $selectedReceipt = null;
        if ($this->showDetailModal && $this->selectedId) {
            $selectedReceipt = \App\Models\GoodsReceipt::with(['items.product', 'items.unit', 'purchaseOrder.supplier', 'user'])->find($this->selectedId);
        }

        return view('livewire.procurement.goods-receipt-index', compact('receipts', 'selectedReceipt'));
    }
}
