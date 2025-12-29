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
