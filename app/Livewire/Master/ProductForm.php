<?php

namespace App\Livewire\Master;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Product;
use App\Models\Category;
use App\Models\Unit;
use Illuminate\Support\Str;

#[Layout('layouts.app')]
class ProductForm extends Component
{
    public $product_id;
    public $category_id;
    public $unit_id;
    public $name;
    public $barcode;
    public $min_stock = 0;
    public $sell_price = 0;
    public $description;

    public $canEditPrice = false;

    public function mount($id = null)
    {
        // Check if user can edit price (Super Admin only)
        $this->canEditPrice = auth()->user()->hasRole('super-admin');

        if ($id) {
            $product = Product::findOrFail($id);
            $this->product_id = $product->id;
            $this->category_id = $product->category_id;
            $this->unit_id = $product->unit_id;
            $this->name = $product->name;
            $this->barcode = $product->barcode;
            $this->min_stock = $product->min_stock;
            $this->sell_price = $product->sell_price;
            $this->description = $product->description;
        }
    }

    public function rules()
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'name' => 'required|min:3',
            'barcode' => 'nullable|unique:products,barcode,' . $this->product_id,
            'min_stock' => 'required|integer|min:0',
            'sell_price' => 'required|numeric|min:0',
            'description' => 'nullable',
        ];
    }

    public function save()
    {
        $this->validate();

        $data = [
            'category_id' => $this->category_id,
            'unit_id' => $this->unit_id,
            'name' => $this->name,
            'slug' => Str::slug($this->name),
            'barcode' => $this->barcode,
            'description' => $this->description,
        ];

        // Only Super Admin can update price and min_stock
        if ($this->canEditPrice) {
            $data['min_stock'] = $this->min_stock;
            $data['sell_price'] = $this->sell_price;
        }

        if ($this->product_id) {
            Product::find($this->product_id)->update($data);
            session()->flash('message', 'Obat berhasil diperbarui.');
        } else {
            Product::create($data);
            session()->flash('message', 'Obat berhasil ditambahkan.');
        }

        return redirect()->route('products.index');
    }

    public function render()
    {
        return view('livewire.master.product-form', [
            'categories' => Category::all(),
            'units' => Unit::all(),
        ]);
    }
}
