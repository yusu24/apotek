<?php

namespace App\Imports;

use App\Models\Sale;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Validators\Failure;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class OmsetImport implements ToCollection, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    private $successCount = 0;
    private $currentRow = 1;

    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                $this->currentRow++;
                $this->processRow($row);
            }
        });
    }

    public function getSuccessCount()
    {
        return $this->successCount;
    }

    private function processRow($row)
    {
        $data = is_array($row) ? $row : (method_exists($row, 'toArray') ? $row->toArray() : (array)$row);

        $dateInput = $data['tanggal'] ?? null;
        $yearInput = $data['tahun'] ?? null;
        $omsetInput = $data['omset'] ?? null;
        $hppInput = $data['hpp'] ?? 0;
        $labaInput = $data['laba'] ?? null;

        // Skip empty rows
        if (empty($dateInput) && empty($yearInput) && empty($omsetInput)) {
            return;
        }

        if (empty($dateInput) && empty($yearInput)) {
            $this->failures[] = new Failure($this->currentRow, 'tanggal', ['Tanggal atau Tahun wajib diisi'], $data);
            return;
        }

        if (empty($omsetInput) || !is_numeric($omsetInput) || floatval($omsetInput) < 0) {
            $this->failures[] = new Failure($this->currentRow, 'omset', ['Omset wajib diisi dengan angka minimal 0'], $data);
            return;
        }

        $date = null;
        if (!empty($dateInput)) {
            try {
                if (is_numeric($dateInput)) {
                    $date = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateInput));
                } else {
                    $date = Carbon::parse($dateInput);
                }
            } catch (\Exception $e) {
                $this->failures[] = new Failure($this->currentRow, 'tanggal', ['Format tanggal tidak valid (gunakan YYYY-MM-DD atau DD-MM-YYYY)'], $data);
                return;
            }
        } elseif (!empty($yearInput)) {
            if (!is_numeric($yearInput) || strlen((string)$yearInput) !== 4) {
                $this->failures[] = new Failure($this->currentRow, 'tahun', ['Format tahun tidak valid (harus 4 digit angka, misal: 2022)'], $data);
                return;
            }
            $date = Carbon::createFromFormat('Y-m-d', $yearInput . '-01-01');
        }

        $omset = floatval($omsetInput);
        $hpp = floatval($hppInput);
        $laba = $labaInput !== null ? floatval($labaInput) : ($omset - $hpp);
        $userId = Auth::id() ?? 1;

        // Generate unique invoice_no for this record
        $invoiceNo = 'OMSET-' . $date->format('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

        $sale = Sale::create([
            'user_id' => $userId,
            'invoice_no' => $invoiceNo,
            'date' => $date->format('Y-m-d H:i:s'),
            'total_amount' => $omset,
            'tax' => 0,
            'discount' => 0,
            'grand_total' => $omset,
            'dpp' => $omset,
            'cogs' => $hpp,
            'profit' => $laba,
            'payment_method' => 'cash',
            'notes' => 'Imported historical turnover/omset',
        ]);

        // Post to journal
        $accountingService = new \App\Services\AccountingService();
        $accountingService->postSaleJournal($sale->id);

        $this->successCount++;
    }

    public function rules(): array
    {
        return [
            'tanggal' => 'required_without:tahun',
            'tahun' => 'required_without:tanggal',
            'omset' => 'required|numeric|min:0',
            'hpp' => 'nullable|numeric|min:0',
            'laba' => 'nullable|numeric',
        ];
    }
}
