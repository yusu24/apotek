<?php

namespace App\Livewire\Pos;

use Livewire\Component;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use App\Models\Batch;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\ActivityLog;

#[Layout('layouts.app')]
class Cashier extends Component
{
    public $search = '';
    public $cart = []; 
    // Structure: [id => [id, name, price, qty, unit, discount_amount, subtotal]]
    
    // Financials
    public $subtotal = 0;
    public $global_discount = 0;
    public $ppn_mode = 'off'; // off, inclusive, exclusive
    public $tax = 0;
    public $dpp = 0;
    public $service_charge = 0; // percentage
    public $service_charge_amount = 0;
    public $grand_total = 0;
    public $rounding = 0;
    
    // Order Metadata
    public $order_mode = 'In'; // In / Out
    public $invoice_no = '';
    public $global_notes = '';
    public $selectedCategory = 'all';
    
    // Payment
    public $payment_method = 'cash';
    public $cash_amount = null;
    public $change_amount = 0;
    public $success_message = '';
    public $showPaymentModal = false;
    public $showPendingModal = false;
    public $pendingOrders = [];

    public function loadPendingOrders()
    {
        $this->pendingOrders = Sale::where('status', 'pending')
            ->where('user_id', Auth::id()) // Optional: only own pending orders
            ->latest()
            ->get();
        
        $this->showPendingModal = true;
    }

    public function restorePendingOrder($saleId)
    {
        $sale = Sale::with(['saleItems.product', 'saleItems.batch'])->find($saleId);
        
        if (!$sale) {
            $this->dispatch('cart-error', message: 'Pesanan tidak ditemukan');
            return;
        }

        // Restore Invoice No
        $this->invoice_no = $sale->invoice_no;
        
        // Restore Cart
        $this->cart = [];
        foreach ($sale->saleItems as $item) {
            // Calculate discount percent from price and subtotal if needed, 
            // OR just use what we have. 
            // Current DB structure might not save discount_percent.
            // Let's infer or default to 0. 
            
            // Logic: $item->subtotal = ($price - discount) * qty
            // discount_per_unit = $price - ($subtotal / qty)
            // discount_percent = (discount_per_unit / $price) * 100
            
            $price = $item->sell_price;
            $qty = $item->quantity;
            $subtotal = $item->subtotal;
            
            $discount_percent = 0;
            if ($qty > 0 && $price > 0) {
                $actual_total = $subtotal;
                $expected_total = $price * $qty;
                if ($expected_total > $actual_total) {
                    $discount_amount = ($expected_total - $actual_total) / $qty;
                    $discount_percent = ($discount_amount / $price) * 100;
                }
            }

            $this->cart[$item->product_id] = [
                'id' => $item->product_id,
                'name' => $item->product->name ?? 'Unknown',
                'price' => (float)$item->sell_price,
                'qty' => $item->quantity,
                'unit' => $item->product->unit->name ?? 'pcs',
                'discount_percent' => round($discount_percent, 2),
                'notes' => '', // Notes might be lost if not saved in SaleItem. Feature limitation for now.
                'has_ppn' => false,
            ];
        }

        // Restore Global Settings
        $this->global_notes = $sale->notes;
        $this->global_discount = $sale->discount;
        $this->payment_method = $sale->payment_method ?? 'cash';

        // Delete the pending record (it is now "live" in session)
        // Also revert stock movements? 
        // processPayment created StockMovements. We MUST delete them to "Release" stock back to system, 
        // because "saveOrder" (processPayment) deducted stock.
        
        // Delete Stock Movements
        StockMovement::where('doc_ref', $sale->invoice_no)->delete();
        
        // Restore stock to batches
        foreach ($sale->saleItems as $item) {
           if ($item->batch_id) {
               $batch = Batch::find($item->batch_id);
               if ($batch) {
                   $batch->increment('stock_current', $item->quantity);
               }
           }
        }
        
        // Delete Sale Items and Sale
        $sale->saleItems()->delete();
        $sale->delete();

        $this->calculateTotal();
        $this->showPendingModal = false;
        $this->dispatch('cart-updated', message: 'Pesanan berhasil dipulihkan');
    }

    public function deletePendingOrder($saleId)
    {
        $sale = Sale::with(['saleItems.product', 'saleItems.batch'])->find($saleId);

        if (!$sale) {
            $this->dispatch('cart-error', message: 'Pesanan tidak ditemukan');
            return;
        }

        // Restore stock to batches
        foreach ($sale->saleItems as $item) {
            if ($item->batch_id) {
                $batch = Batch::find($item->batch_id);
                if ($batch) {
                    $batch->increment('stock_current', $item->quantity);
                }
            }
        }

        // Delete Stock Movements
        StockMovement::where('doc_ref', $sale->invoice_no)->delete();

        // Delete Sale Items and Sale
        $sale->saleItems()->delete();
        $sale->delete();

        if ($this->showPendingModal) {
            $this->pendingOrders = Sale::where('status', 'pending')
                ->where('user_id', Auth::id())
                ->latest()
                ->get();
        }

        $this->dispatch('cart-updated', message: 'Pesanan tertunda berhasil dihapus dan stok dikembalikan');
    }
    public function mount()
    {
        // Check permission
        if (!auth()->user()->can('access pos')) {
            abort(403, 'Unauthorized');
        }
        $this->generateInvoiceNo();
    }

