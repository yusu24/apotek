<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductsImport;
use App\Imports\SuppliersImport;
use App\Imports\StockImport;
use App\Exports\ProductTemplateExport;
use App\Exports\SupplierTemplateExport;
use App\Exports\StockTemplateExport;

class ImportController extends Controller
{
    public function downloadProductTemplate()
    {
        return Excel::download(new ProductTemplateExport, 'template_produk.xlsx');
    }

    public function downloadSupplierTemplate()
    {
        return Excel::download(new SupplierTemplateExport, 'template_supplier.xlsx');
    }

    public function downloadStockTemplate()
    {
        return Excel::download(new StockTemplateExport, 'template_stok_opname.xlsx');
    }

    public function importProducts(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xls,xlsx'
        ]);

        try {
            Excel::import(new ProductsImport, $request->file('file'));
            return redirect()->back()->with('message', 'Data Produk berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }

    public function importSuppliers(Request $request)
    {
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
        $request->validate([
            'file' => 'required|mimes:xls,xlsx'
        ]);

        try {
            Excel::import(new StockImport, $request->file('file'));
            return redirect()->back()->with('message', 'Data Stok berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }
}
