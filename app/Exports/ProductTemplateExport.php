<?php

namespace App\Exports;

use App\Models\Category;
use App\Models\Unit;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new ProductTemplateSheet(),
            new CategoryReferenceSheet(),
            new UnitReferenceSheet(),
        ];
    }
}

class ProductTemplateSheet implements WithHeadings, WithTitle, WithStyles, ShouldAutoSize
{
    public function headings(): array
    {
        return [
            'nama_produk',
            'id_kategori_lihat_sheet_kategori',
            'id_satuan_lihat_sheet_satuan',
            'harga_jual',
            'stok_minimal',
            'barcode',
            'keterangan',
        ];
    }

    public function title(): string
    {
        return 'Input Produk';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4472C4']]],
        ];
    }
}

class CategoryReferenceSheet implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize
{
    public function collection()
    {
        return Category::orderBy('name')->get(['id', 'name']);
    }

    public function headings(): array
    {
        return ['ID', 'Nama Kategori'];
    }

    public function title(): string
    {
        return 'Kategori';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '70AD47']]],
        ];
    }
}

class UnitReferenceSheet implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize
{
    public function collection()
    {
        return Unit::orderBy('name')->get(['id', 'name']);
    }

    public function headings(): array
    {
        return ['ID', 'Nama Satuan'];
    }

    public function title(): string
    {
        return 'Satuan';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'FFC000']]],
        ];
    }
}
