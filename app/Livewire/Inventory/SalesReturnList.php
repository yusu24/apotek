<?php

namespace App\Livewire\Inventory;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Sale;
use App\Models\SalesReturn;
use App\Models\SalesReturnItem;
use App\Models\Batch;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use App\Models\ActivityLog;

class SalesReturnList extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    
    // New Return Form
    public $invoiceSearch = '';
    public $selectedSale = null;
    public $returnItems = [];
    public $notes = '';

    protected $rules = [
        'invoiceSearch' => 'nullable|string',
        'notes' => 'nullable|string',
        'returnItems.*.quantity' => 'numeric|min:0',
    ];

    public function updatedInvoiceSearch()
    {
        if (strlen($this->invoiceSearch) > 3) {
            $this->selectedSale = Sale::with('saleItems.product', 'saleItems.batch')
                ->where('invoice_no', $this->invoiceSearch)
                ->first();
            
            if ($this->selectedSale) {
                $this->returnItems = [];
                foreach ($this->selectedSale->saleItems as $item) {
                    $this->returnItems[$item->id] = [
                        'quantity' => 0,
                        'max_quantity' => $item->quantity,
                        'price' => $item->sell_price,
                        'product_id' => $item->product_id,
                        'batch_id' => $item->batch_id,
                        'name' => $item->product->name,
                        'batch_no' => $item->batch ? $item->batch->batch_no : '-'
                    ];
                }
            } else {
                $this->selectedSale = null;
                $this->returnItems = [];
            }
        }
    }

    public function openModal()
    {
        $this->reset(['selectedSale', 'returnItems', 'invoiceSearch', 'notes']);
        $this->showModal = true;
    }

    public function saveReturn()
    {
        if (!$this->selectedSale) return;

        $totalReturnAmount = 0;
        $itemsToProcess = array_filter($this->returnItems, function($item) {
            return $item['quantity'] > 0;
        });

        if (empty($itemsToProcess)) {
            $this->addError('returnItems', 'Minimal satu barang harus diretur.');
            return;
        }

        foreach ($itemsToProcess as $id => $item) {
            if ($item['quantity'] > $item['max_quantity']) {
                $this->addError("returnItems.{$id}.quantity", "Jumlah retur melebihi jumlah beli.");
                return;
            }
            $totalReturnAmount += $item['quantity'] * $item['price'];
        }

        DB::beginTransaction();
        try {
            $salesReturn = SalesReturn::create([
                'sale_id' => $this->selectedSale->id,
                'return_no' => 'SR-' . date('YmdHis'),
                'user_id' => auth()->id(),
                'total_amount' => $totalReturnAmount,
                'notes' => $this->notes,
            ]);

            foreach ($itemsToProcess as $id => $item) {
                SalesReturnItem::create([
                    'sales_return_id' => $salesReturn->id,
                    'product_id' => $item['product_id'],
                    'batch_id' => $item['batch_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);

                // Update Stock
                if ($item['batch_id']) {
                    $batch = Batch::find($item['batch_id']);
                    if ($batch) {
                        $batch->increment('stock_current', $item['quantity']);
                    }
                }

                // Record Stock Movement
                StockMovement::create([
                    'product_id' => $item['product_id'],
                    'batch_id' => $item['batch_id'],
                    'type' => 'in',
                    'quantity' => $item['quantity'],
                    'reference' => $salesReturn->return_no,
                    'notes' => 'Retur Penjualan INV: ' . $this->selectedSale->invoice_no,
                    'user_id' => auth()->id(),
                ]);
            }

            ActivityLog::log([
                'action' => 'created',
                'module' => 'sales_returns',
                'description' => "Membuat retur penjualan: {$salesReturn->return_no} untuk Invoice: {$this->selectedSale->invoice_no}",
                'new_values' => $salesReturn->toArray()
            ]);

            DB::commit();
            session()->flash('message', 'Retur penjualan berhasil disimpan.');
            $this->reset(['showModal', 'selectedSale', 'returnItems', 'invoiceSearch', 'notes']);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $returns = SalesReturn::with('sale', 'user')
            ->where('return_no', 'like', '%' . $this->search . '%')
            ->orWhereHas('sale', function($q) {
                $q->where('invoice_no', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('livewire.inventory.sales-return-list', [
            'returns' => $returns
        ]);
    }
}
