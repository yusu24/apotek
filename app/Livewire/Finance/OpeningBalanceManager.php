<?php

namespace App\Livewire\Finance;

use App\Models\OpeningBalance;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class OpeningBalanceManager extends Component
{
    public $openingBalanceId;
    public $cash_amount = null;
    public $bank_amount = null;
    public $capital_amount = null;
    
    public $assets = [];
    public $debts = [];

    public $summary = [
        'total_assets' => 0,
        'total_liabilities' => 0,
        'total_equity' => 0,
        'difference' => 0,
        'is_balanced' => true
    ];

    public function mount()
    {
        $ob = OpeningBalance::with(['assets', 'debts'])->first();
        if ($ob) {
            $this->openingBalanceId = $ob->id;
            $this->cash_amount = number_format($ob->cash_amount, 2, '.', '');
            $this->bank_amount = number_format($ob->bank_amount, 2, '.', '');
            $this->capital_amount = number_format($ob->capital_amount, 2, '.', '');
            
            foreach ($ob->assets as $asset) {
                $this->assets[] = [
                    'id' => $asset->id,
                    'asset_name' => $asset->asset_name,
                    'amount' => number_format($asset->amount, 2, '.', ''),
                    'acquisition_date' => $asset->acquisition_date ? $asset->acquisition_date->format('Y-m-d') : null
                ];
            }

            foreach ($ob->debts as $debt) {
                $this->debts[] = [
                    'id' => $debt->id,
                    'debt_name' => $debt->debt_name,
                    'debt_type' => $debt->debt_type,
                    'amount' => number_format($debt->amount, 2, '.', '')
                ];
            }
        } else {
            // Default empty rows
            $this->addAsset();
            $this->addDebt();
        }
        $this->updateSummary();
    }

    public function addAsset()
    {
        $this->assets[] = ['asset_name' => '', 'amount' => null, 'acquisition_date' => null];
    }

    public function removeAsset($index)
    {
        unset($this->assets[$index]);
        $this->assets = array_values($this->assets);
        $this->updateSummary();
    }

    public function addDebt()
    {
        $this->debts[] = ['debt_name' => '', 'debt_type' => 'supplier', 'amount' => null];
    }

    public function removeDebt($index)
    {
        unset($this->debts[$index]);
        $this->debts = array_values($this->debts);
        $this->updateSummary();
    }

    public function updated($propertyName)
    {
        $this->updateSummary();
    }

    public function updateSummary()
    {
        $totalAssets = (float)$this->cash_amount + (float)$this->bank_amount;
        foreach ($this->assets as $asset) {
            $totalAssets += (float)($asset['amount'] ?? 0);
        }

        $totalLiabilities = 0;
        foreach ($this->debts as $debt) {
            $totalLiabilities += (float)($debt['amount'] ?? 0);
        }

        $totalEquity = (float)$this->capital_amount;

        $difference = $totalAssets - ($totalLiabilities + $totalEquity);

        $this->summary = [
            'total_assets' => $totalAssets,
            'total_liabilities' => $totalLiabilities,
            'total_equity' => $totalEquity,
            'difference' => $difference,
            'is_balanced' => abs($difference) < 0.01
        ];
    }

    public function save()
    {
        $this->validate([
            'cash_amount' => 'required|numeric|min:0',
            'bank_amount' => 'required|numeric|min:0',
            'capital_amount' => 'required|numeric|min:0',
            'assets.*.asset_name' => 'required_with:assets.*.amount',
            'assets.*.amount' => 'nullable|numeric|min:0',
            'debts.*.debt_name' => 'required_with:debts.*.amount',
            'debts.*.amount' => 'nullable|numeric|min:0',
        ]);

        $this->updateSummary();

        if (!$this->summary['is_balanced']) {
            session()->flash('error', 'Neraca belum seimbang! Selisih: Rp ' . number_format(abs($this->summary['difference']), 0, ',', '.'));
            return;
        }

        DB::beginTransaction();
        try {
            $ob = OpeningBalance::updateOrCreate(
                ['id' => $this->openingBalanceId],
                [
                    'cash_amount' => $this->cash_amount,
                    'bank_amount' => $this->bank_amount,
                    'capital_amount' => $this->capital_amount,
                    'is_confirmed' => true
                ]
            );

            // Sync Assets
            $ob->assets()->delete();
            foreach ($this->assets as $asset) {
                if (!empty($asset['asset_name']) && $asset['amount'] > 0) {
                    $ob->assets()->create($asset);
                }
            }

            // Sync Debts
            $ob->debts()->delete();
            foreach ($this->debts as $debt) {
                if (!empty($debt['debt_name']) && $debt['amount'] > 0) {
                    $ob->debts()->create($debt);
                }
            }

            // Generate/Update Journal
            $ob->syncJournal();

            DB::commit();
            session()->flash('success', 'Neraca awal berhasil disimpan dan jurnal pembukaan telah dibuat.');
            return redirect()->route('finance.opening-balance');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal menyimpan neraca awal: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.finance.opening-balance')
            ->layout('layouts.app');
    }
}
