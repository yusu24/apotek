<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GeneralLedgerExport;
use App\Exports\AgingReportExport;
use App\Exports\IncomeStatementExport;
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
}
