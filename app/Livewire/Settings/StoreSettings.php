<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

#[Layout('layouts.app')]
class StoreSettings extends Component
{
    use WithFileUploads;

    public $store_name;
    public $store_address;
    public $store_phone;
    public $store_email;
    public $store_tax_id;

    // Logo
    public $store_logo;
    public $logo_url;
    public $login_logo;
    public $login_logo_url;
    public $sidebar_logo;
    public $sidebar_logo_url;

    // Social Media
    public $store_website;
    public $store_facebook;
    public $store_instagram;
    public $store_tiktok;

    // Bank Details
    public $store_bank_name;
    public $store_bank_account;
    public $store_bank_holder;

    // Footer
    public $store_footer_note;

    public $success_message = '';

    public function mount()
    {
        // Check permission
        if (!auth()->user()->can('manage settings')) {
            abort(403, 'Unauthorized');
        }

        // Load current settings
        $settings = Setting::getMultiple([
            'store_name', 'store_address', 'store_phone', 'store_email', 'store_tax_id',
            'store_logo_path', 'store_login_logo_path', 'store_sidebar_logo_path',
            'store_website', 'store_facebook', 'store_instagram', 'store_tiktok',
            'store_bank_name', 'store_bank_account', 'store_bank_holder', 'store_footer_note'
        ]);

        $this->store_name = $settings['store_name'] ?? '';
        $this->store_address = $settings['store_address'] ?? '';
        $this->store_phone = $settings['store_phone'] ?? '';
        $this->store_email = $settings['store_email'] ?? '';
        $this->store_tax_id = $settings['store_tax_id'] ?? '';
        
        $this->logo_url = $settings['store_logo_path'] ? asset('storage/' . $settings['store_logo_path']) : null;
        $this->login_logo_url = $settings['store_login_logo_path'] ? asset('storage/' . $settings['store_login_logo_path']) : null;
        $this->sidebar_logo_url = $settings['store_sidebar_logo_path'] ? asset('storage/' . $settings['store_sidebar_logo_path']) : null;
        
        $this->store_website = $settings['store_website'] ?? '';
        $this->store_facebook = $settings['store_facebook'] ?? '';
        $this->store_instagram = $settings['store_instagram'] ?? '';
        $this->store_tiktok = $settings['store_tiktok'] ?? '';

        $this->store_bank_name = $settings['store_bank_name'] ?? '';
        $this->store_bank_account = $settings['store_bank_account'] ?? '';
        $this->store_bank_holder = $settings['store_bank_holder'] ?? '';
        
        $this->store_footer_note = $settings['store_footer_note'] ?? '';
    }

    public function save()
    {
        $this->validate([
            'store_name' => 'required|string|max:255',
            'store_address' => 'required|string',
            'store_phone' => 'required|string|max:50',
            'store_email' => 'nullable|email|max:255',
            'store_tax_id' => 'nullable|string|max:100',
            'store_logo' => 'nullable|image|max:2048', // 2MB Max
            'login_logo' => 'nullable|image|max:2048', // 2MB Max
            'sidebar_logo' => 'nullable|image|max:2048', // 2MB Max
            'store_website' => 'nullable|url|max:255',
            'store_facebook' => 'nullable|string|max:255',
            'store_instagram' => 'nullable|string|max:255',
            'store_tiktok' => 'nullable|string|max:255',
            'store_bank_name' => 'nullable|string|max:100',
            'store_bank_account' => 'nullable|string|max:100',
            'store_bank_holder' => 'nullable|string|max:100',
            'store_footer_note' => 'nullable|string|max:500',
        ]);

        if ($this->store_logo) {
            $path = $this->store_logo->store('settings', 'public');
            Setting::set('store_logo_path', $path);
            $this->logo_url = asset('storage/' . $path);
        }

        if ($this->login_logo) {
            $path = $this->login_logo->store('settings', 'public');
            Setting::set('store_login_logo_path', $path);
            $this->login_logo_url = asset('storage/' . $path);
        }

        if ($this->sidebar_logo) {
            $path = $this->sidebar_logo->store('settings', 'public');
            Setting::set('store_sidebar_logo_path', $path);
            $this->sidebar_logo_url = asset('storage/' . $path);
        }

        Setting::set('store_name', $this->store_name);
        Setting::set('store_address', $this->store_address);
        Setting::set('store_phone', $this->store_phone);
        Setting::set('store_email', $this->store_email);
        Setting::set('store_tax_id', $this->store_tax_id);

        Setting::set('store_website', $this->store_website);
        Setting::set('store_facebook', $this->store_facebook);
        Setting::set('store_instagram', $this->store_instagram);
        Setting::set('store_tiktok', $this->store_tiktok);

        Setting::set('store_bank_name', $this->store_bank_name);
        Setting::set('store_bank_account', $this->store_bank_account);
        Setting::set('store_bank_holder', $this->store_bank_holder);
        
        Setting::set('store_footer_note', $this->store_footer_note);

        $this->success_message = 'Pengaturan toko berhasil disimpan!';
    }

    public function deleteLogo($type)
    {
        $settingKey = match($type) {
            'store' => 'store_logo_path',
            'login' => 'store_login_logo_path',
            'sidebar' => 'store_sidebar_logo_path',
            default => null
        };

        if (!$settingKey) return;

        $currentPath = Setting::get($settingKey);
        if ($currentPath) {
            Storage::disk('public')->delete($currentPath);
        }

        Setting::set($settingKey, null);

        // Reset local state
        if ($type === 'store') {
            $this->store_logo = null;
            $this->logo_url = null;
        } elseif ($type === 'login') {
            $this->login_logo = null;
            $this->login_logo_url = null;
        } elseif ($type === 'sidebar') {
            $this->sidebar_logo = null;
            $this->sidebar_logo_url = null;
        }

        $this->success_message = 'Logo berhasil hapus dan dikembalikan ke default!';
    }

    public function render()
    {
        return view('livewire.settings.store-settings');
    }
}
