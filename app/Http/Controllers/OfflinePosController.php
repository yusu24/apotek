<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Batch;
use App\Models\StockMovement;
use App\Models\Receivable;
use App\Models\Setting;
use App\Models\ActivityLog;
use App\Services\AccountingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OfflinePosController extends Controller
{
    /**
     * Get full product catalog, categories, customers, and store settings
     * for client-side LocalStorage cache in Offline POS mode.
     */
    public function catalog()
    {
        $products = Product::query()
            ->with(['unit', 'unitConversions.fromUnit'])
            ->withSum(['batches as total_stock' => function ($q) {
                $q->valid();
            }], 'stock_current')
            ->orderBy('name')
            ->get()
            ->map(function ($product) {
                $availableUnits = [];
                $availableUnits[$product->unit_id] = [
                    'id' => $product->unit_id,
                    'name' => $product->unit->name ?? 'Pcs',
                    'factor' => 1,
                    'price' => (float) $product->sell_price,
                    'wholesale_price' => null,
                ];

                foreach ($product->unitConversions as $conv) {
                    $factor = (float) $conv->conversion_factor;
                    $wPrice = (float) $conv->wholesale_price ?: null;
                    $availableUnits[$conv->from_unit_id] = [
                        'id' => $conv->from_unit_id,
                        'name' => $conv->fromUnit->name ?? '-',
                        'factor' => $factor,
                        'price' => $wPrice ? $wPrice : ((float) $product->sell_price * $factor),
                        'wholesale_price' => $wPrice,
                    ];
                }

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'barcode' => $product->barcode ?? '',
                    'category_id' => $product->category_id,
                    'sell_price' => (float) $product->sell_price,
                    'unit_id' => $product->unit_id,
                    'unit_name' => $product->unit->name ?? 'Pcs',
                    'total_stock' => (float) ($product->total_stock ?? 0),
                    'image' => $product->image ? asset('storage/' . $product->image) : null,
                    'available_units' => $availableUnits,
                ];
            });

        $categories = Category::orderBy('name')->get(['id', 'name']);
        $customers = Customer::orderBy('name')->get(['id', 'name', 'phone', 'address']);

        $settings = [
            'store_name' => Setting::get('store_name', config('app.name', 'Apotek')),
            'store_address' => Setting::get('store_address', '-'),
            'store_phone' => Setting::get('store_phone', '-'),
            'ppn_rate' => (float) Setting::get('pos_ppn_rate', 11),
            'ppn_mode' => Setting::get('pos_ppn_mode', 'off'),
            'paper_size' => Setting::get('pos_paper_size', '58mm'),
            'cashier_id' => Auth::id() ?? 1,
            'cashier_name' => Auth::user()->name ?? 'Kasir',
            'synced_at' => now()->toIso8601String(),
        ];

        return response()->json([
            'success' => true,
            'products' => $products,
            'categories' => $categories,
            'customers' => $customers,
            'settings' => $settings,
        ]);
    }

    /**
     * Synchronize queued offline transactions to the database.
     */
    public function sync(Request $request)
    {
        $transactions = $request->input('transactions', []);
        if (empty($transactions) || !is_array($transactions)) {
            return response()->json([
                'success' => true,
                'synced_ids' => [],
                'message' => 'Tidak ada transaksi untuk disinkronkan.',
            ]);
        }

        $syncedIds = [];
        $errors = [];

        foreach ($transactions as $tx) {
            $offlineId = $tx['offline_id'] ?? ($tx['invoice_no'] ?? uniqid('OFF-'));
            $invoiceNo = $tx['invoice_no'] ?? ('INV/' . date('Ymd') . '/' . strtoupper(substr(md5($offlineId), 0, 6)));

            // Idempotency Check: Skip if invoice already synced previously
            if (Sale::where('invoice_no', $invoiceNo)->exists()) {
                $syncedIds[] = $offlineId;
                continue;
            }

            DB::beginTransaction();
            try {
                $customerId = $tx['customer_id'] ?? null;
                $paymentMethod = $tx['payment_method'] ?? 'cash';

                // Create new customer if Tempo and inline new_customer provided
                if ($paymentMethod === 'tempo' && !$customerId && !empty($tx['new_customer_name'])) {
                    $customer = Customer::create([
                        'name' => $tx['new_customer_name'],
                        'phone' => $tx['new_customer_phone'] ?? null,
                        'address' => $tx['new_customer_address'] ?? null,
                    ]);
                    $customerId = $customer->id;
                }

                $txDate = !empty($tx['created_at']) ? date('Y-m-d H:i:s', strtotime($tx['created_at'])) : now()->format('Y-m-d H:i:s');
                $userId = Auth::id() ?? ($tx['cashier_id'] ?? 1);

                $sale = Sale::create([
                    'user_id' => $userId,
                    'customer_id' => $customerId ?: null,
                    'invoice_no' => $invoiceNo,
                    'date' => $txDate,
                    'total_amount' => (float) ($tx['subtotal'] ?? 0),
                    'discount' => (float) ($tx['global_discount'] ?? 0),
                    'service_charge_amount' => 0,
                    'service_charge_percentage' => 0,
                    'tax' => (float) ($tx['tax'] ?? 0),
                    'dpp' => (float) ($tx['dpp'] ?? ($tx['grand_total'] ?? 0)),
                    'ppn_mode' => $tx['ppn_mode'] ?? 'off',
                    'order_mode' => 'In',
                    'rounding' => (float) ($tx['rounding'] ?? 0),
                    'grand_total' => (float) ($tx['grand_total'] ?? 0),
                    'payment_method' => $paymentMethod,
                    'cash_amount' => (float) ($tx['cash_amount'] ?? 0),
                    'change_amount' => (float) ($tx['change_amount'] ?? 0),
                    'notes' => trim(($tx['notes'] ?? '') . ' [Sinkronisasi Offline]'),
                    'status' => 'completed',
                    'patient_name' => $tx['patient_name'] ?? null,
                    'patient_doctor_name' => $tx['patient_doctor_name'] ?? null,
                    'patient_phone' => $tx['patient_phone'] ?? null,
                ]);

                // Create Receivable if Tempo
                if ($sale->payment_method === 'tempo' && $customerId) {
                    $remaining = (float) $sale->grand_total - (float) $sale->cash_amount;
                    if ($remaining > 0.01) {
                        $tempoDays = (int) ($tx['tempo_duration'] ?? 30);
                        Receivable::create([
                            'sale_id' => $sale->id,
                            'customer_id' => $customerId,
                            'amount' => $sale->grand_total,
                            'paid_amount' => (float) $sale->cash_amount,
                            'remaining_balance' => $remaining,
                            'due_date' => \Carbon\Carbon::parse($txDate)->addDays($tempoDays),
                            'status' => ((float) $sale->cash_amount > 0) ? 'partial' : 'unpaid',
                            'notes' => 'Tempo Payment (Offline Sync) for ' . $sale->invoice_no,
                        ]);
                    }
                }

                // Process Items & Deduct Stock via FEFO
                $items = $tx['items'] ?? [];
                foreach ($items as $item) {
                    $productId = $item['id'];
                    $factor = (float) ($item['unit_factor'] ?? 1);
                    $qty = (float) ($item['qty'] ?? 1);
                    $qtyNeeded = $qty * $factor;
                    $price = (float) ($item['price'] ?? 0);
                    $discountPercent = (float) ($item['discount_percent'] ?? 0);
                    $itemDiscount = $price * ($discountPercent / 100);
                    $itemNotes = $item['notes'] ?? '';

                    $batches = Batch::where('product_id', $productId)
                        ->valid()
                        ->orderBy('expired_date', 'asc')
                        ->get();

                    $qtyRemaining = $qtyNeeded;

                    foreach ($batches as $batch) {
                        if ($qtyRemaining <= 0) break;
                        $take = min($batch->stock_current, $qtyRemaining);
                        if ($take <= 0) continue;

                        $batch->decrement('stock_current', $take);

                        SaleItem::create([
                            'sale_id' => $sale->id,
                            'product_id' => $productId,
                            'unit_id' => $item['unit_id'] ?? null,
                            'batch_id' => $batch->id,
                            'quantity' => $take / $factor,
                            'sell_price' => $price,
                            'discount_amount' => $itemDiscount,
                            'subtotal' => ($price - $itemDiscount) * ($take / $factor),
                            'notes' => $itemNotes,
                        ]);

                        StockMovement::create([
                            'product_id' => $productId,
                            'batch_id' => $batch->id,
                            'user_id' => $userId,
                            'type' => 'sale',
                            'quantity' => -$take,
                            'doc_ref' => $sale->invoice_no,
                            'description' => 'Penjualan Kasir Offline (' . $qty . ' ' . ($item['unit_name'] ?? 'Pcs') . ')',
                        ]);

                        $qtyRemaining -= $take;
                    }

                    if ($qtyRemaining > 0) {
                        SaleItem::create([
                            'sale_id' => $sale->id,
                            'product_id' => $productId,
                            'unit_id' => $item['unit_id'] ?? null,
                            'batch_id' => null,
                            'quantity' => $qtyRemaining / $factor,
                            'sell_price' => $price,
                            'discount_amount' => $itemDiscount,
                            'subtotal' => ($price - $itemDiscount) * ($qtyRemaining / $factor),
                            'notes' => $itemNotes,
                        ]);

                        StockMovement::create([
                            'product_id' => $productId,
                            'batch_id' => null,
                            'user_id' => $userId,
                            'type' => 'sale',
                            'quantity' => -$qtyRemaining,
                            'doc_ref' => $sale->invoice_no,
                            'description' => 'Penjualan Offline (Stok Minus)',
                        ]);
                    }
                }

                ActivityLog::log([
                    'action' => 'created',
                    'module' => 'sales',
                    'description' => "Transaksi penjualan (Sinkronisasi Offline): {$sale->invoice_no}",
                    'new_values' => $sale->toArray(),
                ]);

                try {
                    $accountingService = new AccountingService();
                    $accountingService->postSaleJournal($sale->id);
                } catch (\Exception $je) {
                    Log::error('Failed to post offline sale journal for ' . $sale->invoice_no . ': ' . $je->getMessage());
                }

                DB::commit();
                $syncedIds[] = $offlineId;
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Offline POS Sync Error ({$invoiceNo}): " . $e->getMessage());
                $errors[] = [
                    'offline_id' => $offlineId,
                    'invoice_no' => $invoiceNo,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return response()->json([
            'success' => count($errors) === 0,
            'synced_ids' => $syncedIds,
            'errors' => $errors,
        ]);
    }
}
