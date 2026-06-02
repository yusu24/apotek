<?php

namespace App\Imports;

use App\Models\Account;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class AccountsImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        return new Account([
            'code'      => $row['kode_akun'],
            'name'      => $row['nama_akun'],
            'type'      => $row['tipe'],
            'category'  => $row['kategori'],
            'is_active' => true,
            'is_system' => false,
            'balance'   => 0,
        ]);
    }

    public function rules(): array
    {
        return [
            'kode_akun' => 'required|string|unique:accounts,code',
            'nama_akun' => 'required|string|max:255',
            'tipe'      => 'required|in:asset,liability,equity,revenue,expense',
            'kategori'  => 'required|in:cash_bank,current_asset,fixed_asset,current_liability,long_term_liability,equity,operating_revenue,other_revenue,cogs,operating_expense,other',
        ];
    }

    public function prepareForValidation($data, $index)
    {
        // Normalize Tipe Akun
        if (isset($data['tipe'])) {
            $data['tipe'] = $this->normalizeType($data['tipe']);
        }

        // Normalize Kategori Akun
        if (isset($data['kategori'])) {
            $type = $data['tipe'] ?? '';
            $data['kategori'] = $this->normalizeCategory($data['kategori'], $type);
        }

        return $data;
    }

    private function normalizeType($value): string
    {
        $val = strtolower(trim($value));
        
        switch ($val) {
            case 'asset':
            case 'assets':
            case 'aset':
            case 'harta':
            case 'aktiva':
                return 'asset';
                
            case 'liability':
            case 'liabilities':
            case 'kewajiban':
            case 'utang':
            case 'pasiva':
                return 'liability';
                
            case 'equity':
            case 'ekuitas':
            case 'modal':
                return 'equity';
                
            case 'revenue':
            case 'revenues':
            case 'pendapatan':
            case 'penjualan':
                return 'revenue';
                
            case 'expense':
            case 'expenses':
            case 'beban':
            case 'biaya':
                return 'expense';
                
            default:
                return $value;
        }
    }

    private function normalizeCategory($value, $type = ''): string
    {
        $val = strtolower(trim($value));
        
        // Capital / Modal -> equity
        if ($val === 'capital' || $val === 'modal' || $val === 'modal sendiri') {
            return 'equity';
        }
        
        // Sales -> operating_revenue
        if ($val === 'sales' || $val === 'penjualan') {
            return 'operating_revenue';
        }
        
        // Other (based on Type)
        if ($val === 'other' || $val === 'lain' || $val === 'lainnya' || $val === 'lain lain' || $val === 'lain-lain') {
            if ($type === 'revenue') {
                return 'other_revenue';
            }
            if ($type === 'expense') {
                return 'other';
            }
            return 'other';
        }
        
        // Kas & Bank
        if (str_contains($val, 'kas') || str_contains($val, 'bank')) {
            return 'cash_bank';
        }
        
        // HPP / COGS
        if (str_contains($val, 'hpp') || str_contains($val, 'cogs') || str_contains($val, 'pokok penjualan')) {
            return 'cogs';
        }
        
        // Fixed Asset / Aset Tetap
        if (str_contains($val, 'tetap') || str_contains($val, 'fixed') || str_contains($val, 'peralatan') || str_contains($val, 'gedung')) {
            return 'fixed_asset';
        }
        
        // Current Asset / Aset Lancar
        if (str_contains($val, 'lancar') && (str_contains($val, 'aset') || str_contains($val, 'asset') || str_contains($val, 'harta') || str_contains($val, 'aktiva'))) {
            return 'current_asset';
        }
        if (str_contains($val, 'piutang') || str_contains($val, 'stok') || str_contains($val, 'persediaan')) {
            return 'current_asset';
        }
        
        // Long Term Liability / Kewajiban Jangka Panjang
        if (str_contains($val, 'panjang') || str_contains($val, 'long term') || str_contains($val, 'long-term')) {
            return 'long_term_liability';
        }
        
        // Current Liability / Kewajiban Lancar
        if (str_contains($val, 'lancar') && (str_contains($val, 'kewajiban') || str_contains($val, 'liability') || str_contains($val, 'utang') || str_contains($val, 'pasiva'))) {
            return 'current_liability';
        }
        
        // Equity / Ekuitas
        if (str_contains($val, 'equity') || str_contains($val, 'ekuitas')) {
            return 'equity';
        }
        
        // Operating Revenue / Pendapatan Operasional
        if (str_contains($val, 'pendapatan') && (str_contains($val, 'operasional') || str_contains($val, 'usaha') || str_contains($val, 'operating'))) {
            return 'operating_revenue';
        }
        
        // Other Revenue
        if (str_contains($val, 'pendapatan')) {
            return 'other_revenue';
        }
        
        // Operating Expense / Beban Operasional
        if ((str_contains($val, 'beban') || str_contains($val, 'biaya')) && (str_contains($val, 'operasional') || str_contains($val, 'usaha') || str_contains($val, 'operating'))) {
            return 'operating_expense';
        }
        
        // Other Expense
        if (str_contains($val, 'beban') || str_contains($val, 'biaya')) {
            return 'other';
        }
        
        // Generic word mappings
        if ($val === 'aset' || $val === 'asset' || $val === 'assets' || $val === 'harta') {
            return 'current_asset';
        }
        if ($val === 'kewajiban' || $val === 'liability' || $val === 'liabilities' || $val === 'utang') {
            return 'current_liability';
        }
        if ($val === 'pendapatan' || $val === 'revenue' || $val === 'revenues') {
            return 'operating_revenue';
        }
        if ($val === 'beban' || $val === 'biaya' || $val === 'expense' || $val === 'expenses') {
            return 'operating_expense';
        }
        
        // Fallback to exact switch checks for other values
        $valNormalized = str_replace(['_', '-'], ' ', $val);
        switch ($valNormalized) {
            case 'cash bank':
            case 'cash & bank':
                return 'cash_bank';
            case 'current asset':
            case 'current assets':
            case 'aset lancar':
            case 'aset lancar lainnya':
                return 'current_asset';
            case 'fixed asset':
            case 'fixed assets':
            case 'aset tetap':
                return 'fixed_asset';
            case 'current liability':
            case 'current liabilities':
            case 'kewajiban lancar':
                return 'current_liability';
            case 'long term liability':
            case 'long term liabilities':
            case 'kewajiban jangka panjang':
                return 'long_term_liability';
            case 'equity':
            case 'ekuitas':
                return 'equity';
            case 'operating revenue':
            case 'pendapatan operasional':
                return 'operating_revenue';
            case 'other revenue':
            case 'pendapatan lain lain':
                return 'other_revenue';
            case 'cogs':
            case 'hpp':
                return 'cogs';
            case 'operating expense':
            case 'operating expenses':
            case 'beban operasional':
                return 'operating_expense';
            case 'other':
            case 'other expense':
            case 'beban lain lain':
                return 'other';
            default:
                return $value;
        }
    }
}
