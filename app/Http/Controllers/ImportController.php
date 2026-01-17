<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductsImport;
use App\Imports\SuppliersImport;
use App\Imports\StockImport;
use App\Imports\CustomersImport;
use App\Imports\AccountsImport;
use App\Exports\ProductTemplateExport;
use App\Exports\SupplierTemplateExport;
use App\Exports\StockTemplateExport;
use App\Exports\CustomerTemplateExport;
use App\Exports\AccountTemplateExport;

class ImportController extends Controller
{
    public function downloadProductTemplate()
    {
        abort_if(!auth()->user()->can('import products'), 403);
        return Excel::download(new ProductTemplateExport, 'template_produk.xlsx');
    }

    public function downloadSupplierTemplate()
    {
        abort_if(!auth()->user()->can('import suppliers'), 403);
        return Excel::download(new SupplierTemplateExport, 'template_supplier.xlsx');
    }

    public function downloadStockTemplate()
    {
        abort_if(!auth()->user()->can('import stock'), 403);
        return Excel::download(new StockTemplateExport, 'template_stok_opname.xlsx');
    }

    public function downloadCustomerTemplate()
    {
        abort_if(!auth()->user()->can('import customers'), 403);
        return Excel::download(new CustomerTemplateExport, 'template_pelanggan.xlsx');
    }

    public function downloadAccountTemplate()
    {
        abort_if(!auth()->user()->can('import accounts'), 403);
        return Excel::download(new AccountTemplateExport, 'template_daftar_akun.xlsx');
    }

    public function importProducts(Request $request)
    {
        abort_if(!auth()->user()->can('import products'), 403);
        $request->validate([
            'file' => 'required|mimes:xls,xlsx'
        ]);

        try {
            $import = new ProductsImport;
            Excel::import($import, $request->file('file'));
            
            $failures = $import->failures();
            if ($failures->isNotEmpty()) {
                $errorMessages = [];
                foreach ($failures as $failure) {
                    $row = $failure->row();
                    $attribute = $failure->attribute();
                    $errorMsg = implode(', ', $failure->errors());
                    $errorMessages[] = "Baris {$row}.{$attribute}: {$errorMsg}";
                }
                
                $displayErrors = array_slice($errorMessages, 0, 5);
                $remainingCount = count($errorMessages) - 5;
                
                $message = 'Gagal import: ' . implode(' | ', $displayErrors);
                if ($remainingCount > 0) {
                    $message .= " (dan {$remainingCount} error lainnya)";
                }
                
                return redirect()->back()->with('error', $message);
            }
            
            return redirect()->back()->with('message', 'Data Produk berhasil diimport!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            foreach ($failures as $failure) {
                $row = $failure->row();
                $attribute = $failure->attribute();
                $errorMsg = implode(', ', $failure->errors());
                $errorMessages[] = "Baris {$row}.{$attribute}: {$errorMsg}";
            }
            
            $displayErrors = array_slice($errorMessages, 0, 5);
            $remainingCount = count($errorMessages) - 5;
            
            $message = 'Gagal import: ' . implode(' | ', $displayErrors);
            if ($remainingCount > 0) {
                $message .= " (dan {$remainingCount} error lainnya)";
            }
            
            return redirect()->back()->with('error', $message);
        } catch (\Exception $e) {
            \Log::error('Product Import Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }

    public function importSuppliers(Request $request)
    {
        abort_if(!auth()->user()->can('import suppliers'), 403);
        $request->validate([
            'file' => 'required|mimes:xls,xlsx'
        ]);

        try {
            Excel::import(new SuppliersImport, $request->file('file'));
            return redirect()->back()->with('message', 'Data Supplier berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }

    public function importStock(Request $request)
    {
        abort_if(!auth()->user()->can('import stock'), 403);
        $request->validate([
            'file' => 'required|mimes:xls,xlsx'
        ]);

        try {
            $import = new StockImport;
            Excel::import($import, $request->file('file'));
            
            return redirect()->back()->with('message', 'Import berhasil! Data stok telah ditambahkan.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            foreach ($failures as $failure) {
                $errorMessages[] = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
            }
            return redirect()->back()->with('error', 'Validasi gagal: ' . implode(' | ', $errorMessages));
        } catch (\Exception $e) {
            \Log::error('Stock Import Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }

    public function importCustomers(Request $request)
    {
        abort_if(!auth()->user()->can('import customers'), 403);
        $request->validate([
            'file' => 'required|mimes:xls,xlsx'
        ]);

        try {
            Excel::import(new CustomersImport, $request->file('file'));
            return redirect()->back()->with('message', 'Data Pelanggan berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }

    public function importAccounts(Request $request)
    {
        abort_if(!auth()->user()->can('import accounts'), 403);
        $request->validate([
            'file' => 'required|mimes:xls,xlsx'
        ]);

        try {
            Excel::import(new AccountsImport, $request->file('file'));
            return redirect()->back()->with('message', 'Daftar Akun berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }

    public function downloadExpenseCategoryTemplate()
    {
        abort_if(!auth()->user()->can('manage expense categories'), 403);
        return Excel::download(new \App\Exports\ExpenseCategoryTemplateExport, 'template_kategori_pengeluaran.xlsx');
    }

    public function importExpenseCategories(Request $request)
    {
        abort_if(!auth()->user()->can('manage expense categories'), 403);
        $request->validate([
            'file' => 'required|mimes:xls,xlsx'
        ]);

        try {
            Excel::import(new \App\Imports\ExpenseCategoriesImport, $request->file('file'));
            return redirect()->back()->with('message', 'Kategori Pengeluaran berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }
}
