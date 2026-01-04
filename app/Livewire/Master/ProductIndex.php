<?php

namespace App\Livewire\Master;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\ActivityLog;
use App\Models\Product;
use App\Models\Category;

#[Layout('layouts.app')]
class ProductIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $category_id = '';

    // History Modal
    public $showHistoryModal = false;
    public $historyProduct = null;
    public $sellPriceHistory = [];
    public $buyPriceHistory = [];

    public function viewHistory($productId)
    {
        $this->historyProduct = Product::find($productId);
        if (!$this->historyProduct) return;

        // Fetch Sell Price History from ActivityLog
        // We look for 'updated' action on this product where 'sell_price' is in old or new values
        // Note: 'created' action also sets initial price
        $this->sellPriceHistory = ActivityLog::where(function($q) use ($productId) {
                $q->where('subject_type', Product::class)
                  ->where('subject_id', $productId);
            })
            ->whereIn('action', ['created', 'updated'])
            ->with('user')
            ->latest()
            ->get()
            ->filter(function ($log) {
                if ($log->action === 'created') return true;
                
                // For updates, check if sell_price changed
                $oldPrice = $log->old_values['sell_price'] ?? null;
                $newPrice = $log->new_values['sell_price'] ?? null;
                
                // Allow fuzzy comparison for floats
                return abs((float)$oldPrice - (float)$newPrice) > 0.01;
            })
            ->map(function ($log) {
                return [
                    'date' => $log->created_at,
                    'user' => $log->user->name ?? 'Unknown',
                    'action' => $log->action,
                    'old_price' => $log->old_values['sell_price'] ?? 0,
                    'new_price' => $log->new_values['sell_price'] ?? 0,
                ];
            });

        // Fetch Buy Price History from Purchase Order Items
        // Join with Purchase Order to get Date and User?
        // PurchaseOrderItem has created_at, but actual transaction date is in PurchaseOrder->date
        $this->buyPriceHistory = \App\Models\PurchaseOrderItem::where('product_id', $productId)
            ->whereHas('purchaseOrder', function($q) {
                $q->whereNotIn('status', ['draft', 'cancelled']); // Only committed POs
            })
            ->with(['purchaseOrder.user', 'purchaseOrder.supplier'])
            ->latest() // This uses created_at of item, which is fine
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->purchaseOrder->date, // PO Date
                    'po_number' => $item->purchaseOrder->po_number,
                    'supplier' => $item->purchaseOrder->supplier->name ?? '-',
                    'user' => $item->purchaseOrder->user->name ?? '-',
                    'price' => $item->unit_price,
                    'unit' => $item->unit->name ?? '-', // Assuming unit relationship exists or logic needed
                ];
            });

        $this->showHistoryModal = true;
    }

    public function closeHistoryModal()
    {
        $this->showHistoryModal = false;
        $this->historyProduct = null;
        $this->sellPriceHistory = [];
        $this->buyPriceHistory = [];
    }

    public function mount()
    {
        if (!auth()->user()->can('view products')) {
            abort(403, 'Unauthorized');
        }
    }

    public function render()
    {
        $products = Product::with(['category', 'unit'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('barcode', 'like', '%' . $this->search . '%');
            })
            ->when($this->category_id, function ($query) {
                $query->where('category_id', $this->category_id);
            })
            ->latest()
            ->paginate(10)
            ->onEachSide(2);

        return view('livewire.master.product-index', [
            'products' => $products,
            'categories' => Category::all(),
        ]);
    }

    public function deleteProduct($id)
    {
        if (!auth()->user()->can('delete products')) {
            session()->flash('error', 'Anda tidak memiliki akses untuk menghapus produk.');
            return;
        }

        try {
            // Check for related stock movements
            // Stock movements represent critical transaction history (sales, adjustments, etc.)
            // and should not be orphaned.
            $hasMovements = \App\Models\StockMovement::where('product_id', $id)->exists();
            
            if ($hasMovements) {
                session()->flash('error', 'Produk tidak dapat dihapus karena sudah memiliki riwayat transaksi (stock movements).');
                return;
            }

            $product = Product::findOrFail($id);
            $oldData = $product->toArray();
            $product->delete();

            ActivityLog::log([
                'action' => 'deleted',
                'module' => 'products',
                'description' => "Menghapus obat: {$oldData['name']}",
                'old_values' => $oldData
            ]);
            
            session()->flash('message', 'Produk berhasil dihapus.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus produk: ' . $e->getMessage());
        }
    }
}
