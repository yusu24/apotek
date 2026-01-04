<?php

namespace App\Livewire\Master;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Product;
use App\Models\Category;
use App\Models\Unit;
use App\Models\ActivityLog;
use Illuminate\Support\Str;

use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

#[Layout('layouts.app')]
class ProductForm extends Component
{
    use WithFileUploads;

    public $product_id;
    public $category_id;
    public $unit_id;
    public $name;
    public $barcode;
    public $min_stock; // Default null
    public $sell_price; // Default null
    public $description;
    public $image; // Temporary uploaded file
    public $current_image_path; // Existing path from DB
    public $delete_image = false; // Flag to delete image

    public $canEditPrice = false;
    public $canUploadImage = false;

    // Wholesale pricing per unit
    public $unitPrices = []; // ['unit_conversion_id' => ['unit_name' => 'Box', 'calculated_price' => 100000, 'wholesale_price' => 90000]]

    // Hook to generate barcode when dependencies change
    public function updatedCategoryId() { $this->generateBarcode(); }
    public function updatedUnitId() { $this->generateBarcode(); }
    public function updatedName() { $this->generateBarcode(); }

    public function generateBarcode()
    {
        // Only auto-generate if we have minimum requirements and we are NOT editing an existing valid barcode (optional, but safer to always regen if user is typing name on create?)
        // User request implies auto generation. Let's do it if create mode or if user consciously triggers it?
        // Usually better to only auto-gen on CREATE or if field is empty to avoid overwriting existing custom barcodes.
        // But user constraint is specific "hasilnya : OBSTVIT001".
        
        if ($this->product_id) return; // Don't auto-change barcode on edit to prevent breaking labels already printed

        if ($this->category_id && $this->unit_id && strlen($this->name) >= 3) {
            $category = Category::find($this->category_id);
            $unit = Unit::find($this->unit_id);

            if ($category && $unit) {
                // Cat (2 chars) + Unit (2 chars) + Name (3 chars)
                $prefix = strtoupper(substr($category->name, 0, 2) . substr($unit->name, 0, 2) . substr($this->name, 0, 3));
                
                // Count existing to determine sequence
                // We look for barcodes starting with this prefix
                $count = Product::where('barcode', 'like', $prefix . '%')->count();
                $sequence = $count + 1;
                
                $this->barcode = $prefix . sprintf('%03d', $sequence);
            }
        }
    }

    public function mount($id = null)
    {
        if ($id) {
            if (!auth()->user()->can('edit products')) {
                abort(403, 'Unauthorized');
            }
        } else {
            if (!auth()->user()->can('create products')) {
                abort(403, 'Unauthorized');
            }
        }

        // Check if user can edit price (Super Admin only)
        $this->canEditPrice = auth()->user()->hasRole('super-admin');
        
        // Check permission for image upload
        $this->canUploadImage = auth()->user()->can('upload product images');

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
            $this->current_image_path = $product->image_path;

            // Load unit conversions and their wholesale prices
            $this->loadUnitPrices();
        }
    }

    public function loadUnitPrices()
    {
        if (!$this->product_id) {
            $this->unitPrices = [];
            return;
        }

        $product = Product::with(['unitConversions.fromUnit'])->find($this->product_id);
        
        $this->unitPrices = $product->unitConversions->mapWithKeys(function ($conversion) use ($product) {
            $calculatedPrice = $product->sell_price * $conversion->conversion_factor;
            
            return [$conversion->id => [
                'unit_name' => $conversion->fromUnit->name ?? '-',
                'conversion_factor' => $conversion->conversion_factor,
                'calculated_price' => $calculatedPrice,
                'wholesale_price' => $conversion->wholesale_price ?? null,
            ]];
        })->toArray();
    }

    public function rules()
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'name' => 'required|min:3',
            'barcode' => 'required|unique:products,barcode,' . $this->product_id,
            'min_stock' => 'required|integer|min:1', // Tidak boleh 0
            'sell_price' => 'required|numeric|min:1', // Tidak boleh 0
            'description' => 'nullable',
            'image' => 'nullable|image|max:2048', // 2MB Max
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

        // Only Super Admin can modify price and min_stock on existing products.
        // For NEW products, anyone allowed to create can set the initial values.
        if ($this->canEditPrice || !$this->product_id) {
            $data['min_stock'] = $this->min_stock;
            $data['sell_price'] = $this->sell_price;
        }

        // Handle Image Deletion
        if ($this->delete_image && $this->current_image_path) {
            if (Storage::disk('public')->exists($this->current_image_path)) {
                Storage::disk('public')->delete($this->current_image_path);
            }
            $data['image_path'] = null;
            $this->current_image_path = null;
        }

        // Handle Image Upload
        if ($this->image && $this->canUploadImage) {
            // Delete old image if replacing
            if ($this->current_image_path && Storage::disk('public')->exists($this->current_image_path)) {
                Storage::disk('public')->delete($this->current_image_path);
            }
            $path = $this->image->store('products', 'public');
            $data['image_path'] = $path;
        }

        if ($this->product_id) {
            $product = Product::find($this->product_id);
            $oldData = $product->toArray();
            $product->update($data);
            
            ActivityLog::log([
                'action' => 'updated',
                'module' => 'products',
                'description' => "Memperbarui obat: {$this->name}",
                'old_values' => $oldData,
                'new_values' => $data,
                'subject_id' => $product->id,
                'subject_type' => Product::class,
            ]);

            session()->flash('message', 'Obat berhasil diperbarui.');
        } else {
            $product = Product::create($data);

            ActivityLog::log([
                'action' => 'created',
                'module' => 'products',
                'description' => "Menambah obat baru: {$this->name}",
                'new_values' => $data,
                'subject_id' => $product->id,
                'subject_type' => Product::class,
            ]);

            session()->flash('message', 'Obat berhasil ditambahkan.');
        }

        // Sync wholesale prices to unit_conversions (only for existing product)
        if ($this->product_id && $this->canEditPrice) {
            foreach ($this->unitPrices as $conversionId => $priceData) {
                \App\Models\UnitConversion::where('id', $conversionId)
                    ->update(['wholesale_price' => $priceData['wholesale_price'] ?? null]);
            }
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
