<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalesReturn;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProfitLossService
{
    /**
     * Bulan kalender penuh -> bulan sebelumnya. Selain itu -> rentang dgn panjang sama persis sebelum $start.
     */
    public function getPreviousPeriod($startDate, $endDate)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        $isFullMonth = $start->isSameDay($start->copy()->startOfMonth())
            && $end->isSameDay($end->copy()->endOfMonth())
            && $start->isSameMonth($end);

        if ($isFullMonth) {
            $prevMonth = $start->copy()->subMonth();
            return [
                $prevMonth->copy()->startOfMonth()->format('Y-m-d'),
                $prevMonth->copy()->endOfMonth()->format('Y-m-d'),
            ];
        }

        $days = $start->diffInDays($end) + 1;
        $prevEnd = $start->copy()->subDay();
        $prevStart = $prevEnd->copy()->subDays($days - 1);

        return [$prevStart->format('Y-m-d'), $prevEnd->format('Y-m-d')];
    }

    public function calculateMetrics($startDate, $endDate)
    {
        // 1. Sales Details (Gross, Discount, Tax) — pakai kolom 'date' agar sinkron dengan Laporan Penjualan
        $sales = Sale::whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->with('saleItems')
            ->get();

        $grossRevenue = (float) $sales->sum('dpp');

        $totalTax = (float) $sales->sum('tax');
        $totalDiscount = (float) $sales->sum('discount');
        $grandTotal = (float) $sales->sum('grand_total');

        // 2. Sales Returns (Retur Penjualan) — sinkron dengan Laporan Penjualan
        $salesReturns = SalesReturn::whereBetween('created_at', [
            Carbon::parse($startDate)->startOfDay(),
            Carbon::parse($endDate)->endOfDay()
        ])->get();

        $totalReturns = (float) $salesReturns->sum('total_amount');

        // Revenue = DPP - Retur (Pendapatan Riil, sinkron dengan Laporan Penjualan)
        $revenue = $grossRevenue - $totalReturns;

        // Rincian pendapatan per kategori produk (nilai kotor per-item, sebelum retur/penyesuaian level transaksi)
        $revenueByCategory = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->whereDate('sales.date', '>=', $startDate)
            ->whereDate('sales.date', '<=', $endDate)
            ->selectRaw("COALESCE(categories.name, 'Tanpa Kategori') as category_name, SUM(sale_items.subtotal) as total")
            ->groupBy('category_name')
            ->orderByDesc('total')
            ->get();

        // 3. COGS (HPP) - Detailed records for table — pakai kolom 'date'
        $cogsDetails = SaleItem::select(
                'sale_items.*',
                'products.name as product_name',
                'batches.buy_price as cost_price',
                'sales.date as sale_date'
            )
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('batches', 'sale_items.batch_id', '=', 'batches.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->whereDate('sales.date', '>=', $startDate)
            ->whereDate('sales.date', '<=', $endDate)
            ->orderBy('sales.date', 'desc')
            ->get();

        // Also fetch sales with direct cogs (historical/imported)
        $historicalSales = Sale::whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->whereNotNull('cogs')
            ->where('cogs', '>', 0)
            ->get();

        $historicalCogsItems = $historicalSales->map(function ($sale) {
            return (object) [
                'id' => null,
                'sale_id' => $sale->id,
                'sale_date' => $sale->date,
                'product_name' => 'HPP Omset Historis (' . $sale->invoice_no . ')',
                'quantity' => 1,
                'cost_price' => (float) $sale->cogs,
                'subtotal' => (float) $sale->cogs
            ];
        });

        $mergedCogsDetails = $cogsDetails->concat($historicalCogsItems)->sortByDesc('sale_date');

        $cogs = (float) $mergedCogsDetails->sum(function($item) {
            return (float) $item->quantity * (float) $item->cost_price;
        });

        // 4. Expenses (Beban Operasional, Pajak & Lain-lain)
        $allExpenses = Expense::where('type', 'expense')
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->orderBy('date', 'desc')
            ->get();

        $taxKeywords = ['pajak', 'tax', 'beban pajak', 'income tax', 'pph', 'pajak penghasilan'];
        $otherKeywords = ['bunga', 'lain-lain', 'lain lain'];

        $taxExpenseDetails = $allExpenses->filter(function($expense) use ($taxKeywords) {
            return in_array(strtolower($expense->category), $taxKeywords) ||
                   Str::contains(strtolower($expense->description), 'pajak penghasilan');
        });

        $otherExpenseDetails = $allExpenses->diff($taxExpenseDetails)->filter(function($expense) use ($otherKeywords) {
            $category = strtolower($expense->category ?? '');
            foreach ($otherKeywords as $keyword) {
                if (Str::contains($category, $keyword)) {
                    return true;
                }
            }
            return false;
        });

        $operatingExpenseDetails = $allExpenses->diff($taxExpenseDetails)->diff($otherExpenseDetails);

        $operatingExpenses = (float) $operatingExpenseDetails->sum('amount');
        $otherExpenseAmount = (float) $otherExpenseDetails->sum('amount');

        // Pendapatan lain-lain (mis. bunga bank) — dicatat sbg Expense bertipe 'income'
        $otherIncomeDetails = Expense::where('type', 'income')
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->orderBy('date', 'desc')
            ->get();

        $otherIncomeAmount = (float) $otherIncomeDetails->sum('amount');
        $otherIncomeExpenseNet = $otherIncomeAmount - $otherExpenseAmount;

        // Tax: skema manual (dari input Beban Pajak) atau PPh Final UMKM (% dari omzet)
        $taxScheme = \App\Models\Setting::get('tax_scheme', 'manual');
        $taxRateUmkm = (float) \App\Models\Setting::get('tax_rate_umkm', 0.5);

        if ($taxScheme === 'umkm_final') {
            $taxExpenses = round($revenue * $taxRateUmkm / 100);
        } else {
            $taxExpenses = (float) $taxExpenseDetails->sum('amount');
        }

        // 5. Final Calculations
        $grossProfit = $revenue - $cogs;
        $operatingProfit = $grossProfit - $operatingExpenses; // LABA USAHA (EBIT)
        $netProfitBeforeTax = $operatingProfit + $otherIncomeExpenseNet;
        $netProfit = $netProfitBeforeTax - $taxExpenses;

        return [
            'grossRevenue' => $grossRevenue, // DPP sebelum retur
            'totalReturns' => $totalReturns, // Total retur penjualan
            'revenue' => $revenue, // DPP - Retur = Pendapatan Riil
            'revenueByCategory' => $revenueByCategory,
            'totalTax' => $totalTax,
            'totalDiscount' => $totalDiscount,
            'grandTotal' => $grandTotal,
            'cogs' => $cogs,
            'operatingExpenses' => $operatingExpenses,
            'expenses' => $operatingExpenses,
            'operatingProfit' => $operatingProfit,
            'otherIncomeDetails' => $otherIncomeDetails,
            'otherExpenseDetails' => $otherExpenseDetails,
            'otherIncomeAmount' => $otherIncomeAmount,
            'otherExpenseAmount' => $otherExpenseAmount,
            'otherIncomeExpenseNet' => $otherIncomeExpenseNet,
            'taxScheme' => $taxScheme,
            'taxRateUmkm' => $taxRateUmkm,
            'taxExpenses' => $taxExpenses,
            'grossProfit' => $grossProfit,
            'netProfitBeforeTax' => $netProfitBeforeTax,
            'netProfit' => $netProfit,
            'salesDetails' => $sales,
            'salesReturns' => $salesReturns,
            'cogsDetails' => $mergedCogsDetails,
            'expenseDetails' => $operatingExpenseDetails,
            'taxDetails' => $taxExpenseDetails,
        ];
    }
}
