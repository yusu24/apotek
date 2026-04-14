<?php

namespace App\Http\Controllers;

use App\Models\GoodsReceipt;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PdfController extends Controller
{
    /**
     * Export Goods Receipt as PDF
     */
    public function exportGoodsReceipt($id)
    {
        // Load goods receipt with relations
        $receipt = GoodsReceipt::with([
            'items.product',
            'items.unit',
            'purchaseOrder.supplier',
            'user'
        ])->findOrFail($id);

        $apotekName = \App\Models\Setting::get('store_name');
        if (!$apotekName || $apotekName === 'Laravel') {
            $apotekName = config('app.name') === 'Laravel' ? 'APOTEK' : config('app.name');
        }

        // Prepare data for PDF
        $data = [
            'receipt' => $receipt,
            'printedBy' => auth()->user()->name ?? 'System',
            'printedAt' => Carbon::now()->format('d/m/Y H:i'),
            'apotekName' => $apotekName,
        ];

        // Generate PDF
        $pdf = Pdf::loadView('pdf.goods-receipt-pdf', $data);
        $pdf->setPaper('a4', 'portrait');

        // Stream PDF to browser for printing
        $filename = 'Penerimaan-Barang-' . $receipt->delivery_note_number . '.pdf';
        return $pdf->stream($filename);
    }

    /**
     * Export Stock History as PDF
     */
    public function exportStockHistory(Request $request, $productId)
    {
        // Load product with relations
        $product = Product::with(['category', 'unit'])->findOrFail($productId);

        // Get filters from request
        $filterType = $request->get('type', 'all');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Build query for stock movements
        $query = StockMovement::where('product_id', $productId)
            ->with(['batch', 'user']);

        // Apply filters
        if ($filterType !== 'all') {
            $query->where('type', $filterType);
        }

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        // Get movements ordered chronologically
        $movements = $query->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
            ->limit(500) // Limit to prevent large PDFs
            ->get();

        // Calculate running balance
        $runningBalance = 0;
        $movements->transform(function($movement) use (&$runningBalance) {
            $runningBalance += $movement->quantity;
            $movement->running_balance = $runningBalance;
            return $movement;
        });

        // Calculate summary
        $totalIn = $movements->where('quantity', '>', 0)->sum('quantity');
        $totalOut = abs($movements->where('quantity', '<', 0)->sum('quantity'));
        $netChange = $totalIn - $totalOut;

        // Get active batches
        $activeBatches = $product->batches()
            ->where('stock_current', '>', 0)
            ->orderBy('expired_date')
            ->get();

        // Format period for header
        $period = $this->formatPeriod($startDate, $endDate);

        $apotekName = \App\Models\Setting::get('store_name');
        if (!$apotekName || $apotekName === 'Laravel') {
            $apotekName = config('app.name') === 'Laravel' ? 'APOTEK' : config('app.name');
        }

        // Prepare data for PDF
        $data = [
            'product' => $product,
            'movements' => $movements,
            'activeBatches' => $activeBatches,
            'totalIn' => $totalIn,
            'totalOut' => $totalOut,
            'netChange' => $netChange,
            'period' => $period,
            'filterType' => $filterType,
            'printedBy' => auth()->user()->name ?? 'System',
            'printedAt' => Carbon::now()->format('d/m/Y H:i'),
            'apotekName' => $apotekName,
        ];

        // Generate PDF
        $pdf = Pdf::loadView('pdf.stock-history-pdf', $data);
        $pdf->setPaper('a4', 'landscape'); // Landscape for better table view

        // Download PDF
        $filename = 'Kartu-Stok-' . str_replace(' ', '-', $product->name) . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Format period for display
     */
    private function formatPeriod($startDate, $endDate)
    {
        if ($startDate && $endDate) {
            return Carbon::parse($startDate)->format('d/m/Y') . ' - ' . Carbon::parse($endDate)->format('d/m/Y');
        } elseif ($startDate) {
            return 'Sejak ' . Carbon::parse($startDate)->format('d/m/Y');
        } elseif ($endDate) {
            return 'Sampai ' . Carbon::parse($endDate)->format('d/m/Y');
        }
        return 'Semua Periode';
    }

    /**
     * Export PPN Report to PDF
     */
    public function exportPpnReport(Request $request)
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);
        
        $taxService = new \App\Services\TaxService();
        $data = $taxService->getMonthlySummary($year, $month);
        
        $monthName = Carbon::create($year, $month)->format('F Y');
        
        $storeName = \App\Models\Setting::get('store_name');
        if (!$storeName || $storeName === 'Laravel') {
            $storeName = config('app.name') === 'Laravel' ? 'APOTEK' : config('app.name');
        }

        $store = [
            'name' => $storeName,
            'address' => \App\Models\Setting::get('store_address'),
            'phone' => \App\Models\Setting::get('store_phone'),
        ];

        $pdf = PDF::loadView('pdf.ppn-report', [
            'data' => $data,
            'year' => $year,
            'month' => $month,
            'monthName' => $monthName,
            'store' => $store,
            'printedBy' => auth()->user()->name ?? 'System',
            'printedAt' => Carbon::now()->format('d/m/Y H:i'),
        ]);
        
        $filename = 'Laporan-PPN-' . $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '.pdf';
        
        return $pdf->setPaper('a4', 'portrait')->stream($filename);
    }

    /**
     * Export Aging Report (AR/AP) to PDF
     */
    public function exportAgingReport(Request $request)
    {
        $type = $request->get('type', 'ap');
        $includePaid = $request->get('showPaid', false);

        $accountingService = new \App\Services\AccountingService();
        $reportData = $accountingService->getGroupedAgingReport($type, $includePaid);
        
        $storeName = \App\Models\Setting::get('store_name');
        if (!$storeName || $storeName === 'Laravel') {
            $storeName = config('app.name') === 'Laravel' ? 'APOTEK' : config('app.name');
        }

        $store = [
            'name' => $storeName,
            'address' => \App\Models\Setting::get('store_address'),
            'phone' => \App\Models\Setting::get('store_phone'),
        ];

        $pdf = Pdf::loadView('pdf.aging-report', [
            'reportData' => $reportData,
            'store' => $store,
            'type' => $type,
            'printedBy' => auth()->user()->name ?? 'System',
            'printedAt' => Carbon::now()->format('d/m/Y H:i'),
        ]);
        
        $title = $type === 'ar' ? 'Piutang' : 'Hutang';
        $filename = 'Laporan-Aging-' . $title . '-' . Carbon::now()->format('Ymd') . '.pdf';
        
        return $pdf->setPaper('a4', 'landscape')->stream($filename);
    }

    /**
     * Export User Manual to PDF
     */
    public function exportUserManual()
    {
        // Load markdown content
        $markdownPath = storage_path('app/user-manual.md');
        
        // If file doesn't exist, create it from the tutorial
        if (!file_exists($markdownPath)) {
            $tutorialContent = $this->getUserManualContent();
            Storage::put('user-manual.md', $tutorialContent);
        }
        
        $markdown = Storage::get('user-manual.md');
        
        // Convert markdown to HTML (basic conversion)
        $html = $this->convertMarkdownToHtml($markdown);
        
        $apotekName = \App\Models\Setting::get('store_name');
        if (!$apotekName || $apotekName === 'Laravel') {
            $apotekName = config('app.name') === 'Laravel' ? 'APOTEK' : config('app.name');
        }

        // Prepare data for PDF
        $data = [
            'content' => $html,
            'printedBy' => auth()->user()->name ?? 'System',
            'printedAt' => Carbon::now()->format('d/m/Y H:i'),
            'apotekName' => $apotekName,
        ];
        
        // Generate PDF
        $pdf = PDF::loadView('pdf.user-manual', $data);
        $pdf->setPaper('a4', 'portrait');
        
        // Download PDF
        $filename = 'Buku-Panduan-Apotek-' . Carbon::now()->format('Y-m-d') . '.pdf';
        return $pdf->download($filename);
    }
    
    /**
     * Get user manual content
     */
    private function getUserManualContent()
    {
        return file_get_contents(base_path('.gemini/antigravity/brain/110ac294-5a7c-4419-9cd3-8f873ee68cf2/TUTORIAL_PENGGUNAAN_APOTEK.md'));
    }
    
    /**
     * Basic markdown to HTML conversion
     */
    private function convertMarkdownToHtml($markdown)
    {
        // Use parsedown or similar library if available, otherwise basic conversion
        $parsedownClass = '\Parsedown';
        if (class_exists($parsedownClass)) {
            $parsedown = new $parsedownClass();
            return $parsedown->text($markdown);
        }
        
        // Basic conversion
        $html = $markdown;
        $html = preg_replace('/^# (.+)$/m', '<h1>$1</h1>', $html);
        $html = preg_replace('/^## (.+)$/m', '<h2>$1</h2>', $html);
        $html = preg_replace('/^### (.+)$/m', '<h3>$1</h3>', $html);
        $html = preg_replace('/\*\*(.+?)\*\*/','<strong>$1</strong>', $html);
        $html = preg_replace('/\*(.+?)\*/','<em>$1</em>', $html);
        $html = preg_replace('/`(.+?)`/','<code>$1</code>', $html);
        $html = nl2br($html);
        
        return $html;
    }

    /**
     * Export Cash Flow Report to PDF
     */
    public function exportCashFlow(Request $request)
    {
        $startDate = $request->get('startDate', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('endDate', now()->endOfMonth()->format('Y-m-d'));

        $accountingService = new \App\Services\AccountingService();
        $data = $accountingService->getCashFlowStatement($startDate, $endDate);
        
        $storeName = \App\Models\Setting::get('store_name');
        if (!$storeName || $storeName === 'Laravel') {
            $storeName = config('app.name') === 'Laravel' ? 'APOTEK' : config('app.name');
        }

        $store = [
            'name' => $storeName,
            'address' => \App\Models\Setting::get('store_address'),
            'phone' => \App\Models\Setting::get('store_phone'),
        ];

        $pdf = Pdf::loadView('pdf.cash-flow', [
            'data' => $data,
            'store' => $store,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'printedBy' => auth()->user()->name ?? 'System',
            'printedAt' => Carbon::now()->format('d/m/Y H:i'),
        ]);
        
        $filename = 'Laporan-Arus-Kas-' . Carbon::parse($startDate)->format('Ymd') . '-' . Carbon::parse($endDate)->format('Ymd') . '.pdf';
        
        return $pdf->setPaper('a4', 'portrait')->stream($filename);
    }

    /**
     * Export Income Statement (Profit & Loss) to PDF
     */
    public function exportIncomeStatement(Request $request)
    {
        $startDate = $request->get('startDate', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('endDate', now()->endOfMonth()->format('Y-m-d'));

        $accountingService = new \App\Services\AccountingService();
        $reportData = $accountingService->getIncomeStatement($startDate, $endDate);
        
        $storeName = \App\Models\Setting::get('store_name');
        if (!$storeName || $storeName === 'Laravel') {
            $storeName = config('app.name') === 'Laravel' ? 'APOTEK' : config('app.name');
        }

        $store = [
            'name' => $storeName,
            'address' => \App\Models\Setting::get('store_address'),
            'phone' => \App\Models\Setting::get('store_phone'),
        ];

        $pdf = Pdf::loadView('pdf.income-statement', [
            'reportData' => $reportData,
            'store' => $store,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'printedBy' => auth()->user()->name ?? 'System',
            'printedAt' => Carbon::now()->format('d/m/Y H:i'),
        ]);
        
        $filename = 'Laporan-Laba-Rugi-' . Carbon::parse($startDate)->format('Ymd') . '-' . Carbon::parse($endDate)->format('Ymd') . '.pdf';
        
        return $pdf->setPaper('a4', 'portrait')->stream($filename);
    }

    /**
     * Export Balance Sheet to PDF
     */
    public function exportBalanceSheet(Request $request)
    {
        $asOfDate = $request->get('asOfDate', now()->endOfMonth()->format('Y-m-d'));

        $accountingService = new \App\Services\AccountingService();
        $reportData = $accountingService->getBalanceSheet(null, $asOfDate);
        
        $storeName = \App\Models\Setting::get('store_name');
        if (!$storeName || $storeName === 'Laravel') {
            $storeName = config('app.name') === 'Laravel' ? 'APOTEK' : config('app.name');
        }

        $store = [
            'name'    => $storeName,
            'address' => \App\Models\Setting::get('store_address'),
            'phone'   => \App\Models\Setting::get('store_phone'),
        ];

        $pdf = Pdf::loadView('pdf.balance-sheet', [
            'reportData' => $reportData,
            'store'      => $store,
            'asOfDate'   => $asOfDate,
            'printedBy'  => auth()->user()->name ?? 'System',
            'printedAt'  => Carbon::now()->format('d/m/Y H:i'),
        ]);
        
        $filename = 'Neraca-Per-' . Carbon::parse($asOfDate)->format('Ymd') . '.pdf';
        
        return $pdf->setPaper('a4', 'portrait')->stream($filename);
    }
    /**
     * Export General Ledger to PDF
     */
    public function exportLedger(Request $request)
    {
        $startDate = $request->get('startDate', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('endDate', now()->endOfMonth()->format('Y-m-d'));
        $accountId = $request->get('accountId');
        $search = $request->get('search');

        $accountingService = new \App\Services\AccountingService();
        $reportData = $accountingService->getLedgerReport($startDate, $endDate, $accountId, $search);
        
        $storeName = \App\Models\Setting::get('store_name');
        if (!$storeName || $storeName === 'Laravel') {
            $storeName = config('app.name') === 'Laravel' ? 'APOTEK' : config('app.name');
        }

        $store = [
            'name' => $storeName,
            'address' => \App\Models\Setting::get('store_address'),
            'phone' => \App\Models\Setting::get('store_phone'),
        ];

        $pdf = Pdf::loadView('pdf.ledger', [
            'reportData' => $reportData,
            'store' => $store,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'accountId' => $accountId,
            'search' => $search,
            'printedBy' => auth()->user()->name ?? 'System',
            'printedAt' => Carbon::now()->format('d/m/Y H:i'),
        ]);
        
        $filename = 'Buku-Besar-' . Carbon::parse($startDate)->format('Ymd') . '-' . Carbon::parse($endDate)->format('Ymd') . '.pdf';
        
        return $pdf->setPaper('a4', 'landscape')->stream($filename);
    }
    /**
     * Export Stock Report Summary to PDF (Laporan Barang)
     */
    public function exportStockReport(Request $request)
    {
        $search = $request->get('search');
        $categoryFilter = $request->get('category');
        $stockStatus = $request->get('stockStatus');

        // Fetch products with active batches
        $query = Product::with(['unit', 'batches' => function($query) {
            $query->where('stock_current', '>', 0);
        }]);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('barcode', 'like', '%' . $search . '%');
            });
        }

        if ($categoryFilter) {
            $query->where('category_id', $categoryFilter);
        }

        $products = $query->get();

        // Calculate totals for each product
        $products->transform(function($product) {
            $totalStock = $product->batches->sum('stock_current');
            $totalValue = $product->batches->sum(function($batch) {
                return $batch->stock_current * $batch->buy_price;
            });
            
            $product->total_stock = $totalStock;
            $product->total_value = $totalValue;
            $product->avg_buy_price = $totalStock > 0 ? $totalValue / $totalStock : 0;
            
            return $product;
        });

        // Filter by stock status if needed
        if ($stockStatus === 'low') {
            $products = $products->filter(function($product) {
                return $product->total_stock <= $product->min_stock;
            });
        }

        // Filter out products with 0 stock to match screen view
        $products = $products->filter(function($product) {
            return $product->total_stock > 0;
        });

        $storeName = \App\Models\Setting::get('store_name');
        if (!$storeName || $storeName === 'Laravel') {
            $storeName = config('app.name') === 'Laravel' ? 'APOTEK' : config('app.name');
        }

        $store = [
            'name' => $storeName,
            'address' => \App\Models\Setting::get('store_address'),
            'phone' => \App\Models\Setting::get('store_phone'),
        ];

        $pdf = Pdf::loadView('pdf.stock-report', [
            'products' => $products,
            'store' => $store,
            'printedBy' => auth()->user()->name ?? 'System',
            'printedAt' => Carbon::now()->format('d/m/Y H:i'),
        ]);

        $filename = 'Laporan-Barang-' . Carbon::now()->format('Ymd') . '.pdf';

        return $pdf->setPaper('a4', 'portrait')->stream($filename);
    }
    /**
     * Export Sales Report to PDF
     */
    public function exportSalesReport(Request $request)
    {
        $startDate = $request->get('startDate', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('endDate', now()->endOfMonth()->format('Y-m-d'));
        $paymentMethod = $request->get('paymentMethod', 'all');
        $search = $request->get('search');

        $salesQuery = Sale::query()
            ->with(['user'])
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate);

        if ($paymentMethod !== 'all') {
            $salesQuery->where('payment_method', $paymentMethod);
        }

        if ($search) {
            $salesQuery->where(function($q) use ($search) {
                $q->where('invoice_no', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        $sales = $salesQuery->latest('date')->limit(1000)->get();

        $totalReturns = \App\Models\SalesReturn::whereBetween('created_at', [
            Carbon::parse($startDate)->startOfDay(),
            Carbon::parse($endDate)->endOfDay()
        ])->sum('total_amount');

        $stats = [
            'total_sales' => $sales->sum('grand_total'),
            'total_returns' => $totalReturns,
            'net_sales' => $sales->sum('dpp') - $totalReturns,
            'total_tax' => $sales->sum('tax'),
            'total_rounding' => $sales->sum('rounding'),
            'total_dpp' => $sales->sum('dpp'),
            'transaction_count' => $sales->count(),
        ];

        $storeName = \App\Models\Setting::get('store_name');
        if (!$storeName || $storeName === 'Laravel') {
            $storeName = config('app.name') === 'Laravel' ? 'APOTEK' : config('app.name');
        }

        $store = [
            'name' => $storeName,
            'address' => \App\Models\Setting::get('store_address'),
            'phone' => \App\Models\Setting::get('store_phone'),
        ];

        $pdf = Pdf::loadView('pdf.sales-report', [
            'sales' => $sales,
            'stats' => $stats,
            'store' => $store,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'paymentMethod' => $paymentMethod,
            'printedBy' => auth()->user()->name ?? 'System',
            'printedAt' => Carbon::now()->format('d/m/Y H:i'),
        ]);
        
        $filename = 'Laporan-Penjualan-' . Carbon::parse($startDate)->format('Ymd') . '-' . Carbon::parse($endDate)->format('Ymd') . '.pdf';
        
        return $pdf->setPaper('a4', 'portrait')->stream($filename);
    }

    /**
     * Export Product Margin Report to PDF
     */
    public function exportProductMarginReport(Request $request)
    {
        $search = $request->get('search');
        $categoryFilter = $request->get('categoryFilter');
        $marginFilter = $request->get('marginFilter', 'all');
        $reportMode = $request->get('reportMode', 'potential');
        $startDate = $request->get('startDate', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('endDate', now()->format('Y-m-d'));

        if ($reportMode === 'realized') {
            $query = Product::with(['category', 'unit'])
                ->join('sale_items', 'products.id', '=', 'sale_items.product_id')
                ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                ->join('batches', 'sale_items.batch_id', '=', 'batches.id')
                ->whereBetween('sales.date', [
                    \Carbon\Carbon::parse($startDate)->startOfDay(),
                    \Carbon\Carbon::parse($endDate)->endOfDay()
                ])
                ->select([
                    'products.id',
                    'products.name',
                    'products.barcode',
                    'products.category_id',
                    'products.unit_id',
                ])
                ->selectRaw('SUM(sale_items.quantity) as total_sold')
                ->selectRaw('AVG(sale_items.sell_price) as avg_sell_price')
                ->selectRaw('AVG(batches.buy_price) as avg_buy_price')
                ->selectRaw('SUM((sale_items.sell_price - batches.buy_price) * sale_items.quantity) as margin_amount')
                ->selectRaw('
                    CASE 
                    WHEN SUM(batches.buy_price * sale_items.quantity) > 0 
                    THEN (SUM((sale_items.sell_price - batches.buy_price) * sale_items.quantity) / SUM(batches.buy_price * sale_items.quantity) * 100) 
                    ELSE 0 
                    END as margin_percentage
                ')
                ->groupBy('products.id', 'products.name', 'products.barcode', 'products.category_id', 'products.unit_id');
        } else {
            $query = Product::with(['category', 'unit'])
                ->select([
                    'products.id',
                    'products.name',
                    'products.barcode',
                    'products.sell_price',
                    'products.category_id',
                    'products.unit_id',
                ])
                ->selectRaw('
                    COALESCE(
                        (SELECT AVG(buy_price) 
                         FROM batches 
                         WHERE batches.product_id = products.id 
                         AND batches.stock_current > 0
                        ), 
                        (SELECT buy_price 
                         FROM batches 
                         WHERE batches.product_id = products.id 
                         ORDER BY created_at DESC 
                         LIMIT 1
                        ),
                        0
                    ) as last_buy_price
                ')
                ->selectRaw('
                    (products.sell_price - COALESCE(
                        (SELECT AVG(buy_price) 
                         FROM batches 
                         WHERE batches.product_id = products.id 
                         AND batches.stock_current > 0
                        ), 
                        (SELECT buy_price 
                         FROM batches 
                         WHERE batches.product_id = products.id 
                         ORDER BY created_at DESC 
                         LIMIT 1
                        ),
                        0
                    )) as margin_amount
                ')
                ->selectRaw('
                    CASE 
                    WHEN COALESCE(
                        (SELECT AVG(buy_price) 
                         FROM batches 
                         WHERE batches.product_id = products.id 
                         AND batches.stock_current > 0
                        ), 
                        (SELECT buy_price 
                         FROM batches 
                         WHERE batches.product_id = products.id 
                         ORDER BY created_at DESC 
                         LIMIT 1
                        ),
                        0
                    ) > 0 
                    THEN ((products.sell_price - COALESCE(
                        (SELECT AVG(buy_price) 
                         FROM batches 
                         WHERE batches.product_id = products.id 
                         AND batches.stock_current > 0
                        ), 
                        (SELECT buy_price 
                         FROM batches 
                         WHERE batches.product_id = products.id 
                         ORDER BY created_at DESC 
                         LIMIT 1
                        ),
                        0
                    )) / COALESCE(
                        (SELECT AVG(buy_price) 
                         FROM batches 
                         WHERE batches.product_id = products.id 
                         AND batches.stock_current > 0
                        ), 
                        (SELECT buy_price 
                         FROM batches 
                         WHERE batches.product_id = products.id 
                         ORDER BY created_at DESC 
                         LIMIT 1
                        ),
                        0
                    ) * 100) 
                    ELSE 0 
                    END as margin_percentage
                ');
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('products.name', 'like', '%' . $search . '%')
                  ->orWhere('products.barcode', 'like', '%' . $search . '%');
            });
        }

        if ($categoryFilter) {
            $query->where('products.category_id', $categoryFilter);
        }

        if ($marginFilter === 'positive') {
            $query->havingRaw('margin_amount > 0');
        } elseif ($marginFilter === 'negative') {
            $query->havingRaw('margin_amount < 0');
        } elseif ($marginFilter === 'high') {
            $query->havingRaw('margin_percentage > 30');
        } elseif ($marginFilter === 'low') {
            $query->havingRaw('margin_percentage < 10 AND margin_percentage >= 0');
        }

        $products = $query->orderBy('products.name')->get();

        $stats = [
            'total_products' => $products->count(),
            'total_margin_value' => $products->sum('margin_amount'),
            'average_margin_percentage' => $products->count() > 0 ? $products->avg('margin_percentage') : 0,
        ];

        $storeName = \App\Models\Setting::get('store_name') ?? 'APOTEK';
        
        $pdf = Pdf::loadView('pdf.product-margin-report', [
            'products' => $products,
            'stats' => $stats,
            'reportMode' => $reportMode,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'storeName' => $storeName,
            'printedAt' => now()->format('d/m/Y H:i'),
        ]);
        
        $filename = 'Laporan-Margin-Produk-' . now()->format('Ymd') . '.pdf';
        
        return $pdf->setPaper('a4', 'portrait')->stream($filename);
    }

    /**
     * Export Transaction History to PDF
     */
    public function exportTransactionHistory(Request $request)
    {
        $startDate = $request->get('startDate', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('endDate', now()->format('Y-m-d'));
        $type = $request->get('type', 'all');
        $search = $request->get('search');

        $query = StockMovement::with(['product'])
            ->orderBy('created_at', 'desc'); // Order by latest

        // Apply same filters
        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }
        if ($type !== 'all') {
            if ($type === 'sale') {
                $query->where('type', 'sale');
            } elseif ($type === 'purchase') {
                $query->whereIn('type', ['in']);
            } elseif ($type === 'return') {
                $query->whereIn('type', ['return', 'return-supplier']);
            } else {
                $query->where('type', $type);
            }
        }
        if ($search) {
            $query->whereHas('product', function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('barcode', 'like', '%' . $search . '%');
            });
        }

        $transactions = $query->limit(500)->get();

        $storeName = \App\Models\Setting::get('store_name');
        if (!$storeName || $storeName === 'Laravel') {
            $storeName = config('app.name') === 'Laravel' ? 'APOTEK' : config('app.name');
        }

        $store = [
            'name' => $storeName,
            'address' => \App\Models\Setting::get('store_address'),
            'phone' => \App\Models\Setting::get('store_phone'),
        ];

        $pdf = Pdf::loadView('pdf.transaction-history', [
            'transactions' => $transactions,
            'store' => $store,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'type' => $type,
            'printedBy' => auth()->user()->name ?? 'System',
            'printedAt' => Carbon::now()->format('d/m/Y H:i'),
        ]);
        
        $filename = 'Riwayat-Transaksi-' . Carbon::now()->format('Ymd_His') . '.pdf';
        
        return $pdf->setPaper('a4', 'portrait')->stream($filename);
    }

    /**
     * Export Trial Balance to PDF
     */
    public function exportTrialBalance(Request $request)
    {
        $startDate = $request->get('startDate', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('endDate', now()->endOfMonth()->format('Y-m-d'));

        $accountingService = new \App\Services\AccountingService();
        $reportData = $accountingService->getTrialBalance($startDate, $endDate);
        
        $storeName = \App\Models\Setting::get('store_name');
        if (!$storeName || $storeName === 'Laravel') {
            $storeName = config('app.name') === 'Laravel' ? 'APOTEK' : config('app.name');
        }

        $store = [
            'name' => $storeName,
            'address' => \App\Models\Setting::get('store_address'),
            'phone' => \App\Models\Setting::get('store_phone'),
        ];

        $pdf = Pdf::loadView('pdf.trial-balance', [
            'reportData' => $reportData,
            'store' => $store,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'printedBy' => auth()->user()->name ?? 'System',
            'printedAt' => Carbon::now()->format('d/m/Y H:i'),
        ]);
        
        $filename = 'Neraca-Saldo-' . Carbon::parse($startDate)->format('Ymd') . '-' . Carbon::parse($endDate)->format('Ymd') . '.pdf';
        
        return $pdf->setPaper('a4', 'portrait')->stream($filename);
    }

    /**
     * Export Purchase Order to PDF - A5 Landscape (Default)
     */
    public function exportPurchaseOrder($id)
    {
        return $this->exportPurchaseOrderFormat($id, 'a5');
    }

    /**
     * Export Purchase Order to PDF - A4 Portrait
     */
    public function exportPurchaseOrderA4($id)
    {
        return $this->exportPurchaseOrderFormat($id, 'a4');
    }

    /**
     * Export Purchase Order to PDF - NCR Paper (Half Letter)
     */
    public function exportPurchaseOrderNCR($id)
    {
        return $this->exportPurchaseOrderFormat($id, 'ncr');
    }

    /**
     * Common method to export PO in different formats
     */
    private function exportPurchaseOrderFormat($id, $format = 'a5')
    {
        $po = \App\Models\PurchaseOrder::with(['supplier', 'items.product.unit', 'user'])->findOrFail($id);

        $storeName = \App\Models\Setting::get('store_name');
        if (!$storeName || $storeName === 'Laravel') {
            $storeName = config('app.name') === 'Laravel' ? 'APOTEK' : config('app.name');
        }

        $store = [
            'name' => $storeName,
            'address' => \App\Models\Setting::get('store_address'),
            'phone' => \App\Models\Setting::get('store_phone'),
        ];

        // Calculate subtotal
        /** @var \Illuminate\Support\Collection $items */
        $items = $po->items;
        $subtotal = $items->sum(function($item) {
            return ($item->price ?? 0) * $item->qty;
        });

        // Generate terbilang (simplified)
        $terbilang = $this->numberToWords($subtotal);

        $data = [
            'po' => $po,
            'store' => $store,
            'terbilang' => $terbilang,
            'approvedBy' => auth()->user()->name ?? 'Manager',
            'printedAt' => Carbon::now()->format('d M Y H:i:s'),
            'format' => $format,
        ];

        $pdf = Pdf::loadView('pdf.purchase-order', $data);

        $filename = 'PO-' . $po->po_number . '-' . strtoupper($format) . '.pdf';

        // Set paper size based on format
        switch($format) {
            case 'a4':
                // A4 Portrait (210mm x 297mm)
                return $pdf->setPaper('a4', 'portrait')->stream($filename);
            
            case 'ncr':
                // NCR/Half Letter (5.5" x 8.5" = 396pt x 612pt)
                return $pdf->setPaper([0, 0, 396, 612], 'portrait')->stream($filename);
            
            case 'a5':
            default:
                // A5 Landscape (210mm x 148mm)
                return $pdf->setPaper([0, 0, 595.28, 419.53], 'landscape')->stream($filename);
        }
    }

    /**
     * Convert number to Indonesian words (simplified)
     */
    private function numberToWords($number)
    {
        if ($number == 0) return 'Nol rupiah';
        
        $words = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan'];
        $teens = ['sepuluh', 'sebelas', 'dua belas', 'tiga belas', 'empat belas', 'lima belas', 
                  'enam belas', 'tujuh belas', 'delapan belas', 'sembilan belas'];
        
        // Simplified version - just return formatted text
        return ucfirst(number_format($number, 0, ',', '.')) . ' rupiah';
    }
}
