<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class DetailedProfitLossExport implements FromView, ShouldAutoSize
{
    protected $current;
    protected $previous;
    protected $startDate;
    protected $endDate;
    protected $prevStartDate;
    protected $prevEndDate;
    protected $storeName;
    protected $showComparison;

    public function __construct($current, $previous, $startDate, $endDate, $prevStartDate, $prevEndDate, $storeName = null, $showComparison = true)
    {
        $this->current = $current;
        $this->previous = $previous;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->prevStartDate = $prevStartDate;
        $this->prevEndDate = $prevEndDate;
        $this->storeName = $storeName ?? \App\Models\Setting::get('store_name', 'Apotek');
        $this->showComparison = $showComparison;
    }

    public function view(): View
    {
        return view('exports.detailed-profit-loss', [
            'current' => $this->current,
            'previous' => $this->previous,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'prevStartDate' => $this->prevStartDate,
            'prevEndDate' => $this->prevEndDate,
            'storeName' => $this->storeName,
            'showComparison' => $this->showComparison,
        ]);
    }
}
