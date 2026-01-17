<?php

namespace App\Livewire\Finance;

use App\Models\FixedAsset;
use App\Models\Account;
use App\Services\AccountingService;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class AssetIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $showAssetModal = false;
    public $showDepreciationModal = false;
    
    // Form fields
    public $assetId = null;
    public $asset_code, $asset_name, $tax_group = '1', $method = 'straight_line', $acquisition_date, $acquisition_cost, $salvage_value = 0;
    public $asset_account_id, $accumulated_depreciation_account_id, $depreciation_expense_account_id, $description;

    // Depreciation fields
    public $depreciation_month, $depreciation_year;

    public function mount()
    {
        $this->depreciation_month = now()->month;
        $this->depreciation_year = now()->year;
        $this->acquisition_date = now()->format('Y-m-d');
        
        // Try to find default accounts
        $this->asset_account_id = Account::where('code', 'LIKE', '1-3%')->first()?->id; // Fixed Assets
        $this->accumulated_depreciation_account_id = Account::where('name', 'LIKE', '%Akumulasi%')->first()?->id;
        $this->depreciation_expense_account_id = Account::where('name', 'LIKE', '%Penyusutan%')->where('type', 'expense')->first()?->id;
    }

    public function render()
    {
        $assets = FixedAsset::with(['assetAccount', 'accumulatedAccount'])
            ->where('asset_name', 'like', '%' . $this->search . '%')
            ->orWhere('asset_code', 'like', '%' . $this->search . '%')
            ->paginate(10);

        return view('livewire.finance.asset-index', [
            'assets' => $assets,
            'taxGroups' => FixedAsset::getTaxGroups(),
            'accounts' => Account::orderBy('code')->get(),
        ]);
    }

    public function createAsset()
    {
        $this->reset(['assetId', 'asset_code', 'asset_name', 'tax_group', 'method', 'acquisition_cost', 'salvage_value', 'description']);
        $this->tax_group = '1';
        $this->method = 'straight_line';
        $this->acquisition_date = now()->format('Y-m-d');
        $this->showAssetModal = true;
    }

    public function saveAsset()
    {
        $this->validate([
            'asset_code' => 'required|unique:fixed_assets,asset_code,' . $this->assetId,
            'asset_name' => 'required',
            'tax_group' => 'required',
            'method' => 'required',
            'acquisition_date' => 'required|date',
            'acquisition_cost' => 'required|numeric|min:0',
            'salvage_value' => 'required|numeric|min:0',
            'asset_account_id' => 'required|exists:accounts,id',
            'accumulated_depreciation_account_id' => 'required|exists:accounts,id',
            'depreciation_expense_account_id' => 'required|exists:accounts,id',
        ]);

        $groupInfo = FixedAsset::getTaxGroups()[$this->tax_group];

        FixedAsset::updateOrCreate(['id' => $this->assetId], [
            'asset_code' => $this->asset_code,
            'asset_name' => $this->asset_name,
            'tax_group' => $this->tax_group,
            'method' => $this->method,
            'acquisition_date' => $this->acquisition_date,
            'acquisition_cost' => $this->acquisition_cost,
            'salvage_value' => $this->salvage_value,
            'useful_life_years' => $groupInfo['life'],
            'asset_account_id' => $this->asset_account_id,
            'accumulated_depreciation_account_id' => $this->accumulated_depreciation_account_id,
            'depreciation_expense_account_id' => $this->depreciation_expense_account_id,
            'description' => $this->description,
        ]);

        $this->showAssetModal = false;
        session()->flash('message', 'Aset berhasil disimpan.');
    }

    public function processDepreciation(AccountingService $accountingService)
    {
        $assets = FixedAsset::where('is_active', true)->get();
        $processedCount = 0;
        $periodDate = \Carbon\Carbon::create($this->depreciation_year, $this->depreciation_month, 1)->endOfMonth()->format('Y-m-d');

        foreach ($assets as $asset) {
            $result = $accountingService->postAssetDepreciation($asset->id, $periodDate);
            if ($result) {
                $processedCount++;
            }
        }

        $this->showDepreciationModal = false;
        session()->flash('message', "Berhasil memproses penyusutan untuk $processedCount aset.");
    }
}
