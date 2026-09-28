<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Supplier;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Receivable;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ProcurementFinancialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first() ?? User::factory()->create();
        $suppliers = Supplier::all();
        $products = Product::all();
        $customers = Customer::all();

        if ($suppliers->isEmpty() || $products->isEmpty() || $customers->isEmpty()) {
            $this->command->warn('Pastikan Supplier, Product, dan Customer sudah di-seed terlebih dahulu.');
            return;
        }

        // 1. Seed Purchase Orders and Goods Receipts (Payables / Hutang Usaha)
        $poReceiptData = [
            [
                'supplier_index' => 0, // PT Anugrah Argon Medica
                'delivery_note' => 'SJ-AAM-2026-0891',
                'days_ago' => 25,
                'due_days' => -2, // Overdue by 2 days
                'status' => 'pending',
                'paid_fraction' => 0,
            ],
            [
                'supplier_index' => 1, // PT Kimia Farma
                'delivery_note' => 'SJ-KF-2026-1142',
                'days_ago' => 10,
                'due_days' => 4, // Due in 4 days
                'status' => 'pending',
                'paid_fraction' => 0,
            ],
            [
                'supplier_index' => 2, // PT Enseval Putera Megatrading
                'delivery_note' => 'SJ-ENS-2026-3390',
                'days_ago' => 18,
                'due_days' => 2, // Due in 2 days
                'status' => 'partial',
                'paid_fraction' => 0.45,
            ],
            [
                'supplier_index' => 3, // PT Parit Padang Global
                'delivery_note' => 'SJ-PPG-2026-0455',
                'days_ago' => 8,
                'due_days' => 12, // Due in 12 days
                'status' => 'partial',
                'paid_fraction' => 0.35,
            ],
            [
                'supplier_index' => 4, // PT Bina San Prima
                'delivery_note' => 'SJ-BSP-2026-5521',
                'days_ago' => 5,
                'due_days' => 16, // Due in 16 days
                'status' => 'pending',
                'paid_fraction' => 0,
            ],
            [
                'supplier_index' => 5, // PT United Dico Citas
                'delivery_note' => 'SJ-UDC-2026-7812',
                'days_ago' => 32,
                'due_days' => -10,
                'status' => 'paid',
                'paid_fraction' => 1.0,
            ],
        ];

        foreach ($poReceiptData as $idx => $data) {
            $supplier = $suppliers->get($data['supplier_index'] % $suppliers->count());
            $orderDate = Carbon::now()->subDays($data['days_ago'] + 3);
            $receivedDate = Carbon::now()->subDays($data['days_ago']);
            $dueDate = Carbon::now()->addDays($data['due_days']);

            $po = PurchaseOrder::create([
                'po_number' => 'PO-' . $orderDate->format('Ymd') . '-' . str_pad((string)($idx + 101), 4, '0', STR_PAD_LEFT),
                'supplier_id' => $supplier->id,
                'user_id' => $user->id,
                'date' => $orderDate,
                'status' => 'received',
                'notes' => 'Pemesanan stok rutin dari distributor ' . $supplier->name,
                'total_amount' => 0,
            ]);

            // Add 3-5 items to PO
            $poTotal = 0;
            $sampleProducts = $products->random(min(4, $products->count()));

            $gr = GoodsReceipt::create([
                'purchase_order_id' => $po->id,
                'delivery_note_number' => $data['delivery_note'],
                'received_date' => $receivedDate,
                'user_id' => $user->id,
                'notes' => 'Penerimaan barang kondisi baik dan lengkap.',
                'payment_method' => 'due_date',
                'due_date_weeks' => 4,
                'total_amount' => 0,
                'paid_amount' => 0,
                'payment_status' => $data['status'],
                'due_date' => $dueDate,
            ]);

            foreach ($sampleProducts as $p) {
                $qty = rand(20, 50);
                $buyPrice = $p->purchase_price ?? ($p->sell_price * 0.75);
                $subtotal = $qty * $buyPrice;
                $poTotal += $subtotal;

                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'product_id' => $p->id,
                    'qty_ordered' => $qty,
                    'unit_price' => $buyPrice,
                    'subtotal' => $subtotal,
                ]);

                GoodsReceiptItem::create([
                    'goods_receipt_id' => $gr->id,
                    'product_id' => $p->id,
                    'batch_no' => 'GR-' . strtoupper(Str::random(6)),
                    'expired_date' => Carbon::now()->addMonths(rand(12, 24)),
                    'qty_received' => $qty,
                    'buy_price' => $buyPrice,
                ]);
            }

            $po->update(['total_amount' => $poTotal]);

            $paidAmount = round($poTotal * $data['paid_fraction'], -2);
            $gr->update([
                'total_amount' => $poTotal,
                'paid_amount' => $paidAmount,
            ]);
        }

        // 2. Seed Credit Sales & Receivables (Piutang Usaha)
        $receivableData = [
            [
                'customer_index' => 2, // dr. Bambang Sudiro, Sp.A
                'days_ago' => 12,
                'due_days' => 3, // Due in 3 days
                'status' => 'unpaid',
                'paid_fraction' => 0,
                'notes' => 'Pesanan obat klinik anak',
            ],
            [
                'customer_index' => 5, // dr. Rini Kusuma, Sp.PD
                'days_ago' => 20,
                'due_days' => -3, // Overdue by 3 days
                'status' => 'unpaid',
                'paid_fraction' => 0,
                'notes' => 'Resep reguler tempo klinik penyakit dalam',
            ],
            [
                'customer_index' => 22, // Klinik Medika Pratama
                'days_ago' => 15,
                'due_days' => 6, // Due in 6 days
                'status' => 'partial',
                'paid_fraction' => 0.4,
                'notes' => 'Restock obat klinik pratama',
            ],
            [
                'customer_index' => 24, // Apotek Rekanan Sehat
                'days_ago' => 10,
                'due_days' => 14, // Due in 14 days
                'status' => 'partial',
                'paid_fraction' => 0.5,
                'notes' => 'Kerjasama pasokan antar apotek',
            ],
            [
                'customer_index' => 0, // Pak Hendro Santoso
                'days_ago' => 7,
                'due_days' => 7, // Due in 7 days
                'status' => 'unpaid',
                'paid_fraction' => 0,
                'notes' => 'Obat rutin bulanan hipertensi & gula',
            ],
        ];

        foreach ($receivableData as $idx => $rData) {
            $customer = $customers->get($rData['customer_index'] % $customers->count());
            $saleDate = Carbon::now()->subDays($rData['days_ago']);
            $dueDate = Carbon::now()->addDays($rData['due_days']);

            $saleProducts = $products->random(min(3, $products->count()));
            $subtotal = 0;

            $sale = Sale::create([
                'user_id' => $user->id,
                'customer_id' => $customer->id,
                'invoice_no' => 'INV-CR/' . $saleDate->format('Ymd') . '/' . str_pad((string)($idx + 201), 4, '0', STR_PAD_LEFT),
                'date' => $saleDate,
                'total_amount' => 0,
                'tax' => 0,
                'discount' => 0,
                'grand_total' => 0,
                'payment_method' => 'transfer',
                'cash_amount' => 0,
                'change_amount' => 0,
                'order_mode' => 'In',
                'status' => 'completed',
                'notes' => $rData['notes'],
                'created_at' => $saleDate,
                'updated_at' => $saleDate,
            ]);

            foreach ($saleProducts as $p) {
                $qty = rand(2, 5);
                $itemSubtotal = $p->sell_price * $qty;
                $subtotal += $itemSubtotal;

                $batch = $p->batches->first();

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $p->id,
                    'unit_id' => $p->unit_id,
                    'batch_id' => $batch ? $batch->id : 1,
                    'quantity' => $qty,
                    'sell_price' => $p->sell_price,
                    'discount_amount' => 0,
                    'subtotal' => $itemSubtotal,
                    'created_at' => $saleDate,
                    'updated_at' => $saleDate,
                ]);
            }

            $sale->update([
                'total_amount' => $subtotal,
                'grand_total' => $subtotal,
            ]);

            $paidAmount = round($subtotal * $rData['paid_fraction'], -2);
            $remainingBalance = $subtotal - $paidAmount;

            Receivable::create([
                'sale_id' => $sale->id,
                'customer_id' => $customer->id,
                'amount' => $subtotal,
                'paid_amount' => $paidAmount,
                'remaining_balance' => $remainingBalance,
                'status' => $rData['status'],
                'due_date' => $dueDate,
                'notes' => $rData['notes'],
                'created_at' => $saleDate,
                'updated_at' => $saleDate,
            ]);
        }
    }
}
