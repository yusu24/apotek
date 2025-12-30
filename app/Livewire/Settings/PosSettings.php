<?php

namespace App\Livewire\Settings;

use App\Models\Setting;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class PosSettings extends Component
{
    public $pos_paper_size;
    public $pos_ppn_mode;
    public $pos_ppn_rate;
    public $success_message = '';

    public function mount()
    {
        // Check permission
        if (!auth()->user()->can('manage pos settings')) {
            abort(403, 'Unauthorized');
        }

        $this->pos_paper_size = Setting::get('pos_paper_size', '80mm');
        $this->pos_ppn_mode = Setting::get('pos_ppn_mode', 'off');
        $this->pos_ppn_rate = Setting::get('pos_ppn_rate', 11);
    }

    public function save()
    {
        Setting::set('pos_paper_size', $this->pos_paper_size);
        Setting::set('pos_ppn_mode', $this->pos_ppn_mode);
        Setting::set('pos_ppn_rate', $this->pos_ppn_rate);

        $this->success_message = 'Pengaturan kasir berhasil disimpan.';
        $this->dispatch('settings-updated');
    }

    public function render()
    {
        return view('livewire.settings.pos-settings');
    }
}
