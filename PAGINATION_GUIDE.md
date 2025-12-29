# Custom Pagination Usage Guide

## Komponen Pagination Custom

Lokasi: `resources/views/components/custom-pagination.blade.php`

### Fitur:
- ✅ Maximum 5 halaman ditampilkan
- ✅ Tombol Next & Previous dengan state handling
- ✅ Responsive (mobile & desktop view)
- ✅ Auto-calculate page range
- ✅ Styled dengan Tailwind CSS

### Cara Pakai:

#### 1. Di Livewire Component (Controller)

```php
public function render()
{
    $products = Product::paginate(10); // 10 items per page
    
    return view('livewire.product-index', [
        'products' => $products
    ]);
}
```

#### 2. Di Blade View

Ganti pagination default Laravel dengan:

```blade
{{-- OLD: Default Laravel Pagination --}}
{{-- {{ $products->links() }} --}}

{{-- NEW: Custom Pagination --}}
@include('components.custom-pagination', ['items' => $products])
```

### Contoh Lengkap:

```blade
<div class="p-6">
    <!-- Your table or list -->
    <table class="min-w-full">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
                <tr>
                    <td>{{ $product->id }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->price }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <!-- Custom Pagination -->
    @include('components.custom-pagination', ['items' => $products])
</div>
```

### Behavior:

1. **Halaman 1-5**: Tampilkan 1, 2, 3, 4, 5
2. **Halaman 3 (dari 10)**: Tampilkan 1, 2, 3, 4, 5
3. **Halaman 6 (dari 10)**: Tampilkan 4, 5, 6, 7, 8
4. **Halaman 10 (dari 10)**: Tampilkan 6, 7, 8, 9, 10

Navigasi ke halaman di luar range hanya bisa via tombol Next/Previous!

## Purchase Order Form Update

**File**: `resources/views/livewire/procurement/purchase-order-form.blade.php`

**Perubahan**:
- ✅ Input Harga Satuan sekarang menggunakan placeholder yang lebih jelas
- ✅ Placeholder: "Masukkan harga satuan" (bukan "0")
- ✅ Tidak ada default value lagi
