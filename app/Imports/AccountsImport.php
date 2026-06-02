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
            $data['kategori'] = $this->normalizeCategory($data['kategori']);
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

    private function normalizeCategory($value): string
    {
        $val = strtolower(trim($value));
        
        // Remove spaces, dashes and underscores for easy comparison
        $valNormalized = str_replace(['_', '-'], ' ', $val);
        
        switch ($valNormalized) {
            case 'kas bank':
            case 'kas dan bank':
            case 'kas & bank':
            case 'cash bank':
            case 'cash & bank':
                return 'cash_bank';
                
            case 'current asset':
            case 'current assets':
            case 'aset lancar':
            case 'aset lancar lainnya':
            case 'piutang':
            case 'stok':
            case 'inventaris':
                return 'current_asset';
                
            case 'fixed asset':
            case 'fixed assets':
            case 'aset tetap':
            case 'harta tetap':
            case 'peralatan':
            case 'gedung':
                return 'fixed_asset';
                
            case 'current liability':
            case 'current liabilities':
            case 'kewajiban lancar':
            case 'utang lancar':
                return 'current_liability';
                
            case 'long term liability':
            case 'long term liabilities':
            case 'kewajiban jangka panjang':
            case 'utang jangka panjang':
                return 'long_term_liability';
                
            case 'equity':
            case 'ekuitas':
            case 'modal':
                return 'equity';
                
            case 'operating revenue':
            case 'pendapatan operasional':
            case 'pendapatan usaha':
                return 'operating_revenue';
                
            case 'other revenue':
            case 'pendapatan lain lain':
            case 'pendapatan lainlain':
            case 'pendapatan diluar usaha':
                return 'other_revenue';
                
            case 'cogs':
            case 'hpp':
            case 'harga pokok penjualan':
                return 'cogs';
                
            case 'operating expense':
            case 'operating expenses':
            case 'beban operasional':
            case 'biaya operasional':
            case 'beban usaha':
                return 'operating_expense';
                
            case 'other':
            case 'other expense':
            case 'other expenses':
            case 'beban lain lain':
            case 'beban lainlain':
            case 'biaya lain lain':
            case 'biaya lainlain':
                return 'other';
                
            default:
                return $value;
        }
    }
}
