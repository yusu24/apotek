<?php

namespace App\Exports;

use App\Models\Account;
use App\Models\JournalEntryLine;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GeneralLedgerExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $accountId;
    protected $startDate;
    protected $endDate;
    protected $openingBalance;
    protected $runningBalance;
    protected $increaseOnDebit;

    public function __construct($accountId, $startDate, $endDate)
    {
        $this->accountId = $accountId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->calculateOpeningBalance();
    }

    protected function calculateOpeningBalance()
    {
        $account = Account::find($this->accountId);
        if (!$account) return;

        $this->increaseOnDebit = in_array($account->type, ['asset', 'expense']);

        $preLines = JournalEntryLine::whereHas('journalEntry', function($q) {
                $q->whereDate('date', '<', $this->startDate);
            })
            ->where('account_id', $this->accountId)
            ->get();

        $this->openingBalance = 0;
        foreach ($preLines as $line) {
            if ($this->increaseOnDebit) {
                $this->openingBalance += ($line->debit - $line->credit);
            } else {
                $this->openingBalance += ($line->credit - $line->debit);
            }
        }
        $this->runningBalance = $this->openingBalance;
    }

    public function collection()
    {
        $lines = JournalEntryLine::with('journalEntry')
            ->whereHas('journalEntry', function($q) {
                $q->whereDate('date', '>=', $this->startDate)
                  ->whereDate('date', '<=', $this->endDate);
            })
            ->where('account_id', $this->accountId)
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
            ->orderBy('journal_entries.date')
            ->orderBy('journal_entries.id')
            ->select('journal_entry_lines.*')
            ->get();

        return $lines;
    }

    public function map($line): array
    {
        if ($this->increaseOnDebit) {
            $this->runningBalance += ($line->debit - $line->credit);
        } else {
            $this->runningBalance += ($line->credit - $line->debit);
        }

        return [
            $line->journalEntry->date->format('d/m/Y'),
            $line->journalEntry->entry_number,
            $line->journalEntry->description,
            $line->debit > 0 ? $line->debit : '',
            $line->credit > 0 ? $line->credit : '',
            $this->runningBalance
        ];
    }

    public function headings(): array
    {
        $account = Account::find($this->accountId);
        return [
            ['Buku Besar: ' . ($account ? $account->name : '-')],
            ['Periode: ' . $this->startDate . ' - ' . $this->endDate],
            ['Saldo Awal: ' . $this->openingBalance],
            [''],
            ['Tanggal', 'No. Jurnal', 'Keterangan', 'Debit', 'Kredit', 'Saldo']
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            2 => ['font' => ['italic' => true]],
            3 => ['font' => ['bold' => true]],
            5 => ['font' => ['bold' => true], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE0E0E0']]],
        ];
    }
}