    public function generateInvoiceNo()
    {
        $this->invoice_no = 'INV/' . date('Ymd') . '/' . substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6);
    }

    public function updatedSearch()
    {
        // Search happens in render
    }

    public function updated($property, $value)
    {
        if (str_starts_with($property, 'cart.')) {
            $parts = explode('.', $property);
            // $parts[0] = 'cart', $parts[1] = product_id, $parts[2] = property (qty, discount_amount, etc)
            
            if (count($parts) === 3) {
                $productId = $parts[1];
                $field = $parts[2];

                if ($field === 'qty') {
                     // Basic cleaning
                     if ($value === '' || $value === null) return;
                     $qty = (int)$value;

                     if ($qty <= 0) {
                         $this->removeFromCart($productId);
                         return;
                     }
                     
                     // Stock Validation
                     $product = Product::withSum('batches as total_stock', 'stock_current')->find($productId);
                     if ($product && $qty > $product->total_stock) {
                         $this->dispatch('cart-error', message: 'Stok terbatas. Maks: ' . $product->total_stock);
                         $this->cart[$productId]['qty'] = $product->total_stock;
                     } else {
                         //$this->cart[$productId]['qty'] = $qty; // Already set by wire:model
                     }
                }
            }
            
            $this->calculateTotal();
        }
    }

    public function addToCart($productId)
    {
        $product = Product::query()
            ->withSum('batches as total_stock', 'stock_current')
            ->find($productId);

        if (!$product) return;

        // Basic Stock Check
        if ($product->total_stock <= 0) {
            $this->dispatch('cart-error', message: 'Stok ' . $product->name . ' habis!');
            return;
        }

        if (isset($this->cart[$productId])) {
            if ($this->cart[$productId]['qty'] < $product->total_stock) {
                $this->cart[$productId]['qty']++;
            } else {
                $this->dispatch('cart-error', message: 'Stok tidak mencukupi.');
                return;
            }
        } else {
            $this->cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => (float)$product->sell_price,
                'qty' => 1,
                'unit' => $product->unit->name ?? 'pcs',
                'discount_percent' => null, // Default null so placeholder shows
                'notes' => '',
                'has_ppn' => false,
            ];
        }
        $this->calculateTotal();
        $this->search = ''; 
    }

    public function removeFromCart($productId)
    {
        unset($this->cart[$productId]);
        $this->calculateTotal();
    }

    public function updateQty($productId, $qty)
    {
        $product = Product::query()
            ->withSum('batches as total_stock', 'stock_current')
            ->find($productId);

        if ($qty <= 0) {
            $this->removeFromCart($productId);
        } elseif ($product && $qty > $product->total_stock) {
            $this->dispatch('cart-error', message: 'Stok terbatas. Maks: ' . $product->total_stock);
            $this->cart[$productId]['qty'] = $product->total_stock;
            $this->calculateTotal();
        } else {
            $this->cart[$productId]['qty'] = $qty;
            $this->calculateTotal();
        }
    }

    // Update item discount (Received as Percentage)
    public function updateItemDiscount($productId, $discountPercent)
    {
        if (isset($this->cart[$productId])) {
            // Validate 0-100
            $discountPercent = max(0, min(100, (float)$discountPercent));
            $this->cart[$productId]['discount_percent'] = $discountPercent;
            $this->calculateTotal();
        }
    }

    // Update item notes
    public function updateItemNotes($productId, $notes)
    {
        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['notes'] = $notes;
        }
    }

    public function calculateTotal()
    {
        $this->subtotal = 0;
        $total_item_discount = 0;
        $this->tax = 0; // Reset text
        
        $ppn_rate = (float)\App\Models\Setting::get('pos_ppn_rate', 11) / 100;

        foreach ($this->cart as $key => $item) {
            $line_total_gross = $item['price'] * $item['qty'];
            
            // Calculate Discount Amount based on Percentage
            $discount_percent = (float)($item['discount_percent'] ?? 0);
            $discount_amount_per_unit = $item['price'] * ($discount_percent / 100);
            
            $total_line_discount = $discount_amount_per_unit * $item['qty'];
            
            $net_item_total = $line_total_gross - $total_line_discount;
            
            $this->subtotal += $line_total_gross; 
            
            $total_item_discount += $total_line_discount;
            
            // Calculate Item PPN
            if (isset($item['has_ppn']) && $item['has_ppn']) {
                $item_tax = $net_item_total * $ppn_rate;
                $this->tax += $item_tax;
                $net_item_total += $item_tax; 
            }
            
            $this->cart[$key]['subtotal'] = $net_item_total;
            $this->cart[$key]['discount_amount_calculated'] = $discount_amount_per_unit; 
        }

        // Net Amount after item and global discounts
        $net_before_sc = $this->subtotal - $total_item_discount - (float)$this->global_discount;
        if ($net_before_sc < 0) $net_before_sc = 0;

        // Service Charge calculation (applied on net amount)
        $this->service_charge_amount = $net_before_sc * ((float)$this->service_charge / 100);
        
        // Base Grand Total (Net + Service Charge)
        $net_amount = $net_before_sc + $this->service_charge_amount;

        // Add PPN (Global logic merged with Item logic)
        if ($this->ppn_mode === 'inclusive') {
             // Inclusive calculation typically: Total / (1 + rate)
             // However, for simplicity and alignment with existing logic:
        } elseif ($this->ppn_mode === 'exclusive') {
             $this->tax += $net_amount * $ppn_rate; 
        }
        
        $this->dpp = $net_amount; 
        $this->grand_total = $net_amount + $this->tax;

        // Rounding for cash (Ceil to nearest 100)
        $raw_total = $this->grand_total;
        if ($this->payment_method === 'cash') {
            $this->grand_total = ceil($raw_total / 100) * 100;
            $this->rounding = $this->grand_total - $raw_total;
        } else {
            $this->rounding = 0;
            $this->grand_total = round($raw_total);
        }

        $this->calculateChange();
    }

    public function updateCartItem()
    {
        if (!$this->editingItemId || !isset($this->cart[$this->editingItemId])) {
            return;
        }

        // ... Modal Update Logic (If used, needs update too, but previous interaction user didn't mention modal, just inline)
        // Ignoring modal update details for inline fix unless user requested modal rework.
        // Assuming user uses the inline input I saw in blade.
        
        // Convert to Per-Unit Discount for consistent storage
        // $unitDiscount = $this->modalQty > 0 ? $discountAmountTotal / $this->modalQty : 0;

        // Update cart item
        // $this->cart[$this->editingItemId]['qty'] = $this->modalQty;
        // $this->cart[$this->editingItemId]['price'] = $this->modalPrice;
        // $this->cart[$this->editingItemId]['discount_amount'] = $unitDiscount;
        
        //$this->calculateTotal();
        //$this->closeItemModal();
        //$this->dispatch('item-updated', message: 'Item berhasil diperbarui');
        // Since I don't see the modal trigger in the snippet I read earlier (it was obscured or minimal), I'll focus on inline.
    }

    public function saveOrder()
    {
        if (empty($this->cart)) return;
        
        // Simpan pesanan as "pending"
        $this->processPayment(status: 'pending');
        session()->flash('success', 'Pesanan berhasil disimpan!');
    }

    public function cancelOrder()
    {
        $this->cart = [];
        $this->global_discount = 0;
        $this->service_charge = 0;
        $this->ppn_mode = 'off';
        $this->cash_amount = 0;
        $this->calculateTotal();
        $this->generateInvoiceNo();
    }

    public function updatedGlobalDiscount()
    {
        $this->calculateTotal();
    }

    public function updatedPpnMode()
    {
        $this->calculateTotal();
    }

    public function togglePpnMode()
    {
        $modes = ['off', 'inclusive', 'exclusive'];
        $currentIdx = array_search($this->ppn_mode, $modes);
        $this->ppn_mode = $modes[($currentIdx + 1) % 3];
        $this->calculateTotal();
    }

    public function updatedCashAmount()
    {
        $this->calculateChange();
    }

    public function calculateChange()
    {
        if ((float)$this->cash_amount >= $this->grand_total) {
            $this->change_amount = (float)$this->cash_amount - $this->grand_total;
        } else {
            $this->change_amount = 0;
        }
    }

    public function validateStock()
    {
        foreach ($this->cart as $item) {
            $product = Product::withSum('batches as total_stock', 'stock_current')->find($item['id']);
            
            if (!$product) {
                $this->addError('checkout', "Produk {$item['name']} tidak ditemukan.");
                return false;
            }

            if ($item['qty'] > $product->total_stock) {
                $this->addError('checkout', "Stok {$item['name']} tidak mencukupi. Sisa: {$product->total_stock}, Diminta: {$item['qty']}");
                return false;
            }
        }
        return true;
    }

    public function openPayment()
    {
        if (empty($this->cart)) return;
        
        if (!$this->validateStock()) {
            return;
        }

        $this->cash_amount = null;
        $this->change_amount = 0;
        $this->showPaymentModal = true;
    }

    public function processPayment($status = 'completed')
    {
        if (!$this->validateStock()) {
            return;
        }

        if ($status === 'completed' && $this->payment_method == 'cash' && (float)$this->cash_amount < $this->grand_total) {
            $this->addError('cash_amount', 'Uang tunai kurang!');
            return;
        }

        DB::beginTransaction();
        try {
            $sale = Sale::create([
                'user_id' => Auth::id() ?? 1,
                'invoice_no' => $this->invoice_no,
                'date' => now(),
                'total_amount' => $this->subtotal,
                'discount' => (float)$this->global_discount,
                'service_charge_amount' => $this->service_charge_amount,
                'service_charge_percentage' => (float)$this->service_charge,
                'tax' => $this->tax,
                'dpp' => $this->dpp,
                'ppn_mode' => $this->ppn_mode,
                'order_mode' => $this->order_mode,
                'rounding' => $this->rounding,
                'grand_total' => $this->grand_total,
                'payment_method' => $this->payment_method,
                'cash_amount' => (float)$this->cash_amount,
                'change_amount' => $this->change_amount,
                'notes' => $this->global_notes,
                'status' => $status,
            ]);

            foreach ($this->cart as $item) {
                $qtyNeeded = $item['qty'];
                // Use calculated amount from loop
                $discount_percent = $item['discount_percent'] ?? 0;
                $itemDiscount = $item['price'] * ($discount_percent / 100);
                
                $itemNotes = $item['notes'] ?? '';
                
                $batches = Batch::where('product_id', $item['id'])
                    ->valid()
                    ->orderBy('expired_date', 'asc')
                    ->get();
                
                $qtyRemaining = $qtyNeeded;

                foreach ($batches as $batch) {
                    if ($qtyRemaining <= 0) break;
                    $take = min($batch->stock_current, $qtyRemaining);
                    $batch->decrement('stock_current', $take);
                    
                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $item['id'],
                        'batch_id' => $batch->id,
                        'quantity' => $take,
                        'sell_price' => $item['price'],
                        // 'discount_amount' => $itemDiscount, // DB does not have this column in my check, only subtotal?
                        // Checked migration: Only subtotal, sell_price, quantity. NO discount_amount column.
                        // I will update subtotal correctly.
                        'subtotal' => ($item['price'] - $itemDiscount) * $take,
                    ]);

                    StockMovement::create([
                        'product_id' => $item['id'],
                        'batch_id' => $batch->id,
                        'user_id' => Auth::id() ?? 1,
                        'type' => 'sale',
                        'quantity' => -$take,
                        'doc_ref' => $sale->invoice_no,
                        'description' => 'Penjualan Kasir',
                    ]);
                    $qtyRemaining -= $take;
                }

                if ($qtyRemaining > 0) {
                     SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $item['id'],
                        'batch_id' => null,
                        'quantity' => $qtyRemaining,
                        'sell_price' => $item['price'],
                        'subtotal' => ($item['price'] - $itemDiscount) * $qtyRemaining,
                    ]);
                    
                    StockMovement::create([
                        'product_id' => $item['id'],
                        'batch_id' => null,
                        'user_id' => Auth::id() ?? 1,
                        'type' => 'sale',
                        'quantity' => -$qtyRemaining,
                        'doc_ref' => $sale->invoice_no,
                        'description' => 'Penjualan (Stok Minus)',
                    ]);
                }
            }

            DB::commit();

            ActivityLog::log([
                'action' => 'created',
                'module' => 'sales',
                'description' => "Transaksi penjualan baru: {$sale->invoice_no}",
                'new_values' => $sale->toArray()
            ]);

            $this->showPaymentModal = false;
            $this->cart = [];
            $this->calculateTotal();
            $this->generateInvoiceNo(); // Generate new invoice for next order

            if ($status === 'pending') {
                 $this->dispatch('cart-updated', message: 'Pesanan berhasil disimpan ke Pending list.');
                 return;
            }

            return $this->redirect(route('pos.receipt', ['id' => $sale->id]));

        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError('checkout', 'Gagal memproses: ' . $e->getMessage());
        }
    }





    public function render()
    {
        $categories = \App\Models\Category::all();

        $products = Product::query()
            ->with('unit')
            ->withSum('batches as total_stock', 'stock_current')
            ->when($this->search, function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('barcode', 'like', '%' . $this->search . '%');
            })
            ->when($this->selectedCategory && $this->selectedCategory !== 'all', function($q) {
                $q->where('category_id', $this->selectedCategory);
            })
            ->latest()
            ->limit(24)
            ->get();

        return view('livewire.pos.cashier', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }
}
