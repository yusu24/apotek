<?php

namespace App\Livewire\Master;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Product;
use App\Models\Category;
use App\Models\Unit;
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
        }
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
            // If we are updating but not uploading new image/deleting, do not overwrite image_path with null unless explicit delete
            // (Wait, update usually only updates keys present in data. Above logic sets data[image_path] only if valid.)
            
            // Actually, if delete_image is true, we set image_path = null.
            // If uploading image, we set image_path = new path.
            // If neither, we simply DON'T include image_path in $data, so it persists.
            
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
