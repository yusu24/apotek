<?php

namespace App\Livewire\Master;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
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

    public function delete($id)
    {
        if (!auth()->user()->can('delete products')) {
            session()->flash('error', 'Anda tidak memiliki akses untuk menghapus produk.');
            return;
        }

        try {
            Product::find($id)->delete();
            session()->flash('message', 'Product deleted successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Cannot delete product: ' . $e->getMessage());
        }
    }
}
