<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GeneralLedgerExport;
use App\Exports\AgingReportExport;
use App\Exports\IncomeStatementExport;
use App\Exports\SalesReportExport;
use App\Exports\AccountsExport;
use App\Models\Account;
use App\Services\AccountingService;

class ExcelController extends Controller
{
    public function exportAgingReport(Request $request)
    {
        abort_if(!auth()->user()->can('export aging report'), 403, 'Anda tidak memiliki hak akses untuk export aging report.');
        
        $request->validate([
            'showPaid' => 'required',
        ]);

        $filename = 'Aging_Report_AP_AR_' . date('Ymd_His') . '.xlsx';

        return Excel::download(
            new AgingReportExport($request->showPaid),
            $filename
        );
    }

    public function exportGeneralLedger(Request $request)
    {
        abort_if(!auth()->user()->can('export general ledger'), 403, 'Anda tidak memiliki hak akses untuk export ledger.');
        
        $request->validate([
            'accountId' => 'required|exists:accounts,id',
            'startDate' => 'required|date',
            'endDate' => 'required|date',
        ]);

        $account = Account::findOrFail($request->accountId);
        $filename = 'Ledger_' . str_replace(' ', '_', $account->name) . '_' . date('Ymd') . '.xlsx';

        return Excel::download(
            new GeneralLedgerExport($request->accountId, $request->startDate, $request->endDate),
            $filename
        );
    }

    public function exportIncomeStatement(Request $request)
    {
        abort_if(!auth()->user()->can('view reports'), 403, 'Anda tidak memiliki hak akses.');

        $request->validate([
            'startDate' => 'required|date',
            'endDate' => 'required|date',
        ]);

        $accountingService = new AccountingService();
        $reportData = $accountingService->getIncomeStatement($request->startDate, $request->endDate);

        $filename = 'Laporan_Laba_Rugi_' . date('Ymd_His') . '.xlsx';

        return Excel::download(
            new IncomeStatementExport($reportData),
            $filename
        );
    }

    public function exportTrialBalance(Request $request)
    {
        abort_if(!auth()->user()->can('view balance sheet'), 403, 'Anda tidak memiliki hak akses.');

        $request->validate([
            'startDate' => 'required|date',
            'endDate' => 'required|date',
        ]);

        $accountingService = new AccountingService();
        $reportData = $accountingService->getTrialBalance($request->startDate, $request->endDate);

        $filename = 'Neraca_Saldo_' . date('Ymd_His') . '.xlsx';

        return Excel::download(
            new \App\Exports\TrialBalanceExport($reportData),
            $filename
        );
    }

    public function exportSalesReport(Request $request)
    {
        abort_if(!auth()->user()->can('view sales reports'), 403, 'Unauthorized');

        $request->validate([
            'startDate' => 'required|date',
            'endDate' => 'required|date',
        ]);

        $filename = 'Laporan_Penjualan_' . $request->startDate . '_to_' . $request->endDate . '.xlsx';

        return Excel::download(
            new SalesReportExport(
                $request->startDate,
                $request->endDate,
                $request->paymentMethod ?? 'all',
                $request->search ?? ''
            ),
            $filename
        );
    }

    public function exportAccounts(Request $request)
    {
        abort_if(!auth()->user()->can('view accounts'), 403, 'Unauthorized');

        $filename = 'Daftar_Akun_' . date('Ymd_His') . '.xlsx';

        return Excel::download(
            new AccountsExport(
                $request->search ?? '',
                $request->typeFilter ?? ''
            ),
            $filename
        );
    }
}
