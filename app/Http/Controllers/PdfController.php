<?php

namespace App\Http\Controllers;

use App\Models\GoodsReceipt;
use App\Models\Product;
use App\Models\StockMovement;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;

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
            'printedBy' => auth()->user()->name,
            'printedAt' => Carbon::now()->format('d/m/Y H:i'),
            'apotekName' => $apotekName,
        ];

        // Generate PDF
        $pdf = Pdf::loadView('pdf.goods-receipt-pdf', $data);
        $pdf->setPaper('a4', 'portrait');

        // Download PDF
        $filename = 'Penerimaan-Barang-' . $receipt->delivery_note_number . '.pdf';
        return $pdf->download($filename);
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
            'printedBy' => auth()->user()->name,
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
            \Storage::put('user-manual.md', $tutorialContent);
        }
        
        $markdown = \Storage::get('user-manual.md');
        
        // Convert markdown to HTML (basic conversion)
        $html = $this->convertMarkdownToHtml($markdown);
        
        $apotekName = \App\Models\Setting::get('store_name');
        if (!$apotekName || $apotekName === 'Laravel') {
            $apotekName = config('app.name') === 'Laravel' ? 'APOTEK' : config('app.name');
        }

        // Prepare data for PDF
        $data = [
            'content' => $html,
            'printedBy' => auth()->user()->name,
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
        if (class_exists('\Parsedown')) {
            $parsedown = new \Parsedown();
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
        $startDate = $request->get('startDate', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('endDate', now()->endOfMonth()->format('Y-m-d'));

        $accountingService = new \App\Services\AccountingService();
        $reportData = $accountingService->getBalanceSheet($startDate, $endDate);
        
        $storeName = \App\Models\Setting::get('store_name');
        if (!$storeName || $storeName === 'Laravel') {
            $storeName = config('app.name') === 'Laravel' ? 'APOTEK' : config('app.name');
        }

        $store = [
            'name' => $storeName,
            'address' => \App\Models\Setting::get('store_address'),
            'phone' => \App\Models\Setting::get('store_phone'),
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.balance-sheet', [
            'reportData' => $reportData,
            'store' => $store,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'printedBy' => auth()->user()->name ?? 'System',
            'printedAt' => Carbon::now()->format('d/m/Y H:i'),
        ]);
        
        $filename = 'Neraca-' . Carbon::parse($startDate)->format('Ymd') . '-' . Carbon::parse($endDate)->format('Ymd') . '.pdf';
        
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

        $accountingService = new \App\Services\AccountingService();
        $reportData = $accountingService->getLedgerReport($startDate, $endDate, $accountId);
        
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
            'printedBy' => auth()->user()->name ?? 'System',
            'printedAt' => Carbon::now()->format('d/m/Y H:i'),
        ]);
        
        $filename = 'Buku-Besar-' . Carbon::parse($startDate)->format('Ymd') . '-' . Carbon::parse($endDate)->format('Ymd') . '.pdf';
        
        return $pdf->setPaper('a4', 'landscape')->stream($filename);
    }
}
